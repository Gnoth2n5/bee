<?php

namespace App\Exports;

use App\Models\WeeklyMealPlan;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class AllMealPlansExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        return WeeklyMealPlan::where('user_id', $this->user->id)
            ->orderBy('week_start', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tên kế hoạch',
            'Tuần bắt đầu',
            'Tuần kết thúc',
            'Trạng thái',
            'Tổng calories',
            'Tổng chi phí (VNĐ)',
            'Số bữa ăn',
            '% Hoàn thành',
            'Tối ưu thời tiết',
            'Sử dụng AI',
            'Đã tạo danh sách mua sắm',
            'Ngày tạo',
            'Ngày cập nhật'
        ];
    }

    public function map($mealPlan): array
    {
        $statistics = $mealPlan->getStatistics();
        
        return [
            $mealPlan->name,
            $mealPlan->week_start->format('d/m/Y'),
            $mealPlan->week_start->addDays(6)->format('d/m/Y'),
            $mealPlan->is_active ? 'Hoạt động' : 'Không hoạt động',
            number_format($mealPlan->total_calories),
            number_format($mealPlan->total_cost),
            $statistics['total_meals'],
            $statistics['completion_percentage'] . '%',
            $mealPlan->weather_optimized ? 'Có' : 'Không',
            $mealPlan->ai_suggestions_used ? 'Có' : 'Không',
            $mealPlan->shopping_list_generated ? 'Có' : 'Không',
            $mealPlan->created_at->format('d/m/Y H:i'),
            $mealPlan->updated_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style cho header
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FF6B35'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style cho dữ liệu
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle('A2:M' . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Style cho các dòng theo trạng thái
            for ($row = 2; $row <= $lastRow; $row++) {
                $isActive = $sheet->getCell('D' . $row)->getValue();
                if ($isActive === 'Hoạt động') {
                    $sheet->getStyle('A' . $row . ':M' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D4EDDA'],
                        ],
                    ]);
                }
            }
        }

        // Wrap text cho các cột có nội dung dài
        $sheet->getStyle('A:M')->getAlignment()->setWrapText(true);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // Tên kế hoạch
            'B' => 15, // Tuần bắt đầu
            'C' => 15, // Tuần kết thúc
            'D' => 15, // Trạng thái
            'E' => 15, // Tổng calories
            'F' => 20, // Tổng chi phí
            'G' => 12, // Số bữa ăn
            'H' => 15, // % Hoàn thành
            'I' => 15, // Tối ưu thời tiết
            'J' => 12, // Sử dụng AI
            'K' => 20, // Đã tạo danh sách mua sắm
            'L' => 18, // Ngày tạo
            'M' => 18, // Ngày cập nhật
        ];
    }

    public function title(): string
    {
        return 'Danh sách kế hoạch bữa ăn - ' . $this->user->name;
    }
}
