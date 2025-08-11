<?php

namespace App\Filament\ManagerResources\RecipeResource\Pages;

use App\Filament\ManagerResources\RecipeResource;
use App\Models\Recipe;
use App\Services\RecipeService;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewRecipe extends ViewRecord
{
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->record;
        $user = Auth::user();

        $actions = [];

        // Nếu là công thức của chính mình thì cho phép chỉnh sửa
        if ($record->user_id === $user->id) {
            $actions[] = Actions\EditAction::make()
                ->label('Chỉnh sửa')
                ->icon('heroicon-o-pencil');

            $actions[] = Actions\DeleteAction::make()
                ->label('Xóa công thức')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation();
        }

        // Nếu là công thức của người khác và đang chờ duyệt thì cho phép duyệt
        if ($record->user_id !== $user->id && $record->status === 'pending') {
            $actions[] = Actions\Action::make('approve')
                ->label('Phê duyệt')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Phê duyệt công thức')
                ->modalDescription('Bạn có chắc chắn muốn phê duyệt công thức này?')
                ->modalSubmitActionLabel('Phê duyệt')
                ->action(function () use ($record, $user) {
                    app(RecipeService::class)->approve($record, $user);
                    $this->redirect($this->getResource()::getUrl('index'));
                });

            $actions[] = Actions\Action::make('reject')
                ->label('Từ chối')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('Lý do từ chối')
                        ->required()
                        ->maxLength(500)
                        ->placeholder('Nhập lý do từ chối công thức (bắt buộc)...'),
                ])
                ->modalHeading('Từ chối công thức')
                ->modalDescription('Vui lòng cung cấp lý do từ chối rõ ràng để tác giả có thể chỉnh sửa.')
                ->modalSubmitActionLabel('Từ chối')
                ->action(function (array $data) use ($record, $user) {
                    app(RecipeService::class)->reject($record, $user, $data['reason']);
                    $this->redirect($this->getResource()::getUrl('index'));
                });
        }

        return $actions;
    }
}
