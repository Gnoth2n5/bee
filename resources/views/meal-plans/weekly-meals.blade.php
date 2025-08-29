@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Món ăn theo tuần</h1>
                        <p class="text-gray-600 mt-1">{{ $mealPlan->name }}</p>
                        <p class="text-sm text-gray-500">
                            Tuần từ {{ $mealPlan->week_start->format('d/m/Y') }} 
                            đến {{ $mealPlan->week_start->addDays(6)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('meal-plans.export', $mealPlan) }}" 
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Xuất Excel
                        </a>
                        <a href="{{ route('weekly-meal-plan') }}" 
                           class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Quay lại
                        </a>
                        <a href="{{ route('meal-plans.index') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Danh sách kế hoạch
                        </a>
                    </div>
                </div>

                <!-- Weekly Meals Grid -->
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
                                                            <a href="{{ route('recipes.show', $recipe['slug']) }}" 
                                                               target="_blank"
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

                    <!-- Empty state if no meals -->
                    @php
                        $hasMeals = false;
                        foreach($weeklyMeals as $dayData) {
                            if (!empty($dayData['meals'])) {
                                $hasMeals = true;
                                break;
                            }
                        }
                    @endphp

                    @if(!$hasMeals)
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có món ăn nào</h3>
                            <p class="text-gray-600 mb-4">Hãy thêm món ăn vào kế hoạch bữa ăn để xem tổng quan tuần</p>
                            <a href="{{ route('weekly-meal-plan') }}" 
                               class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg transition-colors">
                                Thêm món ăn
                            </a>
                        </div>
                    @endif
                @else
                    <!-- Empty state -->
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có món ăn nào</h3>
                        <p class="text-gray-600 mb-4">Hãy thêm món ăn vào kế hoạch bữa ăn để xem tổng quan tuần</p>
                        <a href="{{ route('weekly-meal-plan') }}" 
                           class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg transition-colors">
                            Thêm món ăn
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
