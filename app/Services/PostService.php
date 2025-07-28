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
            return Post::latest()->limit($limit)->get();
        });
    }

    public function createPost($data)
    {
        // Chỉ admin/manager mới có thể tạo bài viết
        if (!Auth::user()->hasRole(['admin', 'manager'])) {
            throw new \Exception('Bạn không có quyền tạo bài viết.');
        }

        $data['user_id'] = Auth::id();

        if ($data['status'] === 'published' && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        return Post::create($data);
    }

    public function updatePost($post, $data)
    {
        // Chỉ admin/manager mới có thể cập nhật bài viết
        if (!Auth::user()->hasRole(['admin', 'manager'])) {
            throw new \Exception('Bạn không có quyền cập nhật bài viết.');
        }

        if ($data['status'] === 'published' && $post->status !== 'published') {
            $data['published_at'] = now();
        }

        $post->update($data);
        return $post;
    }

    public function deletePost($post)
    {
        // Chỉ admin/manager mới có thể xóa bài viết
        if (!Auth::user()->hasRole(['admin', 'manager'])) {
            throw new \Exception('Bạn không có quyền xóa bài viết.');
        }

        return $post->delete();
    }

    public function incrementViewCount($post)
    {
        $post->incrementViewCount();
    }
}