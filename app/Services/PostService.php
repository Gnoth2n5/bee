<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostService
{
    public function getPopularPosts($limit = 5)
    {
        return cache()->remember("posts.popular.{$limit}", 300, function () use ($limit) {
            return Post::popular()->limit($limit)->get();
        });
    }

    public function getLatestPosts($limit = 10)
    {
        return cache()->remember("posts.latest.{$limit}", 300, function () use ($limit) {
            return Post::latestAll()->limit($limit)->get();
        });
    }

    public function createPost($data)
    {
        $data['user_id'] = Auth::id();

        // Nếu status là published và không có published_at, set về hiện tại
        if ($data['status'] === 'published' && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Nếu có published_at trong tương lai, giữ nguyên để đặt lịch
        // Nếu published_at trong quá khứ và status là published, set về hiện tại
        if (isset($data['published_at']) && $data['status'] === 'published') {
            $publishedAt = \Carbon\Carbon::parse($data['published_at']);
            if ($publishedAt->isPast()) {
                $data['published_at'] = now();
            }
        }

        return Post::create($data);
    }

    public function updatePost($post, $data)
    {
        // Nếu status thay đổi từ draft sang published và không có published_at
        if ($data['status'] === 'published' && $post->status !== 'published' && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Nếu có published_at trong quá khứ và status là published, set về hiện tại
        if (isset($data['published_at']) && $data['status'] === 'published') {
            $publishedAt = \Carbon\Carbon::parse($data['published_at']);
            if ($publishedAt->isPast()) {
                $data['published_at'] = now();
            }
        }

        $post->update($data);
        return $post;
    }

    public function deletePost($post)
    {
        return $post->delete();
    }

    public function incrementViewCount($post)
    {
        $post->incrementViewCount();
    }
}