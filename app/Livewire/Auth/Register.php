<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.guest')]
class Register extends Component
{
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|email|unique:users,email')]
    public $email = '';

    #[Rule('required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/')]
    public $password = '';

    #[Rule('required|same:password')]
    public $password_confirmation = '';

    public $isLoading = false;

    // Location properties
    public $userLatitude = null;
    public $userLongitude = null;
    public $nearestCity = null;
    public $city = '';
    public $country = 'Vietnam';

    public function register()
    {
        $this->isLoading = true;

        try {
            $this->validate();

            $authService = app(AuthService::class);

            $user = $authService->register([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'city' => $this->city,
                'country' => $this->country,
            ]);

            // Login user
            Auth::login($user);
            session()->regenerate();

            return redirect('/')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với BeeFood.');

        } catch (\Exception $e) {
            $this->addError('general', 'Có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}