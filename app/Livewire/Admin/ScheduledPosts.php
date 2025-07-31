<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ScheduledPosts extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public function mount()
    {
        //
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function cancelScheduledPost($postId)
    {
        $post = Post::findOrFail($postId);

        // Chuyển về draft
        $post->update([
            'status' => 'draft',
            'published_at' => null
        ]);

        $this->dispatch('post-updated');
        session()->flash('message', 'Đã hủy lịch xuất bản bài viết: ' . $post->title);
    }

    public function publishNow($postId)
    {
        $post = Post::findOrFail($postId);

        // Xuất bản ngay
        $post->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        $this->dispatch('post-updated');
        session()->flash('message', 'Đã xuất bản ngay bài viết: ' . $post->title);
    }

    public function render()
    {
        $query = Post::scheduled()
            ->with('user')
            ->orderBy('published_at', 'asc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $this->search . '%');
            });
        }

        $scheduledPosts = $query->paginate(10);

        return view('livewire.admin.scheduled-posts', [
            'scheduledPosts' => $scheduledPosts
        ]);
    }
}