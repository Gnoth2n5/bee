<x-filament-panels::page>
    {{-- Dashboard chính dành cho Manager --}}
    
    <div class="space-y-6">
        {{-- Welcome section --}}
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">
                        Chào mừng, {{ auth()->user()->name }}! 👨‍💼
                    </h2>
                    <p class="text-orange-100 mb-2">
                        Quản lý công thức và bài viết của BeeFood
                    </p>
                    <div class="text-sm text-orange-200">
                        <div class="flex items-center space-x-4">
                            <span>✅ Duyệt công thức người khác</span>
                            <span>✏️ CRUD công thức của bạn</span>
                            <span>📝 CRUD bài viết của bạn</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-orange-100">Hôm nay</div>
                    <div class="text-lg font-semibold">{{ now()->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('filament.manager.resources.recipes.index', ['tableFilters[status][value]' => 'pending']) }}" 
               class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-clock class="w-5 h-5 text-orange-600" />
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Công thức chờ duyệt</p>
                        <p class="text-xs text-gray-500">Cần xử lý ngay</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('filament.manager.resources.recipes.create') }}" 
               class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-plus class="w-5 h-5 text-green-600" />
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Tạo công thức mới</p>
                        <p class="text-xs text-gray-500">Thêm nội dung</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('filament.manager.resources.posts.index') }}" 
               class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-document-text class="w-5 h-5 text-blue-600" />
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Quản lý bài viết</p>
                        <p class="text-xs text-gray-500">CRUD bài viết</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('filament.manager.resources.posts.create') }}" 
               class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-pencil-square class="w-5 h-5 text-purple-600" />
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Viết bài mới</p>
                        <p class="text-xs text-gray-500">Tạo nội dung</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Widgets will be displayed here automatically --}}
    </div>
</x-filament-panels::page>
