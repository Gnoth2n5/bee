@php
    use Carbon\Carbon;
    use App\Models\Recipe;
    use App\Models\Favorite;
    use App\Services\WeatherService;
    use App\Services\OpenAiService;
    use Illuminate\Support\Collection;

    use Illuminate\Support\Facades\App;
@endphp

<div>
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
                            <h1 class="text-2xl font-bold text-gray-900">Kế hoạch bữa ăn hàng tuần</h1>
                            <p class="text-gray-600 mt-1">Lập kế hoạch bữa ăn cho cả tuần và xem tổng quan món ăn theo
                                tuần</p>
                        </div>

                        <div class="flex space-x-3">
                            <input type="date" wire:model.live="selectedWeek"
                                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">

                            @if ($currentMealPlan)
                                <a href="{{ route('meal-plans.export', $currentMealPlan) }}"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Xuất Excel
                                </a>
                            @endif

                            @if (!$currentMealPlan)
                                <button wire:click="createMealPlan"
                                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Tạo kế hoạch mới
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Statistics -->
                    @if ($currentMealPlan && !empty($statistics))
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_meals'] ?? 0 }}
                                </div>
                                <div class="text-sm text-blue-600">Tổng bữa ăn</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ $statistics['completion_percentage'] ?? 0 }}%</div>
                                <div class="text-sm text-green-600">Hoàn thành</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ $statistics['total_calories'] ?? 0 }}
                                </div>
                                <div class="text-sm text-yellow-600">Calories</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ number_format($statistics['total_cost'] ?? 0) }}đ</div>
                                <div class="text-sm text-purple-600">Chi phí ước tính</div>
                            </div>
                        </div>
                    @endif

                    <!-- Meal Plan Grid -->
                    @if ($currentMealPlan)
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border border-gray-300 px-4 py-2 text-left">Ngày</th>
                                        @foreach ($mealTypes as $type => $label)
                                            <th class="border border-gray-300 px-4 py-2 text-center">{{ $label }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($days as $dayKey => $dayLabel)
                                        <tr>
                                            <td class="border border-gray-300 px-4 py-2 font-medium bg-gray-50">
                                                {{ $dayLabel }}
                                            </td>
                                            @foreach ($mealTypes as $type => $label)
                                                <td class="border border-gray-300 px-4 py-2 min-h-[100px]">
                                                    @php
                                                        $meals = $this->getMealsForDay($dayKey, $type);
                                                    @endphp

                                                    @if (!empty($meals))
                                                        @foreach ($meals as $recipeId)
                                                            @php
                                                                $recipe = Recipe::find($recipeId);
                                                            @endphp
                                                            @if ($recipe)
                                                                <div
                                                                    class="flex items-center justify-between p-2 bg-orange-50 rounded mb-2">
                                                                    <div class="flex-1">
                                                                        <div class="font-medium text-sm">
                                                                            {{ $recipe->title }}</div>
                                                                        @if ($recipe->calories_per_serving)
                                                                            <div class="text-xs text-gray-500">
                                                                                {{ $recipe->calories_per_serving }} cal
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <button
                                                                        wire:click="removeMeal('{{ $dayKey }}', '{{ $type }}', {{ $recipeId }})"
                                                                        class="flex items-center justify-center text-red-500 hover:text-red-700 ml-2"
                                                                        style="height: 100%;">
                                                                        <x-heroicon-s-x-mark class="w-4 h-4" />
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    <!-- Add meal button -->
                                                    <button
                                                        onclick="openAddMealModal('{{ $dayKey }}', '{{ $type }}')"
                                                        class="w-full p-2 text-gray-400 hover:text-gray-600 border-2 border-dashed border-gray-300 hover:border-gray-400 rounded transition-colors">
                                                        <svg class="w-6 h-6 mx-auto" fill="none"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex justify-between items-center mt-6">
                            <div class="flex space-x-3">
                                <button wire:click="generateWeeklyMeals"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Tạo món ăn theo tuần
                                </button>
                            </div>

                            <div class="text-sm text-gray-500">
                                Tuần từ {{ Carbon::parse($selectedWeek)->format('d/m/Y') }}
                                đến {{ Carbon::parse($selectedWeek)->addDays(6)->format('d/m/Y') }}
                            </div>
                        </div>
                    @else
                        <!-- Empty state -->
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có kế hoạch bữa ăn</h3>
                            <p class="text-gray-600 mb-4">Tạo kế hoạch bữa ăn mới để bắt đầu lập lịch cho tuần này</p>
                            <div class="flex justify-center space-x-4">
                                <button wire:click="createMealPlan"
                                    class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg transition-colors">
                                    Tạo kế hoạch mới
                                </button>
                                <a href="{{ route('collections.index') }}"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors">
                                    Quản lý bộ sưu tập
                                </a>
                            </div>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>

    <!-- Add Meal Modal -->
    <div id="addMealModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Thêm món ăn</h3>
                <button onclick="closeAddMealModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm công thức</label>
                <input type="text" id="recipeSearch"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500"
                    placeholder="Nhập tên công thức...">
            </div>

            <div id="recipeResults" class="max-h-60 overflow-y-auto">
                <!-- Recipe results will be loaded here -->
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <button onclick="closeAddMealModal()"
                    class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Hủy
                </button>
            </div>
        </div>
    </div>



</div>

<script>
    let currentDay = '';
    let currentMealType = '';



    function openAddMealModal(day, mealType) {
        currentDay = day;
        currentMealType = mealType;
        document.getElementById('addMealModal').classList.remove('hidden');

        // Load recipes
        loadRecipes();
    }

    function closeAddMealModal() {
        document.getElementById('addMealModal').classList.add('hidden');
        document.getElementById('recipeSearch').value = '';
        document.getElementById('recipeResults').innerHTML = '';
    }

    function loadRecipes() {
        const searchQuery = document.getElementById('recipeSearch').value;

        fetch(`/api/recipes/search?q=${encodeURIComponent(searchQuery)}`)
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById('recipeResults');
                resultsDiv.innerHTML = '';

                if (data.data && data.data.length > 0) {
                    data.data.forEach(recipe => {
                        const recipeDiv = document.createElement('div');
                        recipeDiv.className =
                            'flex items-center justify-between p-3 border-b hover:bg-gray-50';
                        recipeDiv.innerHTML = `
                        <div>
                            <div class="font-medium">${recipe.title}</div>
                            <div class="text-sm text-gray-500">${recipe.description || ''}</div>
                        </div>
                        <button onclick="addRecipeToMealPlan(${recipe.id})" 
                                class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-sm">
                            Thêm
                        </button>
                    `;
                        resultsDiv.appendChild(recipeDiv);
                    });
                } else {
                    resultsDiv.innerHTML = '<p class="text-gray-500 p-3">Không tìm thấy công thức</p>';
                }
            })
            .catch(error => {
                console.error('Error loading recipes:', error);
                document.getElementById('recipeResults').innerHTML =
                    '<p class="text-red-500 p-3">Có lỗi khi tải công thức</p>';
            });
    }

    function addRecipeToMealPlan(recipeId) {
        @this.addMeal(currentDay, currentMealType, recipeId);
        closeAddMealModal();
    }

    // Search functionality
    document.getElementById('recipeSearch').addEventListener('input', function() {
        loadRecipes();
    });

    // Close modal when clicking outside
    document.getElementById('addMealModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddMealModal();
        }
    });

    // Weekly Meals Modal
    window.addEventListener('weeklyMealsGenerated', function(event) {
        const weeklyMeals = event.detail;
        console.log('Weekly meals data:', weeklyMeals);
        showWeeklyMealsModal(weeklyMeals);
    });

    function showWeeklyMealsModal(weeklyMeals) {
        console.log('Showing weekly meals modal with data:', weeklyMeals);

        // Create modal if it doesn't exist
        let modal = document.getElementById('weeklyMealsModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'weeklyMealsModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Món ăn theo tuần</h3>
                    <button onclick="closeWeeklyMealsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="weeklyMealsContent"></div>
            </div>
        `;
            document.body.appendChild(modal);
        }

        // Update content
        const content = document.getElementById('weeklyMealsContent');
        console.log('Weekly meals keys:', Object.keys(weeklyMeals));
        console.log('Weekly meals length:', Object.keys(weeklyMeals).length);

        if (!weeklyMeals || Object.keys(weeklyMeals).length === 0) {
            content.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có món ăn nào</h3>
                <p class="text-gray-600">Hãy thêm món ăn vào kế hoạch bữa ăn để xem tổng quan tuần</p>
            </div>
        `;
        } else {
            let html = '<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">';
            Object.entries(weeklyMeals).forEach(([day, dayData]) => {
                if (Object.keys(dayData.meals).length > 0) {
                    html += `
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h4 class="font-bold text-lg text-blue-600 mb-3">${dayData.day_label}</h4>
                        <div class="space-y-3">
                `;

                    Object.entries(dayData.meals).forEach(([mealType, mealData]) => {
                        html += `
                        <div class="bg-white rounded-lg p-3 border">
                            <h5 class="font-medium text-green-600 mb-2">${mealData.type_label}</h5>
                            <div class="space-y-2">
                    `;

                        mealData.recipes.forEach(recipe => {
                            html += `
                            <div class="flex items-center justify-between p-2 bg-orange-50 rounded">
                                <div class="flex-1">
                                    <div class="font-medium text-sm">${recipe.title}</div>
                                    <div class="text-xs text-gray-500">
                                        ${recipe.calories ? recipe.calories + ' cal' : ''} 
                                        ${recipe.cooking_time ? '• ' + recipe.cooking_time + ' phút' : ''}
                                        ${recipe.difficulty ? '• ' + recipe.difficulty : ''}
                                    </div>
                                </div>
                                <button onclick="viewRecipe('${recipe.slug}')" 
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Xem chi tiết
                                </button>
                            </div>
                        `;
                        });

                        html += `
                            </div>
                        </div>
                    `;
                    });

                    html += `
                        </div>
                    </div>
                `;
                }
            });
            html += '</div>';
            content.innerHTML = html;
        }

        modal.classList.remove('hidden');
    }

    function closeWeeklyMealsModal() {
        const modal = document.getElementById('weeklyMealsModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function viewRecipe(recipeSlug) {
        // Redirect to recipe detail page
        window.open(`/recipes/${recipeSlug}`, '_blank');
    }

    // Close weekly meals modal when clicking outside
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('weeklyMealsModal');
        if (modal && e.target === modal) {
            closeWeeklyMealsModal();
        }
    });
</script>
