<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách kế hoạch bữa ăn - {{ $user->name }}</title>
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
        .summary-stats {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #FF6B35;
        }
        .summary-stats h3 {
            margin: 0 0 15px 0;
            color: #FF6B35;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #FF6B35;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
        }
        .meal-plan-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .meal-plan-header {
            background: #FF6B35;
            color: white;
            padding: 15px 20px;
            font-weight: bold;
            font-size: 16px;
        }
        .meal-plan-content {
            padding: 20px;
        }
        .meal-plan-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        .meal-plan-stat {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .meal-plan-stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #FF6B35;
        }
        .meal-plan-stat-label {
            font-size: 12px;
            color: #666;
        }
        .meal-plan-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }
        .detail-group {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .detail-group h4 {
            margin: 0 0 10px 0;
            color: #FF6B35;
            font-size: 14px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #333;
        }
        .detail-value {
            color: #666;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        .features {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .feature-badge {
            background: #e9ecef;
            color: #495057;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        .feature-badge.active {
            background: #d4edda;
            color: #155724;
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
        <h1>Danh sách kế hoạch bữa ăn</h1>
        <p><strong>Người dùng: {{ $user->name }}</strong></p>
        <p>Tạo lúc: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary-stats">
        <h3>Thống kê tổng quan</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $mealPlansData->count() }}</div>
                <div class="stat-label">Tổng kế hoạch</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $mealPlansData->where('mealPlan.is_active', true)->count() }}</div>
                <div class="stat-label">Kế hoạch hoạt động</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($mealPlansData->sum('mealPlan.total_calories')) }}</div>
                <div class="stat-label">Tổng calories</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($mealPlansData->sum('mealPlan.total_cost')) }} VNĐ</div>
                <div class="stat-label">Tổng chi phí</div>
            </div>
        </div>
    </div>

    @foreach($mealPlansData as $index => $data)
    <div class="meal-plan-item">
        <div class="meal-plan-header">
            {{ $data['mealPlan']->name }} - Tuần từ {{ $data['mealPlan']->week_start->format('d/m/Y') }}
        </div>
        
        <div class="meal-plan-content">
            <div class="meal-plan-stats">
                <div class="meal-plan-stat">
                    <div class="meal-plan-stat-value">{{ $data['statistics']['total_meals'] }}</div>
                    <div class="meal-plan-stat-label">Tổng bữa ăn</div>
                </div>
                <div class="meal-plan-stat">
                    <div class="meal-plan-stat-value">{{ $data['statistics']['unique_recipes'] }}</div>
                    <div class="meal-plan-stat-label">Công thức duy nhất</div>
                </div>
                <div class="meal-plan-stat">
                    <div class="meal-plan-stat-value">{{ $data['statistics']['completion_percentage'] }}%</div>
                    <div class="meal-plan-stat-label">Hoàn thành</div>
                </div>
                <div class="meal-plan-stat">
                    <div class="meal-plan-stat-value">{{ number_format($data['mealPlan']->total_calories) }}</div>
                    <div class="meal-plan-stat-label">Calories</div>
                </div>
                <div class="meal-plan-stat">
                    <div class="meal-plan-stat-value">{{ number_format($data['mealPlan']->total_cost) }} VNĐ</div>
                    <div class="meal-plan-stat-label">Chi phí</div>
                </div>
            </div>

            <div class="meal-plan-details">
                <div class="detail-group">
                    <h4>Thông tin cơ bản</h4>
                    <div class="detail-item">
                        <span class="detail-label">Trạng thái:</span>
                        <span class="detail-value {{ $data['mealPlan']->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $data['mealPlan']->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tuần kết thúc:</span>
                        <span class="detail-value">{{ $data['mealPlan']->week_start->addDays(6)->format('d/m/Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tạo lúc:</span>
                        <span class="detail-value">{{ $data['mealPlan']->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Cập nhật lúc:</span>
                        <span class="detail-value">{{ $data['mealPlan']->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="detail-group">
                    <h4>Tính năng sử dụng</h4>
                    <div class="features">
                        <span class="feature-badge {{ $data['mealPlan']->weather_optimized ? 'active' : '' }}">
                            {{ $data['mealPlan']->weather_optimized ? '✓' : '✗' }} Tối ưu thời tiết
                        </span>
                        <span class="feature-badge {{ $data['mealPlan']->ai_suggestions_used ? 'active' : '' }}">
                            {{ $data['mealPlan']->ai_suggestions_used ? '✓' : '✗' }} Sử dụng AI
                        </span>
                        <span class="feature-badge {{ $data['mealPlan']->shopping_list_generated ? 'active' : '' }}">
                            {{ $data['mealPlan']->shopping_list_generated ? '✓' : '✗' }} Danh sách mua sắm
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="footer">
        <p>Hệ thống quản lý công thức nấu ăn</p>
        <p>Trang {{ $index + 1 }} / {{ $mealPlansData->count() }}</p>
    </div>
</body>
</html>
