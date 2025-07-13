@php
    use Illuminate\Support\Facades\Auth;
@endphp

<x-filament::page>
    <div class="mb-6">
        <h2 class="text-2xl font-bold flex items-center gap-2">
            <x-heroicon-o-home class="w-7 h-7 text-primary-500" />
            Xin chào, {{ Auth::user()->name }}!
        </h2>
        <p class="text-gray-500 mt-1">Chào mừng bạn đến với bảng điều khiển cá nhân. Tại đây bạn có thể quản lý các công thức nấu ăn của mình.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <x-heroicon-o-book-open class="w-10 h-10 text-primary-500 mb-2" />
            <div class="text-3xl font-bold">{{ $totalRecipes }}</div>
            <div class="text-gray-600">Tổng số công thức của bạn</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center gap-2 mb-2">
                <x-heroicon-o-star class="w-6 h-6 text-yellow-400" />
                <span class="font-semibold">Công thức mới nhất</span>
            </div>
            @if($latestRecipe)
                <div>
                    <div class="font-bold text-lg">{{ $latestRecipe->title }}</div>
                    <div class="text-gray-500 text-sm mb-1">Tạo lúc: {{ $latestRecipe->created_at->format('d/m/Y H:i') }}</div>
                    <div class="line-clamp-2 text-gray-700">{{ $latestRecipe->summary }}</div>
                    <a href="{{ route('filament.user.resources.user-recipe-resource.edit', $latestRecipe->id) }}" class="inline-block mt-2 text-primary-600 hover:underline text-sm">Chỉnh sửa</a>
                </div>
            @else
                <div class="text-gray-400">Bạn chưa có công thức nào.</div>
            @endif
        </div>
    </div>
</x-filament::page> 