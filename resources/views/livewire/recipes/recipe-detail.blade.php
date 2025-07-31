@php
    $user = $recipe->user;
    $profile = $user->profile ?? null;
    $primaryImage = $recipe->featured_image ?? ($recipe->images->first()->image_path ?? null);
@endphp

@section('meta')
    <meta property="og:title" content="{{ $recipe->title }}" />
    <meta property="og:description" content="{{ $recipe->summary }}" />
    <meta property="og:image" content="{{ $primaryImage ? Storage::url($primaryImage) : asset('/default.jpg') }}" />
    <meta property="og:url" content="{{ request()->fullUrl() }}" />
    <meta property="og:type" content="article" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $recipe->title }}" />
    <meta name="twitter:description" content="{{ $recipe->summary }}" />
    <meta name="twitter:image" content="{{ $primaryImage ? Storage::url($primaryImage) : asset('/default.jpg') }}" />
@endsection

<div>
    <!-- Nút quay lại -->
    <div class="max-w-5xl mx-auto w-full" style="width:80%">
        <a href="{{ url()->previous() }}"
            class="flex items-center text-orange-500 hover:underline text-base font-medium py-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Quay lại
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden mb-8 max-w-5xl mx-auto w-full" style="width:80%">
        <!-- Header: Ảnh bìa + tên món + tác giả + lượt xem -->
        <div class="relative h-64 w-full">
            @if ($primaryImage)
                <img src="{{ Storage::url($primaryImage) }}" alt="{{ $recipe->title }}"
                    class="object-cover w-full h-full" />
            @else
                <div class="bg-gray-200 w-full h-full flex items-center justify-center text-gray-400 text-2xl">No Image
                </div>
            @endif

            <!-- Nút yêu thích -->
            <div class="absolute top-4 right-4">
                <button wire:click="confirmToggleFavorite" wire:loading.attr="disabled"
                    class="w-12 h-12 flex items-center justify-center bg-white/90 backdrop-blur-sm rounded-full shadow-lg hover:bg-white transition-all duration-200 group"
                    aria-label="Yêu thích">
                    @if (auth()->check() && $recipe->isFavoritedBy(auth()->user()))
                        <svg class="w-6 h-6 text-red-500 group-hover:scale-110 transition-transform" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-gray-600 group-hover:text-red-500 group-hover:scale-110 transition-all"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    @endif
                </button>
            </div>

            <div
                class="absolute bottom-4 left-6 bg-white/80 px-6 py-3 rounded-xl flex flex-col md:flex-row md:items-center gap-2 shadow">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mr-4">{{ $recipe->title }}</h1>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    @if ($user->hasAvatar())
                        <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}"
                            class="w-7 h-7 rounded-full object-cover" />
                    @else
                        <span
                            class="w-7 h-7 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                    <span class="font-medium">{{ $user->name }}</span>
                    <span class="mx-1">•</span>
                    <span>{{ $recipe->view_count }} lượt xem</span>
                </div>
            </div>
        </div>

        <!-- Info nhanh -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 px-6 py-6 border-b">
            <div class="flex flex-col items-center">
                <span class="text-lg font-bold text-orange-600">{{ $recipe->cooking_time }} phút</span>
                <span class="text-xs text-gray-500">Thời gian nấu</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-lg font-bold text-orange-600">{{ $recipe->preparation_time }} phút</span>
                <span class="text-xs text-gray-500">Chuẩn bị</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-lg font-bold text-orange-600">{{ $recipe->servings }}</span>
                <span class="text-xs text-gray-500">Khẩu phần</span>
            </div>
            <div class="flex flex-col items-center">
                <span
                    class="text-lg font-bold text-orange-600 capitalize">{{ $recipe->difficulty == 'easy' ? 'Dễ' : ($recipe->difficulty == 'medium' ? 'Trung bình' : 'Khó') }}</span>
                <span class="text-xs text-gray-500">Độ khó</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-lg font-bold text-orange-600">{{ $recipe->rating_count }}</span>
                <span class="text-xs text-gray-500">Lượt đánh giá</span>
            </div>
        </div>

        <!-- Mô tả + tags -->
        <div class="px-6 py-4 border-b">
            <div class="text-gray-700 mb-2">{{ $recipe->summary }}</div>
            <div class="flex flex-wrap gap-2">
                @foreach ($recipe->categories as $cat)
                    <span
                        class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">{{ $cat->name }}</span>
                @endforeach
                @foreach ($recipe->tags as $tag)
                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">#{{ $tag->name }}</span>
                @endforeach
            </div>
        </div>

        <!-- Nội dung chính: Nguyên liệu & Cách làm -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 px-6 py-8">
            <!-- Nguyên liệu -->
            <div class="md:col-span-1">
                <h2 class="text-lg font-semibold mb-3 text-gray-900">Nguyên liệu</h2>
                <ul class="space-y-2">
                    @foreach ($recipe->ingredients as $ingredient)
                        <li class="flex justify-between items-center border-b pb-1">
                            <span class="text-gray-700">{{ $ingredient['name'] }}</span>
                            <span class="text-gray-500 text-sm">{{ $ingredient['amount'] ?? '' }}
                                {{ $ingredient['unit'] ?? '' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- Cách làm -->
            <div class="md:col-span-2">
                <h2 class="text-lg font-semibold mb-3 text-gray-900">Cách làm</h2>
                <ol class="space-y-6 list-decimal list-inside">
                    @foreach ($recipe->instructions as $step)
                        <li class="flex gap-4 items-start">
                            <div
                                class="flex-shrink-0 w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-bold text-lg">
                                {{ $step['step'] ?? $loop->iteration }}</div>
                            <div>
                                <div class="font-medium text-gray-800">{{ $step['instruction'] }}</div>
                                @if (!empty($step['image']))
                                    <img src="{{ Storage::url($step['image']) }}"
                                        alt="Bước {{ $step['step'] ?? $loop->iteration }}"
                                        class="w-28 h-20 object-cover rounded mt-2" />
                                @endif
                                @if (!empty($step['time']))
                                    <div class="text-xs text-gray-500 mt-1">{{ $step['time'] }} phút</div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <!-- Mẹo hay, nút tương tác -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 px-6 pb-8">
            <!-- Mẹo hay -->
            <div class="bg-orange-50 rounded-lg p-4 md:col-span-2">
                <h3 class="font-semibold text-orange-700 mb-2 text-sm">Mẹo hay</h3>
                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                    @if (!empty($recipe->tips))
                        <li>{{ $recipe->tips }}</li>
                    @else
                        <li>Hãy chọn nguyên liệu tươi ngon để món ăn đạt hương vị tốt nhất.</li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Nút chia sẻ, lưu -->
        <div class="flex flex-col md:flex-row justify-between items-center px-6 pb-6 gap-4">
            <div class="flex items-center gap-2 text-gray-600">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span>{{ $recipe->favorite_count ?? 0 }}</span>
            </div>
            <div class="flex gap-2">
                <!-- Nút chia sẻ -->
                <div x-data="{ open: false }">
                    <button @click="open = true"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg font-semibold flex items-center gap-2 transition"
                        type="button">
                        <x-heroicon-o-share class="w-5 h-5" />
                        Chia sẻ
                    </button>
                </div>
                <!-- Component bộ sưu tập -->
                @livewire('recipes.recipe-collection-manager', ['recipe' => $recipe])

                <!-- Modal chia sẻ -->
                <div x-show="open" @click.away="open = false"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
                    style="display: none;">
                    <div class="bg-white rounded-xl shadow-lg p-6 w-80 relative">
                        <button @click="open = false"
                            class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <h3 class="text-lg font-bold mb-4 text-gray-900">Chia sẻ công thức</h3>
                        <div class="flex gap-4 justify-center">
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                                target="_blank" rel="noopener" title="Chia sẻ Facebook"
                                class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-3 flex items-center justify-center"
                                aria-label="Chia sẻ Facebook">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22 12c0-5.522-4.477-10-10-10S2 6.478 2 12c0 5 3.657 9.127 8.438 9.877v-6.987h-2.54v-2.89h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.242 0-1.632.771-1.632 1.562v1.875h2.773l-.443 2.89h-2.33v6.987C18.343 21.127 22 17 22 12z" />
                                </svg>
                            </a>
                            <!-- X (Twitter) -->
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($recipe->title) }}"
                                target="_blank" rel="noopener" title="Chia sẻ X"
                                class="bg-black hover:bg-gray-800 text-white rounded-full p-3 flex items-center justify-center"
                                aria-label="Chia sẻ X">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.53 6.477h2.477l-5.42 6.217 6.4 7.806h-5.03l-3.13-3.89-3.58 3.89H2.5l5.77-6.26-6.13-7.76h5.13l2.8 3.53 3.25-3.53zm-1.13 13.023h1.7l-4.7-5.73-1.7 1.93 4.7 5.73zm-7.2-13.02h-1.7l4.7 5.73 1.7-1.93-4.7-5.73z" />
                                </svg>
                            </a>
                            <!-- Zalo -->
                            <a href="https://zalo.me/share?url={{ urlencode(request()->fullUrl()) }}" target="_blank"
                                rel="noopener" title="Chia sẻ Zalo"
                                class="bg-[#0068FF] hover:bg-[#0056cc] text-white rounded-full p-3 flex items-center justify-center"
                                aria-label="Chia sẻ Zalo">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 32 32">
                                    <circle cx="16" cy="16" r="16" fill="#fff" />
                                    <path
                                        d="M16 6C10.477 6 6 10.477 6 16s4.477 10 10 10 10-4.477 10-10S21.523 6 16 6zm0 18.75A8.75 8.75 0 1 1 16 7.25a8.75 8.75 0 0 1 0 17.5z"
                                        fill="#0068FF" />
                                </svg>
                            </a>
                            <!-- Copy link -->
                            <button
                                onclick="navigator.clipboard.writeText('{{ request()->fullUrl() }}'); window.dispatchEvent(new CustomEvent('copied'))"
                                title="Sao chép link"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full p-3 flex items-center justify-center"
                                aria-label="Sao chép link">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"
                                        stroke-width="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"
                                        stroke-width="2" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Component -->
    <div class="max-w-5xl mx-auto w-full" style="width:80%">
        @livewire('recipes.recipe-rating', ['recipe' => $recipe])
    </div>
</div>
