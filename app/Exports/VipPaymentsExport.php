<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class VipPaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $records;

    public function __construct($records = null)
    {
        $this->records = $records;
    }

    public function collection()
    {
        if ($this->records) {
            return collect($this->records);
        }
        
        return Payment::where('transfer_amount', 2000)
            ->where('status', 'completed')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Người dùng',
            'Email', 
            'Số tiền',
            'Trạng thái',
            'Ngày thanh toán',
            'Mã giao dịch',
            'Mã tham chiếu'
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->user->name ?? 'N/A',
            $payment->user->email ?? 'N/A',
            number_format($payment->transfer_amount) . ' đ',
            match($payment->status) {
                'pending' => 'Đang chờ',
                'completed' => 'Hoàn thành',
                'failed' => 'Thất bại',
                'cancelled' => 'Đã hủy',
                default => $payment->status,
            },
            $payment->created_at->format('d/m/Y H:i'),
            $payment->code ?? '',
            $payment->reference_code ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
