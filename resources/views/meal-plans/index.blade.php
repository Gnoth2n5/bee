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
                        <h1 class="text-2xl font-bold text-gray-900">Danh sách kế hoạch bữa ăn</h1>
                        <p class="text-gray-600 mt-1">Xem tất cả kế hoạch bữa ăn đã tạo</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('meal-plans.export-all') }}" 
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Xuất Excel
                        </a>
                        <a href="{{ route('weekly-meal-plan') }}" 
                           class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                            + Tạo kế hoạch mới
                        </a>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $mealPlans->total() }}</div>
                        <div class="text-sm text-blue-600">Tổng kế hoạch</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $mealPlans->where('is_active', true)->count() }}</div>
                        <div class="text-sm text-green-600">Đang hoạt động</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ $mealPlans->where('weather_optimized', true)->count() }}</div>
                        <div class="text-sm text-yellow-600">Tối ưu thời tiết</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ $mealPlans->where('ai_suggestions_used', true)->count() }}</div>
                        <div class="text-sm text-purple-600">Có gợi ý AI</div>
                    </div>
                </div>

                <!-- Meal Plans List -->
                @if($mealPlans->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($mealPlans as $mealPlan)
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                <div class="p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $mealPlan->name }}
                                        </h3>
                                        <div class="flex space-x-1">
                                            @if($mealPlan->is_active)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Hoạt động
                                                </span>
                                            @endif
                                            @if($mealPlan->weather_optimized)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Thời tiết
                                                </span>
                                            @endif
                                            @if($mealPlan->ai_suggestions_used)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    AI
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="text-sm text-gray-600 mb-3">
                                        <div>Tuần từ: {{ $mealPlan->week_start->format('d/m/Y') }}</div>
                                        <div>Đến: {{ $mealPlan->week_start->addDays(6)->format('d/m/Y') }}</div>
                                    </div>
                                    
                                    @php
                                        $statistics = $mealPlan->getStatistics();
                                    @endphp
                                    
                                    <div class="grid grid-cols-2 gap-2 mb-3">
                                        <div class="text-center p-2 bg-blue-50 rounded">
                                            <div class="text-sm font-medium text-blue-600">{{ $statistics['total_meals'] }}</div>
                                            <div class="text-xs text-blue-500">Bữa ăn</div>
                                        </div>
                                        <div class="text-center p-2 bg-green-50 rounded">
                                            <div class="text-sm font-medium text-green-600">{{ $statistics['completion_percentage'] }}%</div>
                                            <div class="text-xs text-green-500">Hoàn thành</div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-xs text-gray-500 mb-3">
                                        <div>Tạo lúc: {{ $mealPlan->created_at->format('d/m/Y H:i') }}</div>
                                        @if($mealPlan->total_calories > 0)
                                            <div>Calories: {{ number_format($mealPlan->total_calories) }} cal</div>
                                        @endif
                                        @if($mealPlan->total_cost > 0)
                                            <div>Chi phí: {{ number_format($mealPlan->total_cost) }}đ</div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <a href="{{ route('meal-plans.show', $mealPlan) }}" 
                                           class="flex-1 bg-orange-600 hover:bg-orange-700 text-white text-center px-3 py-2 rounded-lg text-sm transition-colors">
                                            Xem chi tiết
                                        </a>
                                        <a href="{{ route('meal-plans.export', $mealPlan) }}" 
                                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm transition-colors" 
                                           title="Xuất Excel">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('meal-plans.edit', $mealPlan) }}" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('meal-plans.destroy', $mealPlan) }}" method="POST" 
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa kế hoạch này?')" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $mealPlans->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có kế hoạch bữa ăn nào</h3>
                        <p class="text-gray-600 mb-4">Tạo kế hoạch bữa ăn đầu tiên để bắt đầu lập lịch</p>
                                                 <a href="{{ route('weekly-meal-plan') }}" 
                            class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg transition-colors">
                             Tạo kế hoạch mới
                         </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
