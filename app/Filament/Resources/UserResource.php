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
                        Forms\Components\TextInput::make('password')
                            ->label('Mật khẩu')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
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
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable(),
                    ]),
                
                Forms\Components\Section::make('Thông tin bổ sung')
                    ->schema([
                        Forms\Components\TextInput::make('avatar')
                            ->label('Avatar URL')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('bio')
                            ->label('Tiểu sử')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('preferences')
                            ->label('Tùy chọn')
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Xác thực email lúc'),
                        Forms\Components\DateTimePicker::make('last_login_at')
                            ->label('Đăng nhập cuối lúc'),
                        Forms\Components\TextInput::make('login_count')
                            ->label('Số lần đăng nhập')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Vai trò')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
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
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('ban')
                        ->label('Cấm người dùng')
                        ->icon('heroicon-o-ban')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (User $record) => $record->update(['status' => 'banned']))
                        ->visible(fn (User $record) => $record->status !== 'banned'),
                    Tables\Actions\Action::make('activate')
                        ->label('Kích hoạt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (User $record) => $record->update(['status' => 'active']))
                        ->visible(fn (User $record) => $record->status !== 'active'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('ban')
                        ->label('Cấm người dùng')
                        ->icon('heroicon-o-ban')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'banned'])),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Kích hoạt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'active'])),
                    Tables\Actions\DeleteBulkAction::make(),
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
            ->with(['roles']);
    }
}
