<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google_Client;

class GoogleLogoutMiddleware
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
        $response = $next($request);

        // Nếu user đã logout và có Google token, revoke token
        if (!Auth::check() && session()->has('google_token_revoke')) {
            $googleToken = session('google_token_revoke');

            try {
                $client = new Google_Client();
                $client->setClientId(config('services.google.client_id'));
                $client->setClientSecret(config('services.google.client_secret'));
                $client->revokeToken($googleToken);
            } catch (\Exception $e) {
                \Log::error('Google token revocation failed in middleware: ' . $e->getMessage());
            }

            session()->forget('google_token_revoke');
        }

        return $response;
    }
}
