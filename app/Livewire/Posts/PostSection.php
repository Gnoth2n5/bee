<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use App\Services\PostService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Polling;
use Illuminate\Support\Facades\Cache;

class PostSection extends Component
{
    public $popularPosts;
    public $latestPosts;
    public $currentSlide = 0;

    public function mount()
    {
        $this->loadPosts();
    }

    public function loadPosts()
    {
        $postService = new PostService();
        $this->popularPosts = $postService->getPopularPosts(5);
        $this->latestPosts = $postService->getLatestPosts(10);
    }

    #[On('post-created')]
    #[On('post-updated')]
    #[On('post-deleted')]
    public function refreshPosts()
    {
        $this->loadPosts();
    }

    // Polling để tự động refresh dữ liệu mỗi 30 giây
    #[Polling('30s')]
    public function pollForUpdates()
    {
        // Kiểm tra xem có event nào được trigger không
        $events = ['post-created', 'post-updated', 'post-deleted'];
        $shouldRefresh = false;
        
        foreach ($events as $event) {
            if (Cache::has("post_event_{$event}")) {
                Cache::forget("post_event_{$event}");
                $shouldRefresh = true;
            }
        }
        
        if ($shouldRefresh) {
            $this->loadPosts();
        }
    }

    public function nextSlide()
    {
        if ($this->popularPosts->count() > 0) {
            $this->currentSlide = ($this->currentSlide + 1) % $this->popularPosts->count();
        }
    }

    public function previousSlide()
    {
        if ($this->popularPosts->count() > 0) {
            $this->currentSlide = $this->currentSlide === 0
                ? $this->popularPosts->count() - 1
                : $this->currentSlide - 1;
        }
    }

    public function goToSlide($index)
    {
        $this->currentSlide = $index;
    }

    public function render()
    {
        return view('livewire.posts.post-section');
    }
}