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
                        <h1 class="text-2xl font-bold text-gray-900">{{ $mealPlan->name }}</h1>
                        <p class="text-gray-600 mt-1">
                            Tuần từ {{ $mealPlan->week_start->format('d/m/Y') }} 
                            đến {{ $mealPlan->week_start->addDays(6)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <!-- Export Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Xuất dữ liệu
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                <div class="py-1">
                                    <a href="{{ route('meal-plans.export', $mealPlan) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Excel (.xlsx)
                                    </a>
                                    <a href="{{ route('meal-plans.export-csv', $mealPlan) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        CSV (.csv)
                                    </a>
                                    <a href="{{ route('meal-plans.export-pdf', $mealPlan) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        PDF (.pdf)
                                    </a>
                                    <a href="{{ route('meal-plans.export-zip', $mealPlan) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        ZIP (.zip)
                                    </a>
                                    <a href="{{ route('meal-plans.export-xml', $mealPlan) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        XML (.xml)
                                    </a>
                                    <a href="{{ route('meal-plans.export-markdown', $mealPlan) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Markdown (.md)
                                    </a>
                                    <a href="{{ route('meal-plans.export-json', $mealPlan) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        JSON (.json)
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('meal-plans.edit', $mealPlan) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Chỉnh sửa
                        </a>
                        <a href="{{ route('meal-plans.index') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Quay lại
                        </a>
                    </div>
                </div>

                <!-- Statistics -->
                @php
                    $statistics = $mealPlan->getStatistics();
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_meals'] ?? 0 }}</div>
                        <div class="text-sm text-blue-600">Tổng bữa ăn</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $statistics['completion_percentage'] ?? 0 }}%</div>
                        <div class="text-sm text-green-600">Hoàn thành</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ $statistics['total_calories'] ?? 0 }}</div>
                        <div class="text-sm text-yellow-600">Calories</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($statistics['total_cost'] ?? 0) }}đ</div>
                        <div class="text-sm text-purple-600">Chi phí ước tính</div>
                    </div>
                </div>

                <!-- Meal Plan Details -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin kế hoạch</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Trạng thái:</p>
                            <p class="font-medium">
                                @if($mealPlan->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Không hoạt động
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tối ưu thời tiết:</p>
                            <p class="font-medium">
                                @if($mealPlan->weather_optimized)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Có
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Không
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Sử dụng AI:</p>
                            <p class="font-medium">
                                @if($mealPlan->ai_suggestions_used)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Có
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Không
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Danh sách mua sắm:</p>
                            <p class="font-medium">
                                @if($mealPlan->shopping_list_generated)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Đã tạo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Chưa tạo
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Weekly Meals -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Món ăn theo tuần</h3>
                        <a href="{{ route('weekly-meals.show', $mealPlan) }}" 
                           class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Xem chi tiết
                        </a>
                    </div>
                    
                    @php
                        $weeklyMeals = app(App\Services\WeeklyMealPlanService::class)->generateWeeklyMeals($mealPlan);
                    @endphp
                    
                    @if(!empty($weeklyMeals))
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($weeklyMeals as $day => $dayData)
                                @if(!empty($dayData['meals']))
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <h4 class="font-bold text-lg text-blue-600 mb-3">{{ $dayData['day_label'] }}</h4>
                                        <div class="space-y-3">
                                            @foreach($dayData['meals'] as $mealType => $mealData)
                                                <div class="bg-white rounded-lg p-3 border">
                                                    <h5 class="font-medium text-green-600 mb-2">{{ $mealData['type_label'] }}</h5>
                                                    <div class="space-y-2">
                                                        @foreach($mealData['recipes'] as $recipe)
                                                            <div class="flex items-center justify-between p-2 bg-orange-50 rounded">
                                                                <div class="flex-1">
                                                                    <div class="font-medium text-sm">{{ $recipe['title'] }}</div>
                                                                    <div class="text-xs text-gray-500">
                                                                        @if($recipe['calories'])
                                                                            {{ $recipe['calories'] }} cal
                                                                        @endif
                                                                        @if($recipe['cooking_time'])
                                                                            • {{ $recipe['cooking_time'] }} phút
                                                                        @endif
                                                                        @if($recipe['difficulty'])
                                                                            • {{ $recipe['difficulty'] }}
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <a href="{{ route('recipes.show', $recipe['id']) }}" 
                                                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                                    Xem chi tiết
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Chưa có món ăn nào trong kế hoạch này.</p>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex space-x-3">
                    <a href="{{ route('weekly-meal-plan') }}" 
                       class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Quản lý kế hoạch
                    </a>
                    <form action="{{ route('meal-plans.destroy', $mealPlan) }}" method="POST" 
                          class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa kế hoạch này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Xóa kế hoạch
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
