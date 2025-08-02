<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Quản lý người dùng';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('province')
                            ->label('Tỉnh/Thành phố')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('password')
                            ->label('Mật khẩu')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'active' => 'Hoạt động',
                                'inactive' => 'Không hoạt động',
                                'banned' => 'Bị cấm',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Vai trò và quyền')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Vai trò')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable(),
                    ]),

                Forms\Components\Section::make('Thông tin bổ sung')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->imageEditor()
                            ->circleCropper()
                            ->disk('public')
                            ->visibility('public')
                            ->helperText('Nếu user đăng nhập bằng Google, avatar sẽ được lấy từ Google. Upload file mới sẽ ghi đè avatar Google.'),
                        Forms\Components\Textarea::make('bio')
                            ->label('Tiểu sử')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Xác thực email lúc')
                            ->disabled(),                            
                        Forms\Components\DateTimePicker::make('last_login_at')
                            ->label('Đăng nhập cuối lúc')
                            ->disabled(),
                        Forms\Components\TextInput::make('login_count')
                            ->label('Số lần đăng nhập')
                            ->numeric()
                            ->disabled()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(function ($record) {
                        return $record->getAvatarUrl();
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province')
                    ->label('Tỉnh/Thành phố')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Vai trò')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'banned' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Xác thực email')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Đăng nhập cuối')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('login_count')
                    ->label('Số lần đăng nhập')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipes_count')
                    ->label('Số công thức')
                    ->counts('recipes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('favorites_count')
                    ->label('Số yêu thích')
                    ->counts('favorites')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'active' => 'Hoạt động',
                        'inactive' => 'Không hoạt động',
                        'banned' => 'Bị cấm',
                    ]),
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Vai trò')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\Filter::make('email_verified')
                    ->label('Đã xác thực email')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('has_recipes')
                    ->label('Có công thức')
                    ->query(fn(Builder $query): Builder => $query->has('recipes')),
                Tables\Filters\Filter::make('has_favorites')
                    ->label('Có yêu thích')
                    ->query(fn(Builder $query): Builder => $query->has('favorites')),
                Tables\Filters\Filter::make('recent_users')
                    ->label('Người dùng mới (7 ngày)')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7))),
                Tables\Filters\Filter::make('admin_users')
                    ->label('Chỉ hiển thị Admin')
                    ->query(fn(Builder $query): Builder => $query->whereHas('roles', fn($q) => $q->where('name', 'admin'))),
                Tables\Filters\Filter::make('non_admin_users')
                    ->label('Chỉ hiển thị không phải Admin')
                    ->query(fn(Builder $query): Builder => $query->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('ban')
                        ->label('Cấm người dùng')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn(User $record) => $record->update(['status' => 'banned']))
                        ->visible(fn(User $record) => $record->status !== 'banned' && !$record->hasRole('admin')),
                    Tables\Actions\Action::make('activate')
                        ->label('Kích hoạt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn(User $record) => $record->update(['status' => 'active']))
                        ->visible(fn(User $record) => $record->status !== 'active'),
                    Tables\Actions\Action::make('view_recipes')
                        ->label('Xem công thức')
                        ->icon('heroicon-o-cake')
                        ->color('info')
                        ->url(fn(User $record): string => route('filament.admin.resources.recipes.index', ['tableFilters[user][value]' => $record->id]))
                        ->visible(fn(User $record) => $record->recipes_count > 0),
                    Tables\Actions\Action::make('view_profile')
                        ->label('Xem profile')
                        ->icon('heroicon-o-user')
                        ->color('secondary')
                        ->url(fn(User $record): string => route('filament.admin.resources.users.edit', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa người dùng')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn(User $record) => !$record->hasRole('admin'))
                        ->modalHeading('Xác nhận xóa người dùng')
                        ->modalDescription('Bạn có chắc chắn muốn xóa người dùng này? Hành động này không thể hoàn tác.')
                        ->modalSubmitActionLabel('Xóa người dùng'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('ban')
                        ->label('Cấm người dùng')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update(['status' => 'banned']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Kích hoạt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn(Collection $records) => $records->each->update(['status' => 'active']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa người dùng')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Xác nhận xóa người dùng')
                        ->modalDescription('Bạn có chắc chắn muốn xóa những người dùng đã chọn? Hành động này không thể hoàn tác.')
                        ->modalSubmitActionLabel('Xóa người dùng')
                        ->action(function (Collection $records) {
                            // Lọc ra những user không phải admin
                            $nonAdminUsers = $records->filter(fn($user) => !$user->hasRole('admin'));
                            $adminUsers = $records->filter(fn($user) => $user->hasRole('admin'));
                            
                            // Xóa những user không phải admin
                            $nonAdminUsers->each->delete();
                            
                            // Thông báo nếu có admin bị bỏ qua
                            if ($adminUsers->count() > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->warning()
                                    ->title('Không thể xóa admin')
                                    ->body('Một số người dùng có vai trò admin đã được bỏ qua và không bị xóa.')
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['roles'])
            ->withCount(['recipes', 'favorites', 'ratings']);
    }
}