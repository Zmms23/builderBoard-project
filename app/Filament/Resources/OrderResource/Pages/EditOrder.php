<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        if ($this->record instanceof Order
            && OrderResource::canManageStatus($this->record)
            && OrderResource::hasStatus($this->record, [OrderStatus::Draft, OrderStatus::Pending])
        ) {
            $actions[] = Action::make('approve')
                ->label(__('order.actions.approve.label'))
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    $record = $this->record;
                    $userId = Filament::auth()->id();
                    $isAssignedWorker = $record->assigned_user_id !== null
                        && (string) $record->assigned_user_id === (string) $userId;

                    $record->update([
                        'status' => $isAssignedWorker
                            ? OrderStatus::Approved
                            : OrderStatus::Pending,
                    ]);
                })
                ->successNotificationTitle(__('order.actions.approve.success'));

            $actions[] = Action::make('reject')
                ->label(__('order.actions.reject.label'))
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update([
                        'status' => OrderStatus::Rejected,
                    ]);
                })
                ->successNotificationTitle(__('order.actions.reject.success'));
        }

        $actions[] = DeleteAction::make();

        return $actions;
    }
}
