<?php

namespace App\Livewire\Student;

use Livewire\Component;
use App\Models\Download;
use App\Models\Paper;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DownloadHistory extends Component
{
    use WithPagination;

    // Graph data properties
    public array $popularPapersData = [];
    public array $downloadStatsData = [];
    public int $totalDownloadsThisSemester = 0;

    public function mount()
    {
        $this->prepareGraphData();
    }

    protected function prepareGraphData()
    {
        // 1. Popular Papers (This Week)
        $this->popularPapersData = Paper::query()
            ->withCount(['downloads' => function($query) {
                $query->where('created_at', '>=', now()->subWeek());
            }])
            ->having('downloads_count', '>', 0)
            ->orderByDesc('downloads_count')
            ->limit(5)
            ->get()
            ->map(function ($paper) {
                return [
                    'id' => $paper->id,
                    'title' => $paper->shortTitle(), // Assuming a method to shorten long titles
                    'downloads' => $paper->downloads_count,
                    'is_new' => $paper->created_at->gt(now()->subMonth()),
                ];
            })
            ->toArray();

        // 2. Personal Download Stats (Monthly)
        $downloads = Download::where('user_id', Auth::id())
            ->where('created_at', '>=', now()->subYear())
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->format('Y-m');
            });

        $this->downloadStatsData = [
            'labels' => $downloads->keys()->map(fn($date) => Carbon::parse($date)->format('M Y'))->toArray(),
            'data' => $downloads->map->count()->values()->toArray()
        ];

        // 3. Semester Download Count
        $this->totalDownloadsThisSemester = Download::where('user_id', Auth::id())
            ->where('created_at', '>=', now()->subMonths(4)) // Adjust semester duration as needed
            ->count();
    }

    public function render()
    {
        return view('livewire.student.download-history', [
            'downloads' => Download::where('user_id', Auth::id())
                ->with('paper')
                ->latest('downloaded_at')
                ->paginate(10),
            
            // Graph data passed to view
            'popularPapers' => $this->popularPapersData,
            'downloadStats' => $this->downloadStatsData,
            'totalSemesterDownloads' => $this->totalDownloadsThisSemester,
        ]);
    }
}