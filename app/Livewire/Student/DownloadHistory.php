<?php

namespace App\Livewire\Student;

use Livewire\Component;
use App\Models\Download;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class DownloadHistory extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.student.download-history', [
            'downloads' => Download::where('user_id', Auth::id())
                ->with('paper')
                ->latest('downloaded_at')
                ->paginate(10),
        ]);
    }
}