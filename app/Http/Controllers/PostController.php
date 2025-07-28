<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index()
    {
        $posts = Post::published()
            ->with('user')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('posts.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->with('user')
            ->firstOrFail();

        // Tăng lượt xem
        $this->postService->incrementViewCount($post);

        // Lấy bài viết liên quan
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->with('user')
            ->orderBy('view_count', 'desc')
            ->take(4)
            ->get();

        return view('posts.show', compact('post', 'relatedPosts'));
    }
}