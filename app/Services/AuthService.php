<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthService
{
    /**
     * Attempt to login user
     */
    public function login(string $email, string $password, bool $remember = false): bool
    {
        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            $user = Auth::user();
            
            // Update last login
            $user->update([
                'last_login_at' => now(),
                'login_count' => $user->login_count + 1
            ]);

            // Assign user role if not assigned
            if (!$user->hasRole('user') && !$user->hasRole('manager') && !$user->hasRole('admin')) {
                $user->assignRole('user');
            }

            session()->regenerate();
            return true;
        }

        return false;
    }

    /**
     * Register new user
     */
    public function register(array $data): User
    {
        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => 'active',
                'login_count' => 0
            ]);

            // Create user profile
            UserProfile::create([
                'user_id' => $user->id,
                'cooking_experience' => 'beginner',
                'dietary_preferences' => [],
                'allergies' => [],
                'health_conditions' => [],
                'timezone' => 'Asia/Ho_Chi_Minh',
                'language' => 'vi'
            ]);

            // Assign user role
            $user->assignRole('user');

            DB::commit();

            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    /**
     * Check if user can access specific feature
     */
    public function canAccess(string $permission): bool
    {
        return Auth::check() && Auth::user()->can($permission);
    }

    /**
     * Get current user with profile
     */
    public function getCurrentUser(): ?User
    {
        if (Auth::check()) {
            return Auth::user()->load('profile');
        }
        return null;
    }
} 