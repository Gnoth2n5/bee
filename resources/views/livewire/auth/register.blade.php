<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 via-white to-red-50">
    <div class="w-full max-w-4xl mx-auto flex flex-col lg:flex-row shadow-xl rounded-xl overflow-hidden bg-white">
        <!-- Banner trái -->
        <div class="hidden lg:flex flex-col justify-center items-center w-1/2 bg-gradient-to-br from-orange-400 to-red-400 p-10 relative">
            <img src="https://i.pinimg.com/736x/7b/18/6d/7b186df6922208e8ae765bb97a20f162.jpg" alt="BeeFood Banner" class="rounded-lg shadow-xl w-full h-64 object-cover mb-6">
            <div class="text-white text-2xl font-bold mb-2 drop-shadow-lg">Gia nhập cộng đồng BeeFood!</div>
            <div class="text-white text-base opacity-90 mb-4 text-center">Tạo tài khoản để lưu trữ, chia sẻ và khám phá hàng ngàn công thức nấu ăn hấp dẫn.</div>
            <div class="absolute bottom-4 left-0 right-0 flex justify-center">
                <span class="bg-white/20 px-3 py-1 rounded-full text-white text-xs font-medium shadow">BeeFood - Cùng nhau nấu ăn ngon mỗi ngày</span>
            </div>
        </div>
        <!-- Form phải -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-10">
            <form wire:submit.prevent="register" class="w-full max-w-sm mx-auto space-y-5">
                <div class="flex flex-col items-center mb-4">
                    <a href="/" class="mb-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center shadow">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-gray-900">BeeFood</span>
                        </div>
                    </a>
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">Đăng ký</h2>
                    <p class="text-gray-600 text-center text-base">Tạo tài khoản để lưu và chia sẻ công thức!</p>
                </div>
                @if($errors->has('general'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded text-sm mb-2">
                        {{ $errors->first('general') }}
                    </div>
                @endif
                
                <!-- Google Register Button -->
                <div class="mb-4">
                    <a href="{{ route('google.redirect') }}" class="w-full flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Đăng ký bằng Google
                    </a>
                </div>
                
                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">hoặc</span>
                    </div>
                </div>
                
                <div>
                    <label for="name" class="block mb-1 text-sm font-medium text-gray-700">Họ và tên</label>
                    <input id="name" type="text" wire:model.defer="name" class="form-input block w-full rounded-md border border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm py-2 px-3 @error('name') border-red-300 bg-red-50 @enderror" placeholder="Nhập họ và tên..." autocomplete="name" required>
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" wire:model.defer="email" class="form-input block w-full rounded-md border border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm py-2 px-3 @error('email') border-red-300 bg-red-50 @enderror" placeholder="Nhập email..." autocomplete="email" required>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Mật khẩu</label>
                    <input id="password" type="password" wire:model.defer="password" class="form-input block w-full rounded-md border border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm py-2 px-3 @error('password') border-red-300 bg-red-50 @enderror" placeholder="Tối thiểu 8 ký tự, bao gồm chữ hoa, chữ thường và số" autocomplete="new-password" required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số
                    </p>
                </div>
                <div>
                    <label for="password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">Xác nhận mật khẩu</label>
                    <input id="password_confirmation" type="password" wire:model.defer="password_confirmation" class="form-input block w-full rounded-md border border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm py-2 px-3 @error('password_confirmation') border-red-300 bg-red-50 @enderror" placeholder="Nhập lại mật khẩu..." autocomplete="new-password" required>
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-md bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold text-base shadow focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                    <svg wire:loading wire:target="register" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="register">Đăng ký</span>
                    <span wire:loading wire:target="register">Đang đăng ký...</span>
                </button>
                <div class="text-center mt-3">
                    <span class="text-sm text-gray-600">Đã có tài khoản?</span>
                    <a href="{{ route('login') }}" class="text-orange-600 hover:underline font-medium text-sm ml-1">Đăng nhập</a>
                </div>
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-700 flex items-start">
                        <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span>
                            <strong>Lưu ý:</strong> Bằng việc đăng ký, bạn đồng ý với 
                            <a href="#" class="underline hover:text-blue-800">Điều khoản sử dụng</a> và 
                            <a href="#" class="underline hover:text-blue-800">Chính sách bảo mật</a> của BeeFood.
                        </span>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div> 