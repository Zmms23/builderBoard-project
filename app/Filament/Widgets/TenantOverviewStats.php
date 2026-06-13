<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Helpers\Price;
use App\Models\Client;
use App\Models\Company;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProofUpload;
use App\Settings\CompanySettings;
use App\Support\TenantWorkScope;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantOverviewStats extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Company) {
            return [];
        }

        $companyId = $tenant->getKey();
        $settings = app(CompanySettings::class);
        $currency = $settings->currency->value;

        $activeProjects = TenantWorkScope::projects(Project::query())
            ->where('company_id', $companyId)
            ->whereIn('status', [
                ProjectStatus::Planning->value,
                ProjectStatus::Active->value,
            ])
            ->count();

        $pendingOrders = TenantWorkScope::orders(Order::query())
            ->where('company_id', $companyId)
            ->whereIn('status', [
                OrderStatus::Draft->value,
                OrderStatus::Pending->value,
            ])
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

        $proofUploads = TenantWorkScope::proofUploads(ProofUpload::query())
            ->where('company_id', $companyId)
            ->count();

        $estimatedRevenue = TenantWorkScope::orders(Order::query())
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

        $remainingBalance = max(0, $estimatedRevenue - $paidRevenue);

        $stats = [
            Stat::make(__('dashboard.stats.active_projects'), $activeProjects)
                ->description(__('dashboard.stats.active_projects_description'))
                ->descriptionIcon(Heroicon::ChartBar)
                ->icon(Heroicon::BuildingOffice2)
                ->color('success'),
            Stat::make(__('dashboard.stats.pending_orders'), $pendingOrders)
                ->description(__('dashboard.stats.pending_orders_description'))
                ->descriptionIcon(Heroicon::ClipboardDocumentList)
                ->icon(Heroicon::ClipboardDocumentCheck)
                ->color('warning'),
            Stat::make(__('dashboard.stats.overdue_orders'), $overdueOrders)
                ->description(__('dashboard.stats.overdue_orders_description'))
                ->descriptionIcon(Heroicon::ExclamationTriangle)
                ->icon(Heroicon::Clock)
                ->color($overdueOrders > 0 ? 'danger' : 'gray'),
        ];

        if ($settings->proof_upload_enabled) {
            $stats = [
                ...$stats,
                Stat::make(__('dashboard.stats.proof_uploads'), $proofUploads)
                    ->description(__('dashboard.stats.proof_uploads_description'))
                    ->descriptionIcon(Heroicon::Photo)
                    ->icon(Heroicon::Camera)
                    ->color('info'),
            ];
        }

        if ($settings->budget_tracking_enabled && ! TenantWorkScope::currentUserIsWorker()) {
            $stats = [
                ...$stats,
                Stat::make(__('dashboard.stats.estimated_revenue'), Price::format($estimatedRevenue, $currency))
                    ->description(__('dashboard.stats.estimated_revenue_description'))
                    ->descriptionIcon(Heroicon::ArrowTrendingUp)
                    ->icon(Heroicon::Banknotes)
                    ->color('primary'),
                Stat::make(__('dashboard.stats.paid_revenue'), Price::format($paidRevenue, $currency))
                    ->description(__('dashboard.stats.paid_revenue_description'))
                    ->descriptionIcon(Heroicon::CreditCard)
                    ->icon(Heroicon::Wallet)
                    ->color('success'),
                Stat::make(__('dashboard.stats.remaining_balance'), Price::format($remainingBalance, $currency))
                    ->description(__('dashboard.stats.remaining_balance_description'))
                    ->descriptionIcon(Heroicon::ReceiptPercent)
                    ->icon(Heroicon::DocumentCurrencyDollar)
                    ->color($remainingBalance > 0 ? 'warning' : 'gray'),
            ];
        }

        if (TenantWorkScope::currentUserIsWorker()) {
            return $stats;
        }

        return [
            ...$stats,
            Stat::make(__('dashboard.stats.clients'), Client::query()->where('company_id', $companyId)->count())
                ->description(__('dashboard.stats.clients_description'))
                ->descriptionIcon(Heroicon::UserGroup)
                ->icon(Heroicon::Users)
                ->color('info'),
            Stat::make(__('dashboard.stats.members'), $tenant->members()->count())
                ->description(__('dashboard.stats.members_description'))
                ->descriptionIcon(Heroicon::ShieldCheck)
                ->icon(Heroicon::UserCircle)
                ->color('gray'),
        ];
    }

    public static function canView(): bool
    {
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();

        return $tenant instanceof Company
            && $user?->canAccessTenant($tenant) === true;
    }
}
