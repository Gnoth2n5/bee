<?php

namespace App\Filament\ManagerWidgets;

use App\Models\Recipe;
use App\Services\RecipeService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;

/**
 * Widget hiển thị danh sách công thức chờ duyệt
 * 
 * Widget này cho phép Manager:
 * - Xem nhanh các công thức chờ duyệt
 * - Thực hiện hành động duyệt/từ chối trực tiếp
 * - Theo dõi thời gian tạo và độ ưu tiên
 */
class PendingRecipes extends BaseWidget
{
    protected static ?string $heading = 'Công thức chờ duyệt';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Recipe::query()
                    ->where('status', 'pending')
                    ->where('user_id', '!=', Auth::id()) // Chỉ hiển thị công thức của người khác
                    ->with(['user'])
                    ->orderBy('created_at', 'asc') // Ưu tiên công thức cũ nhất
            )
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Ảnh')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Tác giả')
                    ->searchable(),
                Tables\Columns\TextColumn::make('difficulty')
                    ->label('Độ khó')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'easy' => 'success',
                        'medium' => 'warning',
                        'hard' => 'danger',
                        'expert' => 'purple',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Chờ từ')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('quick_approve')
                        ->label('Duyệt nhanh')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->size('sm')
                        ->requiresConfirmation()
                        ->modalHeading('Phê duyệt nhanh')
                        ->modalDescription('Phê duyệt công thức này ngay lập tức?')
                        ->modalSubmitActionLabel('Phê duyệt')
                        ->action(function (Recipe $record) {
                            app(RecipeService::class)->approve($record, Auth::user());
                        }),
                    Tables\Actions\Action::make('quick_reject')
                        ->label('Từ chối')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->size('sm')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Lý do từ chối')
                                ->required()
                                ->maxLength(500)
                                ->placeholder('Vui lòng nhập lý do từ chối...'),
                        ])
                        ->modalHeading('Từ chối công thức')
                        ->modalSubmitActionLabel('Từ chối')
                        ->action(function (Recipe $record, array $data) {
                            app(RecipeService::class)->reject($record, Auth::user(), $data['reason']);
                        }),
                    Tables\Actions\ViewAction::make('view')
                        ->label('Xem chi tiết')
                        ->icon('heroicon-o-eye')
                        ->size('sm')
                        ->url(fn(Recipe $record): string => route('filament.manager.resources.recipes.view', $record)),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve_all')
                    ->label('Duyệt tất cả')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Phê duyệt tất cả công thức đã chọn')
                    ->modalDescription('Bạn có chắc chắn muốn phê duyệt tất cả công thức đã chọn?')
                    ->action(function ($records) {
                        $service = app(RecipeService::class);
                        $user = Auth::user();
                        foreach ($records as $record) {
                            $service->approve($record, $user);
                        }
                    }),
            ])
            ->defaultPaginationPageOption(5)
            ->poll('30s') // Tự động cập nhật mỗi 30 giây
            ->emptyStateHeading('Không có công thức chờ duyệt')
            ->emptyStateDescription('Tất cả công thức đều đã được xử lý.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
