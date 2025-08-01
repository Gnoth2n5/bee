<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled posts that have reached their scheduled time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled posts to publish...');

        // Tìm các bài viết có status là published nhưng published_at trong tương lai
        // và đã đến thời gian xuất bản
        $scheduledPosts = Post::where('status', 'published')
            ->where('published_at', '<=', now())
            ->where('published_at', '>', now()->subMinutes(10)) // Chỉ xử lý các bài viết trong 10 phút gần đây
            ->get();

        if ($scheduledPosts->isEmpty()) {
            $this->info('No scheduled posts to publish at this time.');
            return 0;
        }

        $this->info("Found {$scheduledPosts->count()} scheduled posts to publish.");

        foreach ($scheduledPosts as $post) {
            try {
                // Cập nhật status để đảm bảo bài viết được hiển thị
                $post->update([
                    'status' => 'published',
                    'published_at' => $post->published_at // Giữ nguyên thời gian đã lên lịch
                ]);

                $this->info("Published: {$post->title} (ID: {$post->id})");
                Log::info("Scheduled post published: {$post->title} (ID: {$post->id})");

            } catch (\Exception $e) {
                $this->error("Failed to publish post {$post->id}: " . $e->getMessage());
                Log::error("Failed to publish scheduled post {$post->id}: " . $e->getMessage());
            }
        }

        $this->info('Scheduled posts publishing completed.');
        return 0;
    }
}