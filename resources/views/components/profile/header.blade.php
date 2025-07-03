@props(['user', 'profile', 'isEditing', 'avatar', 'experienceOptions'])

<div class="relative p-6 sm:p-8">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between">
        <div class="flex flex-col sm:flex-row sm:items-end space-y-4 sm:space-y-0 sm:space-x-6">
            <!-- Avatar -->
            <div class="relative">
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
        </div>
    </div>
</div> 