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
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
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
    </body>
</html>
