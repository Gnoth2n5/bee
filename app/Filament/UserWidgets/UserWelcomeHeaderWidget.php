<?php

namespace App\Filament\UserWidgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UserWelcomeHeaderWidget extends Widget
{
    protected static string $view = 'filament.user-widgets.user-welcome-header-widget';
    protected static ?int $sort = -1; // Sắp xếp đầu tiên nhất
    protected int|string|array $columnSpan = 'full';
}