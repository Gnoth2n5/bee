<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class PendingPosts extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedPosts = [];

    public function mount()
    {
        //
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function approvePost($postId)
    {
        $post = Post::findOrFail($postId);
        
        // Phê duyệt và xuất bản ngay
        $post->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        $this->dispatch('post-updated');
        session()->flash('message', 'Đã phê duyệt bài viết: ' . $post->title);
    }

    public function approveWithSchedule($postId)
    {
        $post = Post::findOrFail($postId);
        
        // Phê duyệt và đặt lịch xuất bản (ví dụ: 1 giờ sau)
        $post->update([
            'status' => 'published',
            'published_at' => now()->addHour()
        ]);

        $this->dispatch('post-updated');
        session()->flash('message', 'Đã phê duyệt và đặt lịch bài viết: ' . $post->title);
    }

    public function rejectPost($postId)
    {
        $post = Post::findOrFail($postId);
        
        // Từ chối và chuyển về draft
        $post->update([
            'status' => 'draft',
            'published_at' => null
        ]);

        $this->dispatch('post-updated');
        session()->flash('message', 'Đã từ chối bài viết: ' . $post->title);
    }

    public function approveMultiple()
    {
        if (empty($this->selectedPosts)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một bài viết');
            return;
        }

        $count = 0;
        foreach ($this->selectedPosts as $postId) {
            $post = Post::find($postId);
            if ($post) {
                $post->update([
                    'status' => 'published',
                    'published_at' => now()
                ]);
                $count++;
            }
        }

        $this->selectedPosts = [];
        $this->dispatch('post-updated');
        session()->flash('message', "Đã phê duyệt {$count} bài viết");
    }

    public function render()
    {
        $query = Post::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $pendingPosts = $query->paginate(10);

        return view('livewire.admin.pending-posts', [
            'pendingPosts' => $pendingPosts
        ]);
    }
} 