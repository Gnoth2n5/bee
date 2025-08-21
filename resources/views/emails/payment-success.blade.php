<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công - Bee Food</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ff6b35;
            margin-bottom: 10px;
        }
        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .invoice-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .invoice-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .invoice-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
        .button {
            display: inline-block;
            background-color: #ff6b35;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .features {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .features h3 {
            color: #28a745;
            margin-bottom: 15px;
        }
        .features ul {
            margin: 0;
            padding-left: 20px;
        }
        .features li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🍯 Bee Food</div>
            <div class="success-icon">✅</div>
            <div class="title">Thanh toán thành công!</div>
            <p>Cảm ơn bạn đã mua gói dịch vụ của chúng tôi</p>
        </div>

        <div class="invoice-details">
            <h3>📋 Chi tiết hóa đơn</h3>
            <div class="invoice-row">
                <span class="label">Số hóa đơn:</span>
                <span class="value">{{ $invoiceData['invoice_number'] }}</span>
            </div>
            <div class="invoice-row">
                <span class="label">Mã giao dịch:</span>
                <span class="value">{{ $invoiceData['transaction_id'] }}</span>
            </div>
            <div class="invoice-row">
                <span class="label">Ngày tạo:</span>
                <span class="value">{{ $invoiceData['created_at'] }}</span>
            </div>
            <div class="invoice-row">
                <span class="label">Ngày thanh toán:</span>
                <span class="value">{{ $invoiceData['paid_at'] }}</span>
            </div>
            <div class="invoice-row">
                <span class="label">Phương thức:</span>
                <span class="value">{{ ucfirst($invoiceData['payment_method']) }}</span>
            </div>
            <div class="invoice-row">
                <span class="label">Số tiền:</span>
                <span class="value amount">{{ number_format($invoiceData['amount']) }} {{ $invoiceData['currency'] }}</span>
            </div>
        </div>

        @if($invoiceData['subscription'])
        <div class="features">
            <h3>🎁 Gói dịch vụ đã kích hoạt</h3>
            <div class="invoice-row">
                <span class="label">Loại gói:</span>
                <span class="value">{{ ucfirst($invoiceData['subscription']['type']) }}</span>
            </div>
            <div class="invoice-row">
                <span class="label">Thời hạn:</span>
                <span class="value">{{ $invoiceData['subscription']['duration'] }}</span>
            </div>
            
            <h4>✨ Tính năng bạn có thể sử dụng:</h4>
            @if($invoiceData['subscription']['type'] === 'vip')
                <ul>
                    <li>Tìm món ăn theo bản đồ nâng cao</li>
                    <li>Quảng cáo cửa hàng ưu tiên cao nhất</li>
                    <li>Hỗ trợ khách hàng 24/7</li>
                    <li>Tính năng đặc biệt dành cho VIP</li>
                    <li>Giảm giá đặc biệt</li>
                </ul>
            @elseif($invoiceData['subscription']['type'] === 'premium')
                <ul>
                    <li>Tìm món ăn theo bản đồ nâng cao</li>
                    <li>Quảng cáo cửa hàng của bạn</li>
                    <li>Ưu tiên hiển thị trong tìm kiếm</li>
                    <li>Thống kê chi tiết</li>
                </ul>
            @else
                <ul>
                    <li>Xem công thức nấu ăn cơ bản</li>
                    <li>Tìm kiếm món ăn theo địa điểm</li>
                    <li>Đánh giá và bình luận</li>
                </ul>
            @endif
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/') }}" class="button">Truy cập Bee Food</a>
        </div>

        <div class="footer">
            <p><strong>Bee Food</strong> - Nền tảng chia sẻ công thức nấu ăn</p>
            <p>Email: support@beefood.com | Hotline: 1900-xxxx</p>
            <p>© {{ date('Y') }} Bee Food. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
