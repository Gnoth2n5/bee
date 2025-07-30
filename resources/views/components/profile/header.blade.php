@props(['user', 'profile', 'isEditing', 'avatar', 'experienceOptions', 'nearestCity'])

<div class="relative p-6 sm:p-8">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between">
        <div class="flex flex-col sm:flex-row sm:items-end space-y-4 sm:space-y-0 sm:space-x-6">
            <!-- Avatar -->
            <div class="relative">
                <!-- Loading spinner khi upload avatar -->
                <div wire:loading wire:target="avatar" class="absolute inset-0 flex items-center justify-center bg-white/70 z-10 rounded-full">
                    <div role="status">
                        <svg aria-hidden="true" class="w-10 h-10 text-gray-200 animate-spin fill-orange-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        <span class="sr-only">Đang tải...</span>
                    </div>
                </div>
                <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full border-4 border-white shadow-lg overflow-hidden bg-gradient-to-br from-orange-100 to-red-100">
                    @if($isEditing && $avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @elseif($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <x-heroicon-o-user class="w-16 h-16 text-orange-400" />
                        </div>
                    @endif
                </div>
                
                @if($isEditing)
                    <label for="avatar" class="absolute bottom-0 right-0 bg-orange-500 hover:bg-orange-600 text-white rounded-full p-2 cursor-pointer shadow-lg transition-colors" title="Thay đổi ảnh đại diện">
                        <x-heroicon-o-camera class="w-5 h-5" />
                        <input wire:model.live="avatar" type="file" id="avatar" class="hidden" accept="image/*">
                    </label>
                    
                    @if($user->avatar)
                        <button wire:click="removeAvatar" type="button" class="absolute top-0 right-0 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 cursor-pointer shadow-lg transition-colors" title="Xóa ảnh đại diện">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    @endif
                    
                    @error('avatar') 
                        <div class="absolute -bottom-8 left-0 right-0 text-red-500 text-xs text-center">{{ $message }}</div>
                    @elseif($avatar)
                        <div class="absolute -bottom-8 left-0 right-0 text-green-500 text-xs text-center">
                            Ảnh đã được chọn ({{ strtoupper($avatar->getClientOriginalExtension()) }}, {{ number_format($avatar->getSize() / 1024, 1) }}KB)
                        </div>
                    @endif
                @endif
            </div>

            <!-- Basic Info -->
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $user->name }}</h1>
                    @if($user->email_verified_at)
                        <x-heroicon-o-check-badge class="w-6 h-6 text-blue-500" />
                    @endif
                </div>
                
                <!-- Thông tin cá nhân công khai -->
                <div class="mt-2 space-y-2 text-gray-700 text-base">
                    @if($user->bio)
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6 text-orange-500" />
                            <span>{{ $user->bio }}</span>
                        </div>
                    @endif
                    @if($profile->city || $profile->country)
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-map-pin class="w-6 h-6 text-orange-500" />
                            <span>{{ trim($profile->city . ($profile->city && $profile->country ? ', ' : '') . $profile->country) }}</span>
                        </div>
                    @endif
                    @if($profile->cooking_experience)
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-academic-cap class="w-6 h-6 text-orange-500" />
                            <span>Kinh nghiệm: {{ $experienceOptions[$profile->cooking_experience] ?? $profile->cooking_experience }}</span>
                        </div>
                    @endif
                    @if(is_array($profile->dietary_preferences) && count($profile->dietary_preferences))
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-sparkles class="w-6 h-6 text-orange-500" />
                            <span>Sở thích: {{ collect($profile->dietary_preferences)->map(fn($v) => ucfirst(str_replace('_',' ',$v)))->implode(', ') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            @if($isEditing)
                <button wire:click="saveProfile" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Lưu thay đổi
                </button>
                <button wire:click="toggleEdit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    Hủy
                </button>
            @else
                <button wire:click="toggleEdit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Chỉnh sửa hồ sơ
                </button>
            @endif
            
            <!-- Nút lấy vị trí -->
            @if(!$nearestCity)
                <button wire:click="getUserLocationFromBrowser" 
                        class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                    Lấy vị trí của tôi
                </button>
            @endif
        </div>
    </div>
</div> 