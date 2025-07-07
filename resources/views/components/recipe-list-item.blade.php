@props(['recipe'])

<div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group border border-gray-200" data-recipe-slug="{{ $recipe->slug }}" data-recipe-id="{{ $recipe->id }}">
    <div class="flex h-28">
        <!-- Recipe Image -->
        <div class="w-28 h-full flex-shrink-0 relative overflow-hidden">
            @if($recipe->featured_image)
                <img src="{{ Storage::url($recipe->featured_image) }}" 
                     alt="{{ $recipe->title }}" 
                     class="w-full h-full object-cover rounded-l-lg" />
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-orange-100 to-red-100 rounded-l-lg">
                    <svg class="w-6 h-6 text-orange-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h6" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>
                </div>
            @endif
            
            <!-- Favorite Button -->
            <div class="absolute top-1 right-1">
                <button 
                    class="w-7 h-7 flex items-center justify-center bg-white rounded-full shadow-sm hover:bg-gray-50 transition-colors p-0"
                    wire:click="confirmToggleFavorite({{ $recipe->id }})"
                    wire:loading.attr="disabled"
                    aria-label="Yêu thích"
                >
                    @if(\Illuminate\Support\Facades\Auth::check() && $recipe->isFavoritedBy(\Illuminate\Support\Facades\Auth::user()))
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    @else
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    @endif
                </button>
            </div>
        </div>

        <!-- Recipe Content -->
        <div class="flex-1 p-3">
            <div class="flex items-start justify-between h-full">
                <div class="flex-1">
                    <!-- Title -->
                    <h3 class="font-semibold text-base text-gray-900 mb-1 group-hover:text-orange-600 transition-colors line-clamp-1">
                        <a href="{{ route('recipes.show', $recipe) }}">{{ $recipe->title }}</a>
                    </h3>

                    <!-- Summary -->
                    <p class="text-xs text-gray-600 mb-2 line-clamp-1">{{ $recipe->summary }}</p>

                    <!-- Meta Info -->
                    <div class="flex items-center space-x-3 text-xs text-gray-500 mb-2">
                        <span class="flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $recipe->cooking_time }} phút
                        </span>
                        <span class="flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ $recipe->servings }} người
                        </span>
                    </div>

                    <!-- Categories -->
                    @if($recipe->categories->count() > 0)
                        <div class="flex flex-wrap gap-1 mb-1">
                            @foreach($recipe->categories->take(2) as $category)
                                <span class="px-1.5 py-0.5 text-[10px] bg-orange-100 text-orange-700 rounded-full">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right Side Info -->
                <div class="flex flex-col items-end space-y-2 ml-3">
                    <!-- Rating -->
                    <div class="flex items-center space-x-1">
                        <svg class="h-4 w-4 text-yellow-400" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-900">{{ number_format($recipe->average_rating, 1) }}</span>
                        <span class="text-[10px] text-gray-400">({{ $recipe->rating_count }})</span>
                    </div>

                    <!-- Author -->
                    <div class="flex items-center space-x-1">
                        @if($recipe->user->profile && $recipe->user->profile->avatar)
                            <img src="{{ Storage::url($recipe->user->profile->avatar) }}" 
                                 alt="{{ $recipe->user->name }}" 
                                 class="w-5 h-5 rounded-full object-cover" />
                        @else
                            <div class="w-5 h-5 rounded-full bg-orange-100 flex items-center justify-center">
                                <span class="text-[10px] font-medium text-orange-600">
                                    {{ strtoupper(substr($recipe->user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <span class="text-xs text-gray-600">{{ $recipe->user->name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 