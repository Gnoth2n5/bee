<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class ForgotPassword extends Component
{
    public $email = '';
    public $status = '';

    public function sendResetLink()
    {
        $this->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.'
        ]);

        $status = Password::sendResetLink(['email' => $this->email]);
        $this->status = $status === Password::RESET_LINK_SENT
            ? 'Đã gửi email đặt lại mật khẩu!'
            : 'Không tìm thấy email này.';
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
} 