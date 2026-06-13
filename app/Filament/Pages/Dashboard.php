<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\FeatureReadiness;
use App\Filament\Widgets\OperationalAlerts;
use App\Filament\Widgets\PaymentChannels;
use App\Filament\Widgets\TenantOverviewStats;
use App\Filament\Widgets\UpcomingOrderDeadlines;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class Dashboard extends BaseDashboard
{
    /**
     * @return array<class-string<Widget>|WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            TenantOverviewStats::class,
            OperationalAlerts::class,
            FeatureReadiness::class,
            PaymentChannels::class,
            UpcomingOrderDeadlines::class,
        ];
    }
}
