<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.guest')]
class ForgotPassword extends Component
{
    #[Rule('required|email', message: 'Vui lòng nhập email hợp lệ.')]
    public $email = '';

    public $isLoading = false;
    public $emailSent = false;

    public function sendResetLink()
    {
        $this->isLoading = true;

        try {
            $this->validate();

            $status = Password::sendResetLink(['email' => $this->email]);

            if ($status === Password::RESET_LINK_SENT) {
                $this->emailSent = true;
                $this->reset('email');
            } else {
                $this->addError('email', 'Không thể gửi email đặt lại mật khẩu. Vui lòng thử lại.');
            }

        } catch (\Exception $e) {
            $this->addError('general', 'Có lỗi xảy ra. Vui lòng thử lại.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
} 