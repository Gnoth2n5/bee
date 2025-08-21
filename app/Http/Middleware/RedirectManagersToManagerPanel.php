<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware chuyển hướng Manager về đúng panel
 * 
 * Middleware này đảm bảo Manager luôn được chuyển hướng về Manager panel
 * thay vì Admin panel khi họ truy cập các URL admin.
 */
class RedirectManagersToManagerPanel
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Nếu user có role manager và không có role admin
            // và đang cố truy cập admin panel, chuyển hướng về manager panel
            if ($user->hasRole('manager') && !$user->hasRole('admin')) {
                if ($request->is('admin*')) {
                    return redirect('/manager');
                }
            }
        }

        return $next($request);
    }
}