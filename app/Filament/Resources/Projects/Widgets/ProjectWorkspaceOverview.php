<?php

namespace App\Filament\Resources\Projects\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectTimelineStageStatus;
use App\Helpers\Price;
use App\Models\Order;
use App\Models\Project;
use App\Models\ProofUpload;
use App\Models\Service;
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
        $visibleProofsCount = $project->proofUploads()
            ->where('is_client_visible', true)
            ->count();
        $activeServicesCount = Service::query()
            ->where('company_id', $project->company_id)
            ->where('is_active', true)
            ->count();

        return [
            'activeServicesCount' => $activeServicesCount,
            'budgetSpentPercent' => $this->percentage($paidTotal, $estimatedTotal),
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
            'readinessItems' => $this->readinessItems(
                hasServices: $activeServicesCount > 0,
                hasOrders: $orders->isNotEmpty(),
                hasTimeline: $timelineTotal > 0,
                hasClientProofs: $visibleProofsCount > 0,
            ),
            'remainingTotal' => Price::format(max(0, $estimatedTotal - $paidTotal), $currency),
            'settings' => $settings,
            'timelineCompleted' => $timelineCompleted,
            'timelinePercent' => $this->percentage($timelineCompleted, $timelineTotal),
            'timelineTotal' => $timelineTotal,
            'visibleProofsCount' => $visibleProofsCount,
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

    private function percentage(int $value, int $total): int
    {
        if ($total <= 0) {
            return 0;
        }

        return min(100, (int) round(($value / $total) * 100));
    }

    /**
     * @return array<int, array{label: string, description: string, complete: bool}>
     */
    private function readinessItems(bool $hasServices, bool $hasOrders, bool $hasTimeline, bool $hasClientProofs): array
    {
        return [
            [
                'label' => __('project.workspace.readiness.services.label'),
                'description' => __('project.workspace.readiness.services.description'),
                'complete' => $hasServices,
            ],
            [
                'label' => __('project.workspace.readiness.orders.label'),
                'description' => __('project.workspace.readiness.orders.description'),
                'complete' => $hasOrders,
            ],
            [
                'label' => __('project.workspace.readiness.timeline.label'),
                'description' => __('project.workspace.readiness.timeline.description'),
                'complete' => $hasTimeline,
            ],
            [
                'label' => __('project.workspace.readiness.proofs.label'),
                'description' => __('project.workspace.readiness.proofs.description'),
                'complete' => $hasClientProofs,
            ],
        ];
    }
}
