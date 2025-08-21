<!DOCTYPE html>
<html>
<head>
    <title>QR Code Test</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .qr-container { text-align: center; margin: 20px 0; }
        .qr-code { border: 1px solid #ccc; padding: 10px; display: inline-block; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>QR Code Test</h1>
    
    <div class="info">
        <h3>Thông tin test:</h3>
        <p><strong>Amount:</strong> {{ number_format($amount) }} VND</p>
        <p><strong>Transaction ID:</strong> {{ $transactionId }}</p>
        <p><strong>Message:</strong> {{ $message }}</p>
        <p><strong>Demo Mode:</strong> {{ $isDemo ? 'Yes' : 'No' }}</p>
    </div>

    <div class="qr-container">
        <h3>QR Code:</h3>
        <div class="qr-code">
            @if($qrCode)
                <img src="{{ $qrCode }}" alt="QR Code" style="width: 300px; height: 300px;">
            @else
                <p style="color: red;">Không thể tạo QR code</p>
            @endif
        </div>
    </div>

    <div class="info">
        <h3>QR Text:</h3>
        <pre>{{ $qrText }}</pre>
    </div>

    <div class="info">
        <h3>Debug Info:</h3>
        <p><strong>QR Code Length:</strong> {{ strlen($qrCode) }}</p>
        <p><strong>QR Code Starts With:</strong> {{ substr($qrCode, 0, 50) }}...</p>
    </div>

    <p><a href="{{ route('subscriptions.packages') }}">← Quay lại trang gói dịch vụ</a></p>
</body>
</html>
