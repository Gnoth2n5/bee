<div class="w-full max-w-md mx-auto bg-white rounded-xl shadow-md p-8 mt-10">
    <div class="flex flex-col items-center mb-6">
        <a href="/" class="mb-2">
            <img src="/favicon.ico" alt="BeeFood" class="w-12 h-12 rounded-full shadow" />
        </a>
        <h2 class="text-2xl font-bold text-gray-900">Đăng nhập vào BeeFood</h2>
        <p class="text-gray-500 text-sm mt-1">Chào mừng bạn quay lại!</p>
    </div>
    <form wire:submit.prevent="login" class="space-y-5">
        @if($errors->has('error'))
            <div class="bg-red-50 text-red-600 rounded px-4 py-2 text-sm">{{ $errors->first('error') }}</div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" wire:model.defer="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:outline-none" placeholder="Nhập email...">
            @error('email')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
            <input type="password" wire:model.defer="password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:outline-none" placeholder="Nhập mật khẩu...">
            @error('password')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center text-sm">
                <input type="checkbox" wire:model="remember" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                <span class="ml-2">Ghi nhớ đăng nhập</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-orange-500 text-sm hover:underline">Quên mật khẩu?</a>
        </div>
        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-lg transition">Đăng nhập</button>
    </form>
    <div class="text-center mt-6 text-sm text-gray-600">
        Chưa có tài khoản?
        <a href="{{ route('register') }}" class="text-orange-500 hover:underline font-medium">Đăng ký ngay</a>
    </div>
</div> 