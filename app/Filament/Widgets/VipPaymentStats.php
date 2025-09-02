<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VipPaymentStats extends BaseWidget
{
    protected function getStats(): array
    {
        $vipPayments = Payment::where('transfer_amount', 2000)->where('status', 'completed');
        
        $totalRevenue = $vipPayments->sum('transfer_amount');
        $totalTransactions = $vipPayments->count();
        
        return [
            Stat::make('Tổng doanh thu', number_format($totalRevenue) . ' đ')
                ->description('Từ giao dịch VIP')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make('Số giao dịch', $totalTransactions)
                ->description('Giao dịch VIP hoàn thành')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
        ];
    }
}
