<?php

namespace App\Filament\Resources\Projects\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectTimelineStageStatus;
use App\Helpers\Price;
use App\Models\Order;
use App\Models\Project;
use App\Models\ProofUpload;
use App\Settings\CompanySettings;
use Filament\Widgets\Widget;

class ProjectWorkspaceOverview extends Widget
{
    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.resources.projects.widgets.project-workspace-overview';

    public Project $record;

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $project = $this->record->loadMissing(['client']);
        $settings = app(CompanySettings::class);
        $currency = $settings->currency->value;

        $orders = $project->orders()
            ->with('assignedUser')
            ->orderByRaw('deadline is null')
            ->orderBy('deadline')
            ->get();

        $estimatedTotal = (int) $orders->sum('estimated_price_amount');
        $paidTotal = (int) $project->payments()
            ->where('status', PaymentStatus::Paid->value)
            ->sum('amount');

        $timelineTotal = $project->timelineStages()->count();
        $timelineCompleted = $project->timelineStages()
            ->where('status', ProjectTimelineStageStatus::Completed->value)
            ->count();

        return [
            'client' => $project->client,
            'currency' => $currency,
            'estimatedTotal' => Price::format($estimatedTotal, $currency),
            'latestProof' => $this->latestProof(),
            'nextOrder' => $this->nextOrder(),
            'ordersCount' => $orders->count(),
            'openOrdersCount' => $orders
                ->whereIn('status', [OrderStatus::Draft, OrderStatus::Pending])
                ->count(),
            'paidTotal' => Price::format($paidTotal, $currency),
            'project' => $project,
            'remainingTotal' => Price::format(max(0, $estimatedTotal - $paidTotal), $currency),
            'settings' => $settings,
            'timelineCompleted' => $timelineCompleted,
            'timelineTotal' => $timelineTotal,
            'visibleProofsCount' => $project->proofUploads()
                ->where('is_client_visible', true)
                ->count(),
        ];
    }

    private function latestProof(): ?ProofUpload
    {
        return $this->record->proofUploads()
            ->with(['order', 'uploadedBy'])
            ->latest()
            ->first();
    }

    private function nextOrder(): ?Order
    {
        return $this->record->orders()
            ->with('assignedUser')
            ->whereNotIn('status', [
                OrderStatus::Approved->value,
                OrderStatus::Rejected->value,
            ])
            ->orderByRaw('deadline is null')
            ->orderBy('deadline')
            ->first();
    }
}
