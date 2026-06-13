<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Helpers\Price;
use App\Models\Company;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Settings\CompanySettings;
use App\Support\TenantWorkScope;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class OperationalAlerts extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.operational-alerts';

    public static function canView(): bool
    {
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();

        return $tenant instanceof Company
            && $user?->canAccessTenant($tenant) === true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Company) {
            return [
                'alerts' => [],
            ];
        }

        $companyId = $tenant->getKey();
        $settings = app(CompanySettings::class);
        $currency = $settings->currency->value;

        $pendingApprovals = TenantWorkScope::orders(Order::query())
            ->where('company_id', $companyId)
            ->where('status', OrderStatus::Pending->value)
            ->count();

        $overdueOrders = TenantWorkScope::orders(Order::query())
            ->where('company_id', $companyId)
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', today())
            ->whereNotIn('status', [
                OrderStatus::Approved->value,
                OrderStatus::Rejected->value,
            ])
            ->count();

        $expectedRevenue = TenantWorkScope::orders(Order::query())
            ->where('company_id', $companyId)
            ->whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Approved->value,
            ])
            ->sum('estimated_price_amount');

        $paidRevenue = Payment::query()
            ->where('company_id', $companyId)
            ->where('status', PaymentStatus::Paid->value)
            ->sum('amount');

        $deadlineRisks = TenantWorkScope::projects(Project::query())
            ->where('company_id', $companyId)
            ->where('status', ProjectStatus::Active->value)
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<=', today()->addDays(14))
            ->where('progress', '<', 80)
            ->count();

        $alerts = [
            [
                'color' => $pendingApprovals > 0 ? 'warning' : 'gray',
                'description' => __('dashboard.alerts.pending_approvals.description'),
                'label' => __('dashboard.alerts.pending_approvals.label'),
                'value' => $pendingApprovals,
            ],
            [
                'color' => $overdueOrders > 0 ? 'danger' : 'gray',
                'description' => __('dashboard.alerts.overdue_orders.description'),
                'label' => __('dashboard.alerts.overdue_orders.label'),
                'value' => $overdueOrders,
            ],
        ];

        if ($settings->budget_tracking_enabled && Filament::auth()->user()?->can('ViewAny:Payment') === true) {
            $alerts[] = [
                'color' => $expectedRevenue > $paidRevenue ? 'warning' : 'success',
                'description' => __('dashboard.alerts.unpaid_balance.description'),
                'label' => __('dashboard.alerts.unpaid_balance.label'),
                'value' => Price::format(max(0, $expectedRevenue - $paidRevenue), $currency),
            ];
        }

        if ($settings->client_progress_enabled) {
            $alerts[] = [
                'color' => $deadlineRisks > 0 ? 'danger' : 'success',
                'description' => __('dashboard.alerts.deadline_risks.description'),
                'label' => __('dashboard.alerts.deadline_risks.label'),
                'value' => $deadlineRisks,
            ];
        }

        return [
            'alerts' => $alerts,
        ];
    }
}
