<div class="w-full max-w-md mx-auto bg-white rounded-xl shadow-md p-8 mt-10">
    <div class="flex flex-col items-center mb-6">
        <a href="/" class="mb-2">
            <x-application-logo class="w-12 h-12 rounded-full shadow" />
        </a>
        <h2 class="text-2xl font-bold text-gray-900">Đăng ký tài khoản BeeFood</h2>
        <p class="text-gray-500 text-sm mt-1">Tạo tài khoản để lưu và chia sẻ công thức!</p>
    </div>
    <form wire:submit.prevent="register" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên</label>
            <input type="text" wire:model.defer="name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:outline-none" placeholder="Nhập tên của bạn...">
            @error('name')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
        </div>
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
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
            <input type="password" wire:model.defer="password_confirmation" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:outline-none" placeholder="Nhập lại mật khẩu...">
            @error('password_confirmation')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-lg transition">Đăng ký</button>
    </form>
    <div class="text-center mt-6 text-sm text-gray-600">
        Đã có tài khoản?
        <a href="{{ route('login') }}" class="text-orange-500 hover:underline font-medium">Đăng nhập</a>
    </div>
</div> 