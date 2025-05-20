<?php

namespace App\Livewire\Admin;

use App\Models\Paper;
use App\Models\PaperVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PaperVersions extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $paper;
    public $paperId;
    public $newVersion;
    public $changeNotes;
    public $showUploadModal = false;
    public $showViewVersionModal = false;
    public $currentVersion;

    protected $listeners = ['refreshVersions' => '$refresh'];

    protected $rules = [
        'newVersion' => 'required|file|mimes:pdf|max:20480', // 20MB Max
        'changeNotes' => 'nullable|string|max:500',
    ];

    public function mount($paperId) // Keep $paperId for initial parameter, but hydrate $paper from it
    {
        $this->paperId = $paperId;
        $this->paper = Paper::find($paperId);

        if (!$this->paper) {
            session()->flash('error', 'Paper not found');
            return redirect()->route('admin.papers.index');
        }
    }

 public function render()
    {
        $versions = PaperVersion::where('paper_id', $this->paperId)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.papers.paper-versions', [
            'versions' => $versions,
            'paper' => $this->paper,
        ])->layout('components.layouts.app'); // <--- THIS IS REQUIRED FOR FULL-PAGE LIVEWIRE COMPONENTS
    }

    public function openUploadModal()
    {
        $this->reset(['newVersion', 'changeNotes']);
        $this->showUploadModal = true;
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
    }

    public function uploadNewVersion()
    {
        $this->validate();

        // Get the next version number
        $latestVersion = PaperVersion::where('paper_id', $this->paperId)
            ->orderByDesc('version_number')
            ->first();

        $nextVersionNumber = $latestVersion ? (floatval($latestVersion->version_number) + 0.1) : 1.0;
        $nextVersionNumber = number_format($nextVersionNumber, 1);

        // Store the file
        $fileName = 'paper_' . $this->paperId . '_v' . $nextVersionNumber . '_' . time() . '.pdf';
        $filePath = $this->newVersion->storeAs('papers/versions', $fileName, 'public');

        // Create the version record
        $version = PaperVersion::create([
            'paper_id' => $this->paperId,
            'file_path' => $filePath,
            'version_number' => $nextVersionNumber,
            'change_notes' => $this->changeNotes,
            'uploaded_by' => Auth::id(),
        ]);

        // Update the paper's current version
        $this->paper->update([
            'current_version_id' => $version->id,
        ]);

        $this->closeUploadModal();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'New version ' . $nextVersionNumber . ' uploaded successfully!'
        ]);
    }

    public function viewVersion(PaperVersion $version)
    {
        $this->currentVersion = $version;
        $this->showViewVersionModal = true;
    }

    public function closeViewVersionModal()
    {
        $this->showViewVersionModal = false;
        $this->currentVersion = null;
    }

    public function setAsCurrentVersion(PaperVersion $version)
    {
        $this->paper->update([
            'current_version_id' => $version->id,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Version ' . $version->version_number . ' set as current version!'
        ]);
    }

    public function deleteVersion(PaperVersion $version)
    {
        // Check if it's the only version
        $versionsCount = PaperVersion::where('paper_id', $this->paperId)->count();

        if ($versionsCount <= 1) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete the only version of this paper!'
            ]);
            return;
        }

        // Check if it's the current version
        if ($this->paper->current_version_id === $version->id) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete the current version. Set another version as current first!'
            ]);
            return;
        }

        // Delete the file
        if (Storage::disk('public')->exists($version->file_path)) {
            Storage::disk('public')->delete($version->file_path);
        }

        // Delete the record
        $versionNumber = $version->version_number;
        $version->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Version ' . $versionNumber . ' deleted successfully!'
        ]);
    }
}