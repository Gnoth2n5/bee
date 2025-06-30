@php
    $user = $recipe->user;
    $profile = $user->profile ?? null;
    $primaryImage = $recipe->primary_image?->image_path ?? ($recipe->images->first()->image_path ?? null);
@endphp

<div>
    <!-- Nút quay lại -->
    <div class="max-w-5xl mx-auto w-full" style="width:80%">
        <a href="{{ url()->previous() }}" class="flex items-center text-orange-500 hover:underline text-base font-medium py-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Quay lại
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden mb-8 max-w-5xl mx-auto w-full" style="width:80%">
        <!-- Header: Ảnh bìa + tên món + tác giả + lượt xem -->
        <div class="relative h-64 w-full">
            @if($primaryImage)
                <img src="{{ Storage::url($primaryImage) }}" alt="{{ $recipe->title }}" class="object-cover w-full h-full" />
            @else
                <div class="bg-gray-200 w-full h-full flex items-center justify-center text-gray-400 text-2xl">No Image</div>
            @endif
            <div class="absolute bottom-4 left-6 bg-white/80 px-6 py-3 rounded-xl flex flex-col md:flex-row md:items-center gap-2 shadow">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mr-4">{{ $recipe->title }}</h1>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    @if($profile && $profile->avatar)
                        <img src="{{ Storage::url($profile->avatar) }}" alt="{{ $user->name }}" class="w-7 h-7 rounded-full object-cover" />
                    @else
                        <span class="w-7 h-7 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold">{{ strtoupper(substr($user->name,0,1)) }}</span>
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
                <span class="text-lg font-bold text-orange-600 capitalize">{{ $recipe->difficulty == 'easy' ? 'Dễ' : ($recipe->difficulty == 'medium' ? 'Trung bình' : 'Khó') }}</span>
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
                @foreach($recipe->categories as $cat)
                    <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">{{ $cat->name }}</span>
                @endforeach
                @foreach($recipe->tags as $tag)
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
                    @foreach($recipe->ingredients as $ingredient)
                        <li class="flex justify-between items-center border-b pb-1">
                            <span class="text-gray-700">{{ $ingredient['name'] }}</span>
                            <span class="text-gray-500 text-sm">{{ $ingredient['amount'] ?? '' }} {{ $ingredient['unit'] ?? '' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- Cách làm -->
            <div class="md:col-span-2">
                <h2 class="text-lg font-semibold mb-3 text-gray-900">Cách làm</h2>
                <ol class="space-y-6 list-decimal list-inside">
                    @foreach($recipe->instructions as $step)
                        <li class="flex gap-4 items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-bold text-lg">{{ $step['step'] ?? $loop->iteration }}</div>
                            <div>
                                <div class="font-medium text-gray-800">{{ $step['instruction'] }}</div>
                                @if(!empty($step['image']))
                                    <img src="{{ Storage::url($step['image']) }}" alt="Bước {{ $step['step'] ?? $loop->iteration }}" class="w-28 h-20 object-cover rounded mt-2" />
                                @endif
                                @if(!empty($step['time']))
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
                    @if(!empty($recipe->tips))
                        <li>{{ $recipe->tips }}</li>
                    @else
                        <li>Hãy chọn nguyên liệu tươi ngon để món ăn đạt hương vị tốt nhất.</li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Nút chia sẻ, lưu, đánh giá -->
        <div class="flex flex-col md:flex-row justify-between items-center px-6 pb-6 gap-4">
            <div class="flex items-center gap-2 text-gray-600">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span>{{ $recipe->favorite_count ?? 0 }}</span>
                <span class="ml-4 flex items-center">
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    {{ number_format($recipe->average_rating, 1) }} <span class="text-xs text-gray-400 ml-1">({{ $recipe->rating_count }})</span>
                </span>
            </div>
            <div class="flex gap-2">
                <button class="bg-white border border-orange-500 text-orange-600 px-5 py-2 rounded-lg font-semibold hover:bg-orange-50 transition">Chia sẻ</button>
                <button class="bg-orange-500 text-white px-5 py-2 rounded-lg font-semibold hover:bg-orange-600 transition">Lưu công thức</button>
            </div>
        </div>
    </div>
</div> 