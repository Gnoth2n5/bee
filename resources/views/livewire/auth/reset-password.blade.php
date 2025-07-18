<div class="w-full max-w-md mx-auto bg-white rounded-xl shadow-md p-8 mt-10">
    <div class="flex flex-col items-center mb-6">
        <a href="/" class="mb-2">
            <x-application-logo class="w-12 h-12 rounded-full shadow" />
        </a>
        <h2 class="text-2xl font-bold text-gray-900">Đặt lại mật khẩu</h2>
        <p class="text-gray-500 text-sm mt-1">Nhập mật khẩu mới cho tài khoản của bạn.</p>
    </div>
    <form wire:submit.prevent="resetPassword" class="space-y-5">
        @if($status)
            <div class="bg-green-50 text-green-700 rounded px-4 py-2 text-sm mb-2">{{ $status }}</div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" wire:model.defer="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:outline-none" placeholder="Nhập email...">
            @error('email')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
            <input type="password" wire:model.defer="password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:outline-none" placeholder="Nhập mật khẩu mới...">
            @error('password')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
            <input type="password" wire:model.defer="password_confirmation" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:outline-none" placeholder="Nhập lại mật khẩu...">
            @error('password_confirmation')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-lg transition">Đặt lại mật khẩu</button>
    </form>
    <div class="text-center mt-6 text-sm text-gray-600">
        <a href="{{ route('login') }}" class="text-orange-500 hover:underline font-medium">Quay lại đăng nhập</a>
    </div>
</div> 