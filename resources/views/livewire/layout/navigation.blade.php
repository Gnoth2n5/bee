<?php

use Livewire\Volt\Component;
use App\Services\AuthService;

new class extends Component {
    public $searchQuery = '';
    public $showSearch = false;

    public function toggleSearch()
    {
        $this->showSearch = !$this->showSearch;
    }

    // Ingredient modal đã chuyển sang JavaScript thuần

    public function logout()
    {
        \Log::info('Logout method called');

        try {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            \Log::info('Logout successful');
            session()->flash('success', 'Đăng xuất thành công!');

            return $this->redirect('/', navigate: true);
        } catch (\Exception $e) {
            \Log::error('Logout error: ' . $e->getMessage());
            session()->flash('error', 'Có lỗi khi đăng xuất: ' . $e->getMessage());
        }
    }
}; ?>

<nav
    class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-sm border-b border-gray-200 dark:border-[#3E3E3A] px-4 py-3 sticky top-0 z-50">
    <div class="flex flex-wrap justify-between items-center max-w-7xl mx-auto">
        <div class="flex justify-start items-center">
            <!-- Logo -->
            <a href="{{ route('home') }}"
                class="flex items-center justify-center mr-6 hover:opacity-80 transition-opacity">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <span
                        class="self-center text-2xl font-bold whitespace-nowrap bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                        BeeFood
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-2">
                <a href="{{ route('home') }}"
                    class="text-gray-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 px-4 py-2 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('home') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 shadow-sm' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Trang chủ</span>
                    </div>
                </a>

                <a href="{{ route('recipes.index') }}"
                    class="text-gray-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 px-4 py-2 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('recipes.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 shadow-sm' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Công thức</span>
                    </div>
                </a>

                {{-- <a href="{{ route('restaurants.index') }}"
                    class="text-gray-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 px-4 py-2 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('restaurants.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 shadow-sm' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Nhà hàng</span>
                    </div>
                </a> --}}

                <button onclick="openIngredientSubstituteModal()"
                    class="text-gray-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 px-4 py-2 text-sm font-medium rounded-xl transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Thay thế nguyên liệu</span>
                    </div>
                </button>



                <!-- AI Chat - VIP Only -->
                @if (auth()->check() && auth()->user()->isVip())
                    <a href="{{ route('openai.index') }}"
                        class="text-gray-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 px-4 py-2 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('openai.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 shadow-sm' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>AI Chat</span>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white">
                                VIP
                            </span>
                        </div>
                    </a>
                @endif

                @auth
                    <!-- Tính năng Dropdown -->
                    <div class="relative group">
                        <button
                            class="text-gray-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 px-4 py-2 text-sm font-medium rounded-xl transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-800/50 flex items-center space-x-2">
                            <x-heroicon-o-funnel class="w-4 h-4" />
                            <span>Tính năng</span>
                            <x-heroicon-o-chevron-down
                                class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" />
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            class="absolute left-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <!-- Meal Plans Section -->
                                <div class="px-3 py-1">
                                    <h3
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Kế hoạch bữa ăn</h3>
                                </div>
                                <a href="{{ route('meal-plans.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <x-heroicon-o-calendar class="w-4 h-4" />
                                        <span>Danh sách kế hoạch</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white">
                                            VIP
                                        </span>
                                    </div>
                                </a>
                                <a href="{{ route('weekly-meal-plan') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <x-heroicon-o-plus class="w-4 h-4" />
                                        <span>Tạo kế hoạch mới</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white">
                                            VIP
                                        </span>
                                    </div>
                                </a>

                                <!-- Shopping Lists Section -->
                                <div class="px-3 py-1 mt-2">
                                    <h3
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Danh sách mua sắm</h3>
                                </div>
                                <a href="{{ route('shopping-lists.dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <x-heroicon-o-shopping-cart class="w-4 h-4" />
                                        <span>Dashboard</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white">
                                            VIP
                                        </span>
                                    </div>
                                </a>
                                <a href="{{ route('shopping-lists.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                                        <span>Shopping List</span>

                                        <span
                                            class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white">
                                            VIP
                                        </span>
                                    </div>
                                </a>

                                <!-- Health & Analysis Section -->
                                <div class="px-3 py-1 mt-2">
                                    <h3
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Sức khỏe & Phân tích</h3>
                                </div>
                                <a href="{{ route('disease-analysis.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span>Phân tích bệnh án</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white">
                                            VIP
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-4">
            <!-- Dark mode toggle -->
            <button onclick="toggleTheme()"
                class="group p-2.5 text-gray-500 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 rounded-xl transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:scale-110 active:scale-95"
                title="Chuyển đổi chế độ tối/sáng">
                <x-lucide-moon class="w-5 h-5 dark:hidden group-hover:rotate-12 transition-transform duration-300" />
                <x-lucide-sun
                    class="w-5 h-5 hidden dark:block group-hover:rotate-12 transition-transform duration-300" />
            </button>

            @auth
                <!-- User menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="flex text-sm rounded-xl focus:ring-4 focus:ring-orange-300 dark:focus:ring-orange-600 transition-all duration-300"
                        id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown"
                        data-dropdown-placement="bottom">
                        <span class="sr-only">Open user menu</span>
                        <div
                            class="w-8 h-8 rounded-xl bg-orange-500 flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </button>

                    <!-- Dropdown menu -->
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#161615] rounded-xl shadow-lg border border-gray-200 dark:border-[#3E3E3A] z-50"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95">
                        <div class="py-1">
                            <a href="{{ route('profile') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg mx-2 my-1 transition-colors">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Hồ sơ</span>
                                    @if (auth()->user()->isVip())
                                        <span
                                            class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white">
                                            VIP
                                        </span>
                                    @endif
                                </div>
                            </a>

                            @if (!auth()->user()->isVip())
                                <a href="{{ route('vip.upgrade') }}"
                                    class="block px-4 py-2 text-sm text-white bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 rounded-lg mx-2 my-1 transition-all duration-200 transform hover:scale-105">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span>🚀 Nâng cấp VIP</span>
                                    </div>
                                </a>
                            @endif

                            <a href="{{ route('filament.user.pages.user-dashboard') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg mx-2 my-1 transition-colors">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Quản lý công thức</span>
                                </div>
                            </a>

                            @if (auth()->user()->hasRole('admin'))
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg mx-2 my-1 transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Quản lý hệ thống (Admin)</span>
                                    </div>
                                </a>
                            @endif

                            @if (auth()->user()->hasRole('manager'))
                                <a href="{{ route('filament.manager.pages.manager-dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg mx-2 my-1 transition-colors">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span>Quản lý hệ thống (Manager)</span>
                                    </div>
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg mx-2 my-1 transition-colors"
                                    role="menuitem" tabindex="-1" id="user-menu-item-3">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Đăng xuất</span>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Guest buttons -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('login') }}"
                        class="text-gray-900 dark:text-white hover:text-orange-600 dark:hover:text-orange-400 px-4 py-2 text-sm font-medium rounded-xl transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}"
                        class="text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:ring-orange-300 dark:focus:ring-orange-600 font-medium rounded-xl text-sm px-4 py-2 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        Đăng ký
                    </a>
                </div>
            @endauth

            <!-- Mobile menu button -->
            <button data-collapse-toggle="navbar-user" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-xl md:hidden hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600"
                aria-controls="navbar-user" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="items-center justify-between hidden w-full md:hidden md:w-auto md:order-1" id="navbar-user">
        <ul
            class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
            <a href="{{ route('home') }}"
                class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 {{ request()->routeIs('home') ? 'text-orange-600' : '' }}">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Trang chủ</span>
                </div>
            </a>

            <a href="{{ route('recipes.index') }}"
                class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 {{ request()->routeIs('recipes.*') ? 'text-orange-600' : '' }}">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Công thức</span>
                </div>
            </a>

            <button onclick="openIngredientSubstituteModal()"
                class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Thay thế nguyên liệu</span>
                </div>
            </button>

            <a href="{{ route('disease-analysis.index') }}"
                class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 {{ request()->routeIs('disease-analysis.*') ? 'text-orange-600' : '' }}">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span>Phân tích bệnh án</span>
                </div>
            </a>

            @auth
                <!-- Kế hoạch bữa ăn -->
                <a href="{{ route('meal-plans.index') }}"
                    class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 {{ request()->routeIs('meal-plans.*') ? 'text-orange-600' : '' }}">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-calendar class="w-4 h-4" />
                        <span>Danh sách kế hoạch</span>
                    </div>
                </a>

                <a href="{{ route('weekly-meal-plan') }}"
                    class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 {{ request()->routeIs('weekly-meal-plan') ? 'text-orange-600' : '' }}">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        <span>Tạo kế hoạch mới</span>
                    </div>
                </a>

                <!-- Danh sách mua sắm -->
                <a href="{{ route('shopping-lists.dashboard') }}"
                    class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 {{ request()->routeIs('shopping-lists.dashboard') ? 'text-orange-600' : '' }}">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-shopping-cart class="w-4 h-4" />
                        <span>Shopping Lists Dashboard</span>
                    </div>
                </a>

                <a href="{{ route('shopping-lists.index') }}"
                    class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 {{ request()->routeIs('shopping-lists.index') ? 'text-orange-600' : '' }}">
                    <div class="flex items-center space-x-2">
                        <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                        <span>Quản lý shopping list</span>
                    </div>
                </a>

                <a href="{{ route('filament.user.pages.user-dashboard') }}"
                    class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700 {{ request()->routeIs('filament.user.*') ? 'text-orange-600' : '' }}">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Quản lý công thức</span>
                    </div>
                </a>

                @if (auth()->user()->hasRole('admin'))
                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                        class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Quản lý hệ thống (Admin)</span>
                        </div>
                    </a>
                @endif

                @if (auth()->user()->hasRole('manager'))
                    <a href="{{ route('filament.manager.pages.manager-dashboard') }}"
                        class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Quản lý hệ thống (Manager)</span>
                        </div>
                    </a>
                @endif

                <!-- VIP Package Link Mobile - Hidden -->
                {{-- <a href="{{ route('subscriptions.packages') }}"
                    class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-600 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Gói VIP</span>
                    </div>
                </a> --}}
            @endauth
        </ul>
    </div>
</nav>
