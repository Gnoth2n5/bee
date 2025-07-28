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
        $posts = cache()->remember('posts.index.page.' . request()->get('page', 1), 300, function () {
            return Post::where('status', 'published')
                ->where('published_at', '<=', now())
                ->with('user')
                ->orderBy('published_at', 'desc')
                ->paginate(12);
        });

        return view('posts.index', compact('posts'));
    }

    public function show($slug)
    {
        // Kiểm tra bài viết có tồn tại không
        $debugPost = Post::where('slug', $slug)->first();

        if (!$debugPost) {
            abort(404, 'Bài viết không tồn tại');
        }

        // Kiểm tra trạng thái
        if ($debugPost->status !== 'published') {
            abort(404, 'Bài viết chưa được xuất bản');
        }

        // Kiểm tra thời gian xuất bản
        if (!$debugPost->published_at) {
            abort(404, 'Bài viết chưa có thời gian xuất bản');
        }

        if ($debugPost->published_at->isFuture()) {
            abort(404, 'Bài viết chưa đến thời gian xuất bản');
        }

        // Lấy bài viết với cache
        $post = cache()->remember("posts.show.{$slug}", 300, function () use ($slug) {
            return Post::where('slug', $slug)
                ->where('status', 'published')
                ->where('published_at', '<=', now())
                ->with('user')
                ->firstOrFail();
        });

        // Tăng lượt xem
        $this->postService->incrementViewCount($post);

        // Lấy bài viết liên quan
        $relatedPosts = cache()->remember("posts.related.{$slug}", 300, function () use ($post) {
            return Post::where('status', 'published')
                ->where('published_at', '<=', now())
                ->where('id', '!=', $post->id)
                ->with('user')
                ->orderBy('view_count', 'desc')
                ->take(4)
                ->get();
        });

        return view('posts.show', compact('post', 'relatedPosts'));
    }
}