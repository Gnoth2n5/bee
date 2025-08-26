@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Flash Messages -->
                @if (session()->has('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session()->has('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Chỉnh sửa kế hoạch bữa ăn</h1>
                        <p class="text-gray-600 mt-1">{{ $mealPlan->name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('meal-plans.show', $mealPlan) }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Quay lại
                        </a>
                    </div>
                </div>

                <!-- Edit Form -->
                <form action="{{ route('meal-plans.update', $mealPlan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tên kế hoạch
                                    </label>
                                    <input type="text" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $mealPlan->name) }}"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                           required>
                                    @error('name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="week_start" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tuần bắt đầu
                                    </label>
                                    <input type="date" 
                                           id="week_start" 
                                           name="week_start" 
                                           value="{{ old('week_start', $mealPlan->week_start->format('Y-m-d')) }}"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                                           required>
                                    @error('week_start')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status Settings -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cài đặt trạng thái</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', $mealPlan->is_active) ? 'checked' : '' }}
                                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Kế hoạch đang hoạt động
                                    </label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="weather_optimized" 
                                           name="weather_optimized" 
                                           value="1"
                                           {{ old('weather_optimized', $mealPlan->weather_optimized) ? 'checked' : '' }}
                                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                    <label for="weather_optimized" class="ml-2 block text-sm text-gray-900">
                                        Tối ưu theo thời tiết
                                    </label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="ai_suggestions_used" 
                                           name="ai_suggestions_used" 
                                           value="1"
                                           {{ old('ai_suggestions_used', $mealPlan->ai_suggestions_used) ? 'checked' : '' }}
                                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                    <label for="ai_suggestions_used" class="ml-2 block text-sm text-gray-900">
                                        Sử dụng gợi ý AI
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('meal-plans.show', $mealPlan) }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Hủy
                            </a>
                            <button type="submit" 
                                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Cập nhật kế hoạch
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
