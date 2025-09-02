@props(['name', 'email', 'province', 'bio', 'phone', 'address', 'city', 'country', 'cooking_experience', 'dietary_preferences', 'allergies', 'health_conditions', 'experienceOptions', 'dietaryOptions'])

<div class="max-w-4xl mx-auto">
    <form wire:submit.prevent="saveProfile" class="">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cột trái: Thông tin cơ bản + Địa chỉ -->
            <div class="space-y-6">
                <!-- Thông tin cơ bản -->
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-xl border border-orange-200/50 dark:border-orange-800/50 p-6 shadow-lg">
                    <h3 class="text-lg font-semibold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-4">Thông tin cơ bản</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                            <input wire:model.defer="name" type="text" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input wire:model.defer="email" type="email" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số điện thoại</label>
                            <input wire:model.defer="phone" type="tel" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300">
                            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kinh nghiệm nấu ăn</label>
                            <select wire:model.defer="cooking_experience" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300">
                                @foreach($experienceOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('cooking_experience') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giới thiệu bản thân</label>
                            <textarea wire:model.defer="bio" rows="3" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300" placeholder="Hãy chia sẻ một chút về bản thân, sở thích nấu ăn..."></textarea>
                            @error('bio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <!-- Địa chỉ -->
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-xl border border-orange-200/50 dark:border-orange-800/50 p-6 shadow-lg">
                    <h3 class="text-lg font-semibold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-4">Địa chỉ</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tỉnh/Thành phố</label>
                            <input wire:model.defer="province" type="text" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300" placeholder="Ví dụ: Hà Nội, TP. Hồ Chí Minh, Ninh Bình...">
                            @error('province') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Thành phố/Quận</label>
                            <input wire:model.defer="city" type="text" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300">
                            @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quốc gia</label>
                            <input wire:model.defer="country" type="text" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300">
                            @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Địa chỉ chi tiết</label>
                            <textarea wire:model.defer="address" rows="2" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300"></textarea>
                            @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cột phải: Sở thích ăn uống + Dị ứng & Sức khỏe -->
            <div class="space-y-6">
                <!-- Sở thích ăn uống -->
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-xl border border-orange-200/50 dark:border-orange-800/50 p-6 shadow-lg">
                    <h3 class="text-lg font-semibold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-4">Sở thích ăn uống</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($dietaryOptions as $value => $label)
                            <label class="flex items-center">
                                <input wire:model.defer="dietary_preferences" type="checkbox" value="{{ $value }}" class="rounded border-orange-300 dark:border-orange-600 text-orange-600 focus:ring-2 focus:ring-orange-500 dark:bg-slate-700 transition-all duration-300">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('dietary_preferences') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
                </div>
                <!-- Dị ứng & Sức khỏe -->
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-xl border border-orange-200/50 dark:border-orange-800/50 p-6 shadow-lg">
                    <h3 class="text-lg font-semibold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-4">Dị ứng & Sức khỏe</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dị ứng thực phẩm</label>
                            <input wire:model.defer="allergies" type="text" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300" placeholder="Ví dụ: đậu phộng, hải sản, sữa...">
                            @error('allergies') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tình trạng sức khỏe</label>
                            <input wire:model.defer="health_conditions" type="text" class="w-full rounded-xl border border-orange-200 dark:border-orange-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-700 dark:text-white transition-all duration-300" placeholder="Ví dụ: tiểu đường, huyết áp cao...">
                            @error('health_conditions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="mt-6 flex justify-end space-x-3">
            <button type="button" wire:click="toggleEdit" class="inline-flex items-center px-4 py-2.5 bg-white/80 dark:bg-slate-700/80 hover:bg-gray-100 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg border border-gray-300 dark:border-slate-600 shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                <x-heroicon-o-x-mark class="w-4 h-4 mr-2" />
                Hủy
            </button>
            <button type="submit" class="group inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                <span wire:loading.remove wire:target="saveProfile" class="flex items-center">
                    <x-heroicon-o-check class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-300" />
                    Lưu thay đổi
                </span>
                <span wire:loading wire:target="saveProfile" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Đang lưu...
                </span>
            </button>
        </div>
    </form>
</div> 