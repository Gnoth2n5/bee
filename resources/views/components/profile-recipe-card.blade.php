@props(['recipe'])

<div class="recipe-card bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group border border-orange-200/50 dark:border-orange-800/50 flex flex-col" data-recipe-slug="{{ $recipe->slug }}" data-recipe-id="{{ $recipe->id }}">
    <!-- Recipe Image -->
    <div class="aspect-[4/3] bg-gradient-to-br from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 relative overflow-hidden">
        @if($recipe->featured_image)
            <img src="{{ Storage::url($recipe->featured_image) }}" 
                 alt="{{ $recipe->title }}" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-orange-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h6" />
                    <circle cx="12" cy="12" r="10" />
                </svg>
            </div>
        @endif
        
        <!-- Status Badge -->
        @if($recipe->status === 'pending')
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 text-xs font-medium text-white bg-yellow-500 rounded-full">
                    Chờ duyệt
                </span>
            </div>
        @elseif($recipe->status === 'rejected')
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 text-xs font-medium text-white bg-red-500 rounded-full">
                    Từ chối
                </span>
            </div>
        @elseif($recipe->status === 'approved')
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 text-xs font-medium text-white bg-green-500 rounded-full">
                    Đã duyệt
                </span>
            </div>
        @endif

        <!-- Difficulty Badge -->
        <div class="absolute top-3 right-3">
            @php
                $difficultyColors = [
                    'easy' => 'bg-green-500',
                    'medium' => 'bg-yellow-500', 
                    'hard' => 'bg-red-500'
                ];
                $difficultyText = [
                    'easy' => 'Dễ',
                    'medium' => 'Trung bình',
                    'hard' => 'Khó'
                ];
            @endphp
            <span class="px-2 py-1 text-xs font-medium text-white rounded-full {{ $difficultyColors[$recipe->difficulty] ?? 'bg-gray-500' }}">
                {{ $difficultyText[$recipe->difficulty] ?? 'Không xác định' }}
            </span>
        </div>
    </div>
    
    <!-- Content -->
    <div class="flex-1 flex flex-col p-4">
        <h3 class="font-bold text-base text-gray-900 dark:text-white mb-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors line-clamp-2">
            <a href="{{ route('recipes.show', $recipe) }}">{{ $recipe->title }}</a>
        </h3>
        
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2 flex-grow">{{ $recipe->summary }}</p>
        
        <!-- Recipe Stats -->
        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-3">
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $recipe->cooking_time }} phút
            </span>
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ $recipe->servings }} người
            </span>
        </div>

        <!-- Rating and Views -->
        <div class="flex items-center justify-between text-sm mb-3">
            <div class="flex items-center space-x-1">
                <svg class="h-4 w-4 text-yellow-400" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($recipe->average_rating, 1) }}</span>
                <span class="text-xs text-gray-400">({{ $recipe->rating_count }})</span>
            </div>
            
            <div class="flex items-center text-xs text-gray-400">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ number_format($recipe->view_count) }} lượt xem
            </div>
        </div>

        <!-- Categories -->
        @if($recipe->categories->count() > 0)
            <div class="flex flex-wrap gap-1 mt-auto">
                @foreach($recipe->categories->take(2) as $category)
                    <span class="px-2 py-1 text-xs bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 text-orange-700 dark:text-orange-300 rounded-full">
                        {{ $category->name }}
                    </span>
                @endforeach
                @if($recipe->categories->count() > 2)
                    <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">
                        +{{ $recipe->categories->count() - 2 }}
                    </span>
                @endif
            </div>
        @endif

        <!-- Created Date -->
        <div class="text-xs text-gray-400 mt-2">
            Tạo {{ $recipe->created_at->diffForHumans() }}
        </div>
    </div>
</div>
