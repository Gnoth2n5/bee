<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        // Clear cache và emit event
        $this->clearCache();
        $this->clearPostSpecificCache($post);
        $this->emitPostEvent('post-created');
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        // Clear cache và emit event
        $this->clearCache();
        $this->clearPostSpecificCache($post);
        $this->emitPostEvent('post-updated');
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        // Clear cache và emit event
        $this->clearCache();
        $this->clearPostSpecificCache($post);
        $this->emitPostEvent('post-deleted');
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        // Clear cache và emit event
        $this->clearCache();
        $this->emitPostEvent('post-updated');
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        // Clear cache và emit event
        $this->clearCache();
        $this->emitPostEvent('post-deleted');
    }

    /**
     * Clear cache related to posts
     */
    private function clearCache(): void
    {
        // Clear all post-related cache
        Cache::forget('posts.popular.5');
        Cache::forget('posts.latest.10');

        // Clear pagination cache for posts index
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("posts.index.page.{$i}");
        }

        // Clear all posts show cache
        Cache::flush();
    }

    /**
     * Clear cache specific to a post
     */
    private function clearPostSpecificCache(Post $post): void
    {
        // Clear specific post cache
        Cache::forget("posts.show.{$post->slug}");
        Cache::forget("posts.related.{$post->slug}");
    }

    /**
     * Emit Livewire event
     */
    private function emitPostEvent(string $event): void
    {
        // Set a cache key to trigger updates
        Cache::put("post_event_{$event}", now()->timestamp, 60);
    }
}