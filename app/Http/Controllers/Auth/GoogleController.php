<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Google_Client;


class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        try {
            // Đảm bảo redirect URI được set đúng
            $redirectUrl = config('services.google.redirect');
            Log::info('Google redirect URL: ' . $redirectUrl);

            return Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Google redirect error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'email' => 'Không thể kết nối với Google. Vui lòng thử lại.',
            ]);
        }
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $redirectUrl = config('services.google.redirect');
            Log::info('Google callback URL: ' . $redirectUrl);

            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->user();

            // Tìm user đã tồn tại với google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                // Tìm user với email tương ứng
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // Cập nhật google_id cho user hiện tại
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                        'avatar' => $googleUser->getAvatar(), // Cập nhật avatar từ Google
                    ]);

                    // Đảm bảo user có role
                    if (!$user->hasRole('user') && !$user->hasRole('manager') && !$user->hasRole('admin')) {
                        $user->assignRole('user');
                    }
                } else {
                    // Tạo user mới
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                        'password' => Hash::make(Str::random(16)), // Tạo password ngẫu nhiên
                        'email_verified_at' => now(), // Email đã được verify bởi Google
                        'avatar' => $googleUser->getAvatar(),
                        'last_login_at' => now(),
                        'login_count' => 1,
                        'status' => 'active',
                    ]);

                    // Tạo user profile
                    $user->profile()->create([
                        'user_id' => $user->id,
                        'cooking_experience' => 'beginner',
                        'dietary_preferences' => [],
                        'allergies' => [],
                        'health_conditions' => [],
                        'timezone' => 'Asia/Ho_Chi_Minh',
                        'language' => 'vi'
                    ]);

                    // Gán role user
                    $user->assignRole('user');
                }
            } else {
                // Cập nhật thông tin đăng nhập
                $user->update([
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'last_login_at' => now(),
                    'login_count' => $user->login_count + 1,
                ]);

                // Đảm bảo user có role
                if (!$user->hasRole('user') && !$user->hasRole('manager') && !$user->hasRole('admin')) {
                    $user->assignRole('user');
                }
            }

            Auth::login($user);

            return redirect()->intended('/')->with('success', 'Đăng nhập thành công! Chào mừng bạn quay lại BeeFood.');

        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            Log::error('Google login error trace: ' . $e->getTraceAsString());
            return redirect()->route('login')->withErrors([
                'email' => 'Đăng nhập bằng Google thất bại. Vui lòng thử lại.',
            ]);
        }
    }

    /**
     * Logout user and revoke Google token.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->google_token) {
            try {
                // Revoke Google token
                $client = new Google_Client();
                $client->setClientId(config('services.google.client_id'));
                $client->setClientSecret(config('services.google.client_secret'));
                $client->revokeToken($user->google_token);

                // Xóa token trong database
                $user->update([
                    'google_token' => null,
                    'google_refresh_token' => null,
                ]);
            } catch (\Exception $e) {
                // Log error nhưng không dừng logout
                Log::error('Google token revocation failed: ' . $e->getMessage());
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Đăng xuất thành công!');
    }
}
