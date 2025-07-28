@php
    use Illuminate\Support\Facades\Auth;
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold flex items-center gap-2">
        <x-heroicon-o-home class="w-7 h-7 text-primary-500" />
        Xin chào, {{ Auth::user()->name }}!
    </h2>
    <p class="text-gray-500 mt-1">Chào mừng bạn đến với bảng điều khiển cá nhân. Tại đây bạn có thể quản lý các công thức nấu ăn và bài viết của mình.</p>
</div> 