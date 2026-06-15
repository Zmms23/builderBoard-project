<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Enums\OrderStatus;
use App\Enums\ProjectTimelineStageStatus;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Order;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    /**
     * @var array<string, mixed>
     */
    private array $quickStartOrder = [];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->quickStartOrder = [
            'enabled' => (bool) ($data['first_order_enabled'] ?? false),
            'title' => $data['first_order_title'] ?? null,
            'assigned_user_id' => $data['first_order_assigned_user_id'] ?? null,
            'deadline' => $data['first_order_deadline'] ?? null,
            'estimated_price_amount' => (int) ($data['first_order_estimated_price_amount'] ?? 0),
            'notes' => $data['first_order_notes'] ?? null,
        ];

        if ((int) ($data['budget_amount'] ?? 0) === 0 && $this->quickStartOrder['estimated_price_amount'] > 0) {
            $data['budget_amount'] = $this->quickStartOrder['estimated_price_amount'];
        }

        unset(
            $data['first_order_enabled'],
            $data['first_order_title'],
            $data['first_order_assigned_user_id'],
            $data['first_order_deadline'],
            $data['first_order_estimated_price_amount'],
            $data['first_order_notes'],
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->timelineStages()->createMany(
            collect($this->defaultTimelineStages())
                ->map(fn (string $name, int $index): array => [
                    'name' => $name,
                    'sort' => $index + 1,
                    'status' => ProjectTimelineStageStatus::Pending->value,
                ])
                ->all()
        );

        $this->createQuickStartOrder();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', [
            'record' => $this->record,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function defaultTimelineStages(): array
    {
        return [
            __('project.timeline.default_stages.planning'),
            __('project.timeline.default_stages.demolition'),
            __('project.timeline.default_stages.electrical'),
            __('project.timeline.default_stages.plumbing'),
            __('project.timeline.default_stages.painting'),
            __('project.timeline.default_stages.final'),
        ];
    }

    private function createQuickStartOrder(): void
    {
        if (($this->quickStartOrder['enabled'] ?? false) !== true) {
            return;
        }

        if (blank($this->quickStartOrder['title'] ?? null)) {
            return;
        }

        $this->record->orders()->create([
            'company_id' => $this->record->company_id,
            'client_id' => $this->record->client_id,
            'assigned_user_id' => $this->quickStartOrder['assigned_user_id'] ?: null,
            'number' => $this->nextOrderNumber(),
            'title' => $this->quickStartOrder['title'],
            'status' => OrderStatus::Draft->value,
            'deadline' => $this->quickStartOrder['deadline'] ?: null,
            'progress' => 0,
            'estimated_price_amount' => $this->quickStartOrder['estimated_price_amount'] ?? 0,
            'notes' => $this->quickStartOrder['notes'] ?: null,
        ]);
    }

    private function nextOrderNumber(): string
    {
        $nextNumber = Order::query()
            ->where('company_id', $this->record->company_id)
            ->count() + 1;

        return 'ORD-'.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
