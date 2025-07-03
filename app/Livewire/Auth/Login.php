<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.guest')]
class Login extends Component
{
    #[Rule('required|email')]
    public $email = '';

    #[Rule('required|min:6')]
    public $password = '';

    public $remember = false;
    public $isLoading = false;

    public function login()
    {
        $this->isLoading = true;

        try {
            $this->validate();

            $authService = app(AuthService::class);
            
            if ($authService->login($this->email, $this->password, $this->remember)) {
                return redirect()->intended('/')->with('success', 'Đăng nhập thành công! Chào mừng bạn quay lại BeeFood.');
            }

            $this->addError('email', __('auth.failed'));
            $this->addError('password', __('auth.failed'));

        } catch (\Exception $e) {
            $this->addError('general', 'Có lỗi xảy ra. Vui lòng thử lại.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
} 