<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Chỉ admin/manager mới có thể xem danh sách bài viết
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        // Chỉ admin/manager mới có thể xem chi tiết bài viết
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Chỉ admin/manager mới có thể tạo bài viết
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        // Chỉ admin/manager mới có thể cập nhật bài viết
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        // Chỉ admin/manager mới có thể xóa bài viết
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        // Chỉ admin/manager mới có thể khôi phục bài viết
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        // Chỉ admin/manager mới có thể xóa vĩnh viễn bài viết
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Post $post): bool
    {
        // Chỉ admin/manager mới có thể xuất bản bài viết
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can archive the model.
     */
    public function archive(User $user, Post $post): bool
    {
        // Chỉ admin/manager mới có thể lưu trữ bài viết
        return $user->hasRole(['admin', 'manager']);
    }
} 