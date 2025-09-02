<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModerationRuleResource\Pages;
use App\Models\ModerationRule;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class ModerationRuleResource extends Resource
{
    protected static ?string $model = ModerationRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Quản lý hệ thống';

    protected static ?string $navigationLabel = 'Quy tắc kiểm duyệt';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // return Auth::check() && $user->hasRole(['admin']);
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin quy tắc')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên quy tắc')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ví dụ: Từ ngữ không phù hợp'),

                        Forms\Components\Textarea::make('keywords')
                            ->label('Từ khóa cấm')
                            ->required()
                            ->rows(4)
                            ->placeholder('Nhập các từ khóa cấm, phân cách bằng dấu phẩy')
                            ->helperText('Phân cách nhiều từ bằng dấu phẩy'),

                        Forms\Components\Select::make('action')
                            ->label('Hành động khi vi phạm')
                            ->options([
                                'reject' => 'Từ chối tự động',
                                'flag' => 'Đánh dấu để kiểm tra thủ công',
                                'auto_approve' => 'Vẫn phê duyệt tự động',
                            ])
                            ->default('reject')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->placeholder('Mô tả về quy tắc này'),
                    ])->columns(1),

                Forms\Components\Section::make('Cấu hình')
                    ->schema([
                        Forms\Components\CheckboxList::make('fields_to_check')
                            ->label('Các trường cần kiểm tra')
                            ->options([
                                'title' => 'Tiêu đề',
                                'description' => 'Mô tả',
                                'summary' => 'Tóm tắt',
                                'ingredients' => 'Nguyên liệu',
                                'instructions' => 'Hướng dẫn',
                                'tips' => 'Mẹo',
                                'notes' => 'Ghi chú',
                            ])
                            ->default(['title', 'description', 'summary', 'ingredients', 'instructions', 'tips', 'notes'])
                            ->columns(2),

                        Forms\Components\TextInput::make('priority')
                            ->label('Độ ưu tiên')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10)
                            ->helperText('Số càng cao càng ưu tiên'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Kích hoạt quy tắc')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên quy tắc')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('keywords')
                    ->label('Từ khóa')
                    ->limit(50)
                    // ẩn từ khóa sang dạng *
                    ->formatStateUsing(function (string $state): string {
                        return str_repeat('*', strlen($state));
                    })
                    ->tooltip(function (Model $record): string {
                        return $record->keywords;
                    }),

                Tables\Columns\BadgeColumn::make('action')
                    ->label('Hành động')
                    ->colors([
                        'danger' => 'reject',
                        'warning' => 'flag',
                        'success' => 'auto_approve',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'reject' => 'Từ chối',
                            'flag' => 'Đánh dấu',
                            'auto_approve' => 'Tự động phê duyệt',
                            default => $state,
                        };
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Độ ưu tiên')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Trạng thái')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Hành động')
                    ->options([
                        'reject' => 'Từ chối',
                        'flag' => 'Đánh dấu',
                        'auto_approve' => 'Tự động phê duyệt',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái')
                    ->placeholder('Tất cả')
                    ->trueLabel('Đang hoạt động')
                    ->falseLabel('Đã tắt'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Xem')
                        ->icon('heroicon-o-eye'),

                    Tables\Actions\EditAction::make()
                        ->label('Sửa')
                        ->icon('heroicon-o-pencil'),

                    Tables\Actions\Action::make('toggle_status')
                        ->label('Bật/Tắt')
                        ->icon('heroicon-o-power')
                        ->color('warning')
                        ->action(function (ModerationRule $record) {
                            $record->update(['is_active' => !$record->is_active]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Thay đổi trạng thái quy tắc')
                        ->modalDescription(
                            fn(ModerationRule $record) =>
                            $record->is_active
                                ? 'Bạn có chắc chắn muốn tắt quy tắc này?'
                                : 'Bạn có chắc chắn muốn bật quy tắc này?'
                        )
                        ->modalSubmitActionLabel('Xác nhận'),

                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa')
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa đã chọn')
                        ->icon('heroicon-o-trash'),

                    Tables\Actions\BulkAction::make('activate_selected')
                        ->label('Bật đã chọn')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => true]);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('deactivate_selected')
                        ->label('Tắt đã chọn')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => false]);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('priority', 'desc')
            ->paginated([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModerationRules::route('/'),
            'create' => Pages\CreateModerationRule::route('/create'),
            'view' => Pages\ViewModerationRule::route('/{record}'),
            'edit' => Pages\EditModerationRule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('creator');
    }
}
