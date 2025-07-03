@props(['name', 'email', 'bio', 'phone', 'address', 'city', 'country', 'cooking_experience', 'dietary_preferences', 'allergies', 'health_conditions', 'experienceOptions', 'dietaryOptions'])

<div class="max-w-4xl mx-auto">
    <form wire:submit.prevent="saveProfile" class="">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cột trái: Thông tin cơ bản + Địa chỉ -->
            <div class="space-y-6">
                <!-- Thông tin cơ bản -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                            <input wire:model.defer="name" type="text" class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input wire:model.defer="email" type="email" class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                            <input wire:model.defer="phone" type="tel" class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500">
                            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kinh nghiệm nấu ăn</label>
                            <select wire:model.defer="cooking_experience" class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500">
                                @foreach($experienceOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('cooking_experience') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giới thiệu bản thân</label>
                            <textarea wire:model.defer="bio" rows="3" class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500" placeholder="Hãy chia sẻ một chút về bản thân, sở thích nấu ăn..."></textarea>
                            @error('bio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <!-- Địa chỉ -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Địa chỉ</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thành phố</label>
                            <input wire:model.defer="city" type="text" class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500">
                            @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quốc gia</label>
                            <input wire:model.defer="country" type="text" class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500">
                            @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ chi tiết</label>
                            <textarea wire:model.defer="address" rows="2" class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500"></textarea>
                            @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cột phải: Sở thích ăn uống + Dị ứng & Sức khỏe -->
            <div class="space-y-6">
                <!-- Sở thích ăn uống -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sở thích ăn uống</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($dietaryOptions as $value => $label)
                            <label class="flex items-center">
                                <input wire:model.defer="dietary_preferences" type="checkbox" value="{{ $value }}" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('dietary_preferences') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
                </div>
                <!-- Dị ứng & Sức khỏe -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Dị ứng & Sức khỏe</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dị ứng thực phẩm</label>
                            <input wire:model.defer="allergies" type="text" class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500" placeholder="Ví dụ: đậu phộng, hải sản, sữa...">
                            @error('allergies') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tình trạng sức khỏe</label>
                            <input wire:model.defer="health_conditions" type="text" class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500" placeholder="Ví dụ: tiểu đường, huyết áp cao...">
                            @error('health_conditions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div> 