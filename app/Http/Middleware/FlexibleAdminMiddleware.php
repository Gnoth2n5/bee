<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlexibleAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $method  Cách kiểm tra: 'field', 'role', 'both'
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $method = 'both')
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $hasAccess = false;

        // Debug: Log thông tin user
        \Log::info('FlexibleAdminMiddleware check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_admin' => $user->is_admin,
            'method' => $method,
            'status' => $user->status
        ]);

        switch ($method) {
            case 'field':
                // Chỉ kiểm tra is_admin field
                $hasAccess = $user->is_admin;
                break;

            case 'role':
                // Chỉ kiểm tra role admin
                $hasAccess = $user->hasRole('admin');
                break;

            case 'both':
            default:
                // Kiểm tra cả field và role
                $hasAccess = $user->is_admin || $user->hasRole('admin');
                break;
        }

        if (!$hasAccess) {
            $hasAdminRole = $user->hasRole('admin');
            abort(403, "Bạn không có quyền truy cập trang này. User: {$user->email}, is_admin: " . ($user->is_admin ? 'true' : 'false') . ", has_role_admin: " . ($hasAdminRole ? 'true' : 'false') . ", method: {$method}");
        }

        return $next($request);
    }
}

