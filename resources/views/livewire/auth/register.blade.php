<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-pink-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-orange-200 to-red-200 dark:from-orange-800/30 dark:to-red-800/30 rounded-full blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-red-200 to-pink-200 dark:from-red-800/30 dark:to-pink-800/30 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-gradient-to-r from-yellow-200 to-orange-200 dark:from-yellow-800/30 dark:to-orange-800/30 rounded-full blur-2xl opacity-25 animate-ping" style="animation-delay: 2s"></div>
    </div>

    <div class="h-screen flex items-center justify-center p-2 relative z-10">
        <div class="w-full max-w-6xl mx-auto h-[85vh] flex flex-col lg:flex-row shadow-2xl rounded-2xl overflow-hidden bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-slate-700/50">
            <!-- Banner trái -->
            <div class="hidden lg:flex flex-col justify-center items-center w-1/2 bg-gradient-to-br from-orange-500 via-red-500 to-pink-600 p-3 relative">
                <!-- Decorative Elements -->
                <div class="absolute top-4 left-4 w-12 h-12 bg-white/10 rounded-xl rotate-12 animate-pulse"></div>
                <div class="absolute bottom-4 right-4 w-16 h-16 bg-white/10 rounded-full animate-bounce" style="animation-delay: 1s"></div>

                <!-- Content -->
                <div class="text-center relative z-10">
                    <div class="mb-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mx-auto mb-2 shadow-lg">
                            <x-lucide-users class="w-5 h-5 text-white animate-pulse" />
                        </div>
                        <h1 class="text-xl font-black text-white mb-1 drop-shadow-lg">
                            Gia Nhập Cộng Đồng!
                        </h1>
                        <p class="text-white/90 text-xs leading-relaxed mb-3 max-w-xs mx-auto">
                            Tạo tài khoản để khám phá công thức nấu ăn
                        </p>
                    </div>

                    <!-- Features -->
                    <div class="space-y-1 mb-3">
                        <div class="flex items-center justify-center">
                            <div class="w-5 h-5 bg-white/20 rounded-lg flex items-center justify-center mr-2">
                                <x-lucide-heart class="w-3 h-3 text-white" />
                            </div>
                            <span class="text-white/90 text-xs">Lưu công thức yêu thích</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <div class="w-5 h-5 bg-white/20 rounded-lg flex items-center justify-center mr-2">
                                <x-lucide-share-2 class="w-3 h-3 text-white" />
                            </div>
                            <span class="text-white/90 text-xs">Chia sẻ sáng tạo</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <div class="w-5 h-5 bg-white/20 rounded-lg flex items-center justify-center mr-2">
                                <x-lucide-sparkles class="w-3 h-3 text-white" />
                            </div>
                            <span class="text-white/90 text-xs">AI gợi ý thông minh</span>
                        </div>
                    </div>

                    <!-- Brand -->
                    <div class="inline-flex items-center px-2 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white/90 text-xs font-medium shadow-lg">
                        <x-lucide-star class="w-3 h-3 mr-1 animate-spin" style="animation-duration: 3s" />
                        BeeFood - Cùng nhau nấu ăn
                    </div>
                </div>
            </div>
            <!-- Form phải -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-3 lg:p-4">
                <div class="w-full max-w-4xl mx-auto">
                    <!-- Logo & Header -->
                    <div class="text-center mb-3">
                        <a href="/" class="inline-block mb-2 group">
                            <div class="flex items-center justify-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <x-lucide-chef-hat class="w-4 h-4 text-white" />
                                </div>
                                <span class="text-lg font-black text-gray-800 dark:text-white">BeeFood</span>
                            </div>
                        </a>
                        
                        <div class="mb-2">
                            <h1 class="text-lg font-black text-gray-800 dark:text-white mb-1">
                                Đăng Ký
                            </h1>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                Tạo tài khoản để lưu công thức
                            </p>
                        </div>
                        
                        <!-- Badge -->
                        <div class="inline-flex items-center px-2 py-1 rounded-full bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 border border-orange-200 dark:border-orange-800 mb-3">
                            <x-lucide-user-plus class="w-3 h-3 mr-1 text-orange-500 animate-pulse" />
                            <span class="text-xs font-semibold text-orange-600 dark:text-orange-400">Tham gia miễn phí</span>
                        </div>
                    </div>

                    <form wire:submit.prevent="register" class="space-y-3">
                        @if($errors->has('general'))
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl text-sm mb-6 flex items-center">
                                <x-lucide-alert-circle class="w-4 h-4 mr-2 flex-shrink-0" />
                                {{ $errors->first('general') }}
                            </div>
                        @endif
                        
                        <!-- Google Register Button -->
                        <div class="mb-3">
                            <a href="{{ route('google.redirect') }}" class="group w-full flex items-center justify-center px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg shadow-sm bg-white/90 dark:bg-slate-700/90 backdrop-blur-sm text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-300 hover:scale-105 hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                <span class="group-hover:scale-110 transition-transform duration-300">Đăng ký bằng Google</span>
                            </a>
                        </div>
                        
                        <!-- Divider -->
                        <div class="relative mb-3">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300 dark:border-slate-600"></div>
                            </div>
                            <div class="relative flex justify-center text-xs">
                                <span class="px-2 bg-white/90 dark:bg-slate-800/90 text-gray-500 dark:text-gray-400 font-medium">hoặc</span>
                            </div>
                        </div>
                
                        <!-- Name & Email Fields - 2 Columns -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Name Field -->
                            <div class="space-y-1">
                                <label for="name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    <x-lucide-user class="w-3 h-3 inline mr-1" />
                                    Họ và tên <span class="text-red-500">*</span>
                                </label>
                                <input id="name" type="text" wire:model.defer="name" 
                                    class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white/90 dark:bg-slate-700/90 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 @error('name') border-red-300 bg-red-50 dark:bg-red-900/20 @enderror" 
                                    placeholder="Nhập họ và tên..." 
                                    autocomplete="name" required>
                                @error('name')
                                    <p class="flex items-center text-xs text-red-600 dark:text-red-400 mt-1">
                                        <x-lucide-alert-circle class="w-3 h-3 mr-1 flex-shrink-0" />
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div class="space-y-1">
                                <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    <x-lucide-mail class="w-3 h-3 inline mr-1" />
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input id="email" type="email" wire:model.defer="email" 
                                    class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white/90 dark:bg-slate-700/90 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 @error('email') border-red-300 bg-red-50 dark:bg-red-900/20 @enderror" 
                                    placeholder="Nhập email..." 
                                    autocomplete="email" required>
                                @error('email')
                                    <p class="flex items-center text-xs text-red-600 dark:text-red-400 mt-1">
                                        <x-lucide-alert-circle class="w-3 h-3 mr-1 flex-shrink-0" />
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                        <!-- Password Fields - 2 Columns -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Password Field -->
                            <div class="space-y-1">
                                <label for="password" class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    <x-lucide-lock class="w-3 h-3 inline mr-1" />
                                    Mật khẩu <span class="text-red-500">*</span>
                                </label>
                                <input id="password" type="password" wire:model.defer="password" 
                                    class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white/90 dark:bg-slate-700/90 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 @error('password') border-red-300 bg-red-50 dark:bg-red-900/20 @enderror" 
                                    placeholder="8+ ký tự, hoa, thường, số" 
                                    autocomplete="new-password" required>
                                @error('password')
                                    <p class="flex items-center text-xs text-red-600 dark:text-red-400 mt-1">
                                        <x-lucide-alert-circle class="w-3 h-3 mr-1 flex-shrink-0" />
                                        {{ $message }}
                                    </p>
                                @else
                                    <p class="flex items-center text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <x-lucide-info class="w-3 h-3 mr-1 flex-shrink-0" />
                                        8+ ký tự, hoa, thường, số
                                    </p>
                                @enderror
                            </div>

                            <!-- Password Confirmation Field -->
                            <div class="space-y-1">
                                <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    <x-lucide-shield-check class="w-3 h-3 inline mr-1" />
                                    Xác nhận mật khẩu <span class="text-red-500">*</span>
                                </label>
                                <input id="password_confirmation" type="password" wire:model.defer="password_confirmation" 
                                    class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white/90 dark:bg-slate-700/90 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 @error('password_confirmation') border-red-300 bg-red-50 dark:bg-red-900/20 @enderror" 
                                    placeholder="Nhập lại mật khẩu..." 
                                    autocomplete="new-password" required>
                                @error('password_confirmation')
                                    <p class="flex items-center text-xs text-red-600 dark:text-red-400 mt-1">
                                        <x-lucide-alert-circle class="w-3 h-3 mr-1 flex-shrink-0" />
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Register Button -->
                        <button type="submit" class="group w-full py-2.5 px-4 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-300 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center justify-center">
                            <x-lucide-loader wire:loading wire:target="register" class="animate-spin w-4 h-4 mr-2" />
                            <x-lucide-user-plus wire:loading.remove wire:target="register" class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-300" />
                            <span wire:loading.remove wire:target="register">Tạo tài khoản</span>
                            <span wire:loading wire:target="register">Đang tạo...</span>
                        </button>

                        <!-- Login Link -->
                        <div class="text-center pt-3 border-t border-gray-200 dark:border-slate-600">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                Đã có tài khoản?
                            </p>
                            <a href="{{ route('login') }}" class="group inline-flex items-center text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-semibold text-sm transition-colors">
                                <x-lucide-log-in class="w-3 h-3 mr-1 group-hover:scale-110 transition-transform duration-300" />
                                Đăng nhập ngay
                                <x-lucide-arrow-right class="w-3 h-3 ml-1 group-hover:translate-x-1 transition-transform duration-300" />
                            </a>
                        </div>

                        <!-- Terms Notice -->
                        <div class="mt-3 p-3 bg-blue-50/80 dark:bg-blue-900/20 backdrop-blur-sm rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-xs text-blue-700 dark:text-blue-400 flex items-start leading-relaxed">
                                <x-lucide-shield class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0" />
                                <span>
                                    Bằng việc đăng ký, bạn đồng ý với 
                                    <a href="#" class="underline hover:text-blue-800 dark:hover:text-blue-300 font-medium">Điều khoản</a> và 
                                    <a href="#" class="underline hover:text-blue-800 dark:hover:text-blue-300 font-medium">Chính sách</a> của BeeFood.
                                </span>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 