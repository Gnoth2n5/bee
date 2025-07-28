<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use App\Services\PostService;
use Livewire\Component;

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