<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Mydnic\Volet\Models\FeedbackMessage;
use App\Models\User;

class FeedbackMessages extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $categoryFilter = '';

    public function render()
    {
        $messages = FeedbackMessage::query()
            ->when($this->statusFilter, fn($query) => $query->where('status', $this->statusFilter))
            ->when($this->categoryFilter, fn($query) => $query->where('category', $this->categoryFilter))
            ->latest()
            ->paginate(10);

        // Chart data - categories and statuses
        $categoryData = FeedbackMessage::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->category,
                    'value' => $item->count
                ];
            });

        $statusData = FeedbackMessage::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->status,
                    'value' => $item->count
                ];
            });

        return view('livewire.admin.feedback-messages', [
            'messages' => $messages,
            'categoryData' => $categoryData,
            'statusData' => $statusData,
            'categories' => FeedbackMessage::distinct()->pluck('category'),
            'statuses' => ['new', 'read', 'resolved']
        ]);
    }

    public function updateStatus($messageId, $status)
    {
        FeedbackMessage::find($messageId)->update(['status' => $status]);
        $this->dispatch('status-updated');
    }

    public function getUserName($userInfo)
    {
        if (!$userInfo) return 'Anonymous';
        
        $info = is_string($userInfo) ? json_decode($userInfo, true) : $userInfo;
        
        if (isset($info['user_id'])) {
            $user = User::find($info['user_id']);
            return $user ? $user->name : 'Unknown User';
        }
        
        return 'Anonymous';
    }

    public function getIpAddress($userInfo)
    {
        if (!$userInfo) return 'N/A';
        
        $info = is_string($userInfo) ? json_decode($userInfo, true) : $userInfo;
        
        return $info['ip'] ?? 'N/A';
    }
}