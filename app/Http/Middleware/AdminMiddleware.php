<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Debug: Log thông tin user
        \Log::info('AdminMiddleware check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_admin' => $user->is_admin,
            'status' => $user->status
        ]);

        // Kiểm tra quyền admin (cả is_admin field và role)
        $hasAdminRole = $user->hasRole('admin');
        $hasAdminField = $user->is_admin;

        if (!$hasAdminField && !$hasAdminRole) {
            abort(403, 'Bạn không có quyền truy cập trang này. User: ' . $user->email . ', is_admin: ' . ($user->is_admin ? 'true' : 'false') . ', has_role_admin: ' . ($hasAdminRole ? 'true' : 'false'));
        }

        return $next($request);
    }
}
