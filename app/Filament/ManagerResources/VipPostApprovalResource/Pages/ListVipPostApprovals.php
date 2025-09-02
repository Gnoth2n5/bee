<?php

namespace App\Filament\ManagerResources\VipPostApprovalResource\Pages;

use App\Filament\ManagerResources\VipPostApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVipPostApprovals extends ListRecords
{
    protected static string $resource = VipPostApprovalResource::class;

    public function getTitle(): string
    {
        return 'Duyệt bài viết VIP';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Không có action tạo mới vì Manager chỉ được duyệt
        ];
    }
}
