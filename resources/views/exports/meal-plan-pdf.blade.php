<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kế hoạch bữa ăn - {{ $mealPlan->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #FF6B35;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #FF6B35;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .stats {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #FF6B35;
        }
        .stats h3 {
            margin: 0 0 10px 0;
            color: #FF6B35;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #FF6B35;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .day-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .day-header {
            background: #FF6B35;
            color: white;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
            font-weight: bold;
            font-size: 16px;
        }
        .meal-type {
            background: #f8f9fa;
            padding: 10px 15px;
            border-left: 3px solid #FF6B35;
            margin: 10px 0;
        }
        .meal-type h4 {
            margin: 0 0 10px 0;
            color: #FF6B35;
            font-size: 14px;
        }
        .recipe-item {
            background: white;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        .recipe-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .recipe-details {
            font-size: 12px;
            color: #666;
        }
        .recipe-details span {
            margin-right: 15px;
        }
        .shopping-list {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            border-left: 4px solid #28a745;
        }
        .shopping-list h3 {
            color: #28a745;
            margin: 0 0 15px 0;
        }
        .ingredient-item {
            padding: 5px 0;
            border-bottom: 1px solid #d4edda;
        }
        .ingredient-item:last-child {
            border-bottom: none;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #666;
            font-size: 12px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Kế hoạch bữa ăn tuần</h1>
        <p><strong>{{ $mealPlan->name }}</strong></p>
        <p>Tuần từ {{ $mealPlan->week_start->format('d/m/Y') }} đến {{ $mealPlan->week_start->addDays(6)->format('d/m/Y') }}</p>
    </div>

    <div class="stats">
        <h3>Thống kê tổng quan</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['total_meals'] }}</div>
                <div class="stat-label">Tổng bữa ăn</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['unique_recipes'] }}</div>
                <div class="stat-label">Công thức duy nhất</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['completion_percentage'] }}%</div>
                <div class="stat-label">Hoàn thành</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($mealPlan->total_calories) }}</div>
                <div class="stat-label">Tổng calories</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($mealPlan->total_cost) }} VNĐ</div>
                <div class="stat-label">Tổng chi phí</div>
            </div>
        </div>
    </div>

    @foreach($weeklyMeals as $dayKey => $dayData)
    <div class="day-section">
        <div class="day-header">
            {{ $dayData['label'] }} - {{ $dayData['date']->format('d/m/Y') }}
        </div>
        
        @foreach($dayData['meals'] as $mealType => $mealData)
        <div class="meal-type">
            <h4>{{ $mealData['label'] }}</h4>
            @if(!empty($mealData['recipes']))
                @foreach($mealData['recipes'] as $recipe)
                <div class="recipe-item">
                    <div class="recipe-title">{{ $recipe->title }}</div>
                    <div class="recipe-details">
                        <span>Calories: {{ $recipe->calories_per_serving }}</span>
                        <span>Thời gian: {{ $recipe->cooking_time }} phút</span>
                        <span>Độ khó: {{ $recipe->difficulty }}</span>
                        @if(!empty($recipe->description))
                        <span>Mô tả: {{ Str::limit($recipe->description, 100) }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="recipe-item">
                    <div class="recipe-title">Chưa có món ăn</div>
                </div>
            @endif
        </div>
        @endforeach
    </div>
    @endforeach

    @if(!empty($shoppingList))
    <div class="shopping-list">
        <h3>Danh sách mua sắm</h3>
        @foreach($shoppingList as $ingredient => $details)
        <div class="ingredient-item">
            <strong>{{ $ingredient }}</strong>: {{ $details['amount'] }} {{ $details['unit'] }}
            @if(!empty($details['recipes']))
            <br><small>(Dùng cho: {{ implode(', ', $details['recipes']) }})</small>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        <p>Tạo lúc: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Hệ thống quản lý công thức nấu ăn</p>
    </div>
</body>
</html>
