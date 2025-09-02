<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckVipAccess
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

        if (!$user->isVip()) {
            // Check if it's an AJAX/API request
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần nâng cấp lên gói VIP để sử dụng tính năng này'
                ], 403);
            }

            // For regular requests, redirect to VIP upgrade page
            return redirect()->route('vip.upgrade')
                ->with('error', 'Bạn cần nâng cấp lên gói VIP để sử dụng tính năng này');
        }

        return $next($request);
    }
}
