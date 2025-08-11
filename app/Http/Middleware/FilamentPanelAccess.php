<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware chung cho phân quyền các Filament panels
 * 
 * Middleware này xử lý tất cả logic phân quyền cho Admin và Manager panels:
 * - Admin: Truy cập /admin panel
 * - Manager: Truy cập /manager panel  
 * - Tự động chuyển hướng về đúng panel theo role
 * - Sử dụng hasRole() trực tiếp từ Spatie Laravel Permission
 */
class FilamentPanelAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $panel = 'admin'): Response
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            $loginRoute = $panel === 'manager'
                ? 'filament.manager.auth.login'
                : 'filament.admin.auth.login';
            return redirect()->route($loginRoute);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Kiểm tra role trực tiếp với hasRole()
        $isAdmin = $user->hasRole('admin');
        $isManager = $user->hasRole('manager');

        // Logic phân quyền theo panel được yêu cầu
        if ($panel === 'admin') {
            // Nếu Manager cố truy cập Admin panel → chuyển về Manager panel
            if ($isManager && !$isAdmin) {
                return redirect()->route('filament.manager.pages.dashboard');
            }

            // Chỉ Admin mới được vào Admin panel
            if (!$isAdmin) {
                abort(403, 'Bạn không có quyền truy cập vào Admin panel.');
            }
        } elseif ($panel === 'manager') {
            // Chỉ Manager mới được vào Manager panel
            if (!$isManager) {
                abort(403, 'Bạn không có quyền truy cập vào Manager panel.');
            }
        }

        return $next($request);
    }
}
