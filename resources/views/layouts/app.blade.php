<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @hasSection('meta')
            @yield('meta')
        @endif

        <title>@hasSection('title') @yield('title') @else {{ config('app.name', 'Laravel') }} @endif</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Theme initialization script to prevent FOUC (Flash of Unstyled Content) -->
        <script>
            // Immediately apply theme before page renders
            (function() {
                const savedTheme = localStorage.getItem('theme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
                
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-slate-900 transition-colors duration-300">
            <livewire:layout.navigation />

            <!-- Flash Messages -->
            <x-flash-message />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot }}
                @endif
            </main>
            <x-footer />

            <!-- OpenAI Quick Chat Button (show on all pages except AI chat) -->
            {{-- @unless(request()->routeIs('openai.*'))
                <x-openai-quick-chat />
            @endunless --}}
        </div>

        <script>
        document.addEventListener('livewire:init', () => {

            // Handle flash message
            Livewire.on('flash-message', (event) => {
                const message = event.message;
                const type = event.type || 'success';
                
                // Tạo flash message element
                const flashElement = document.createElement('div');
                flashElement.className = 'fixed bottom-4 right-4 z-50';
                flashElement.innerHTML = `
                    <div class="bg-${type === 'success' ? 'green' : 'red'}-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:text-${type === 'success' ? 'green' : 'red'}-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                
                document.body.appendChild(flashElement);
                
                // Tự động xóa sau 3 giây
                setTimeout(() => {
                    if (flashElement.parentElement) {
                        flashElement.remove();
                    }
                }, 3000);
            });

            // Handle show message (for post actions)
            Livewire.on('show-message', (data) => {
                const message = data.message;
                const type = data.type || 'success';
                
                // Tạo flash message element
                const flashElement = document.createElement('div');
                flashElement.className = 'fixed bottom-4 right-4 z-50';
                flashElement.innerHTML = `
                    <div class="bg-${type === 'success' ? 'green' : type === 'warning' ? 'yellow' : 'red'}-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:text-${type === 'success' ? 'green' : type === 'warning' ? 'yellow' : 'red'}-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                
                document.body.appendChild(flashElement);
                
                // Tự động xóa sau 3 giây
                setTimeout(() => {
                    if (flashElement.parentElement) {
                        flashElement.remove();
                    }
                }, 3000);
            });

            // Handle copy to clipboard
            Livewire.on('copy-to-clipboard', (event) => {
                const url = event.url;
                navigator.clipboard.writeText(url).then(() => {
                    console.log('URL copied to clipboard:', url);
                }).catch(err => {
                    console.error('Failed to copy URL:', err);
                });
            });

        });
        </script>

		@stack('scripts')

        <!-- Ingredient Substitute Modal -->
        <x-ingredient-substitute-modal />
        
        <!-- Ingredient Substitute JavaScript -->
        <script src="{{ asset('js/ingredient-substitute.js') }}"></script>

        <!-- Scroll to Top Button -->
        <button id="scrollToTop" class="fixed bottom-6 left-6 bg-white/80 hover:bg-white text-black rounded-full p-3 shadow-lg transition-all duration-300 opacity-0 invisible z-40 backdrop-blur-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
            </svg>
        </button>

        <!-- Scroll to Top JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const scrollToTopBtn = document.getElementById('scrollToTop');
                
                // Hiển thị nút khi scroll xuống 300px
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 300) {
                        scrollToTopBtn.classList.remove('opacity-0', 'invisible');
                        scrollToTopBtn.classList.add('opacity-100', 'visible');
                    } else {
                        scrollToTopBtn.classList.add('opacity-0', 'invisible');
                        scrollToTopBtn.classList.remove('opacity-100', 'visible');
                    }
                });
                
                // Scroll to top khi click
                scrollToTopBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });
        </script>
    </body>

</html>
