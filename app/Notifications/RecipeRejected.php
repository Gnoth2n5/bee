<?php

namespace App\Notifications;

use App\Models\Recipe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecipeRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $recipe;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Recipe $recipe, string $reason)
    {
        $this->recipe = $recipe;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Công thức của bạn đã bị từ chối')
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line('Công thức "' . $this->recipe->title . '" của bạn đã bị từ chối bởi hệ thống tự động.')
            ->line('Lý do từ chối:')
            ->line($this->reason)
            ->action('Xem chi tiết công thức', route('recipes.show', $this->recipe))
            ->line('Bạn có thể chỉnh sửa công thức và gửi lại để được xem xét.')
            ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'recipe_rejected',
            'recipe_id' => $this->recipe->id,
            'recipe_title' => $this->recipe->title,
            'reason' => $this->reason,
            'message' => 'Công thức "' . $this->recipe->title . '" đã bị từ chối: ' . $this->reason,
        ];
    }
} 