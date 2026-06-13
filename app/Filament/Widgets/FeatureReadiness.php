<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Settings\CompanySettings;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class FeatureReadiness extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.feature-readiness';

    public static function canView(): bool
    {
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();

        return $tenant instanceof Company
            && $user?->canAccessTenant($tenant) === true
            && $user->can('View:CompanySettings');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $settings = app(CompanySettings::class);

        return [
            'features' => [
                [
                    'description' => __('dashboard.features.client_progress.description'),
                    'enabled' => $settings->client_progress_enabled,
                    'label' => __('dashboard.features.client_progress.label'),
                ],
                [
                    'description' => __('dashboard.features.budget_tracking.description'),
                    'enabled' => $settings->budget_tracking_enabled,
                    'label' => __('dashboard.features.budget_tracking.label'),
                ],
                [
                    'description' => __('dashboard.features.proof_upload.description'),
                    'enabled' => $settings->proof_upload_enabled,
                    'label' => __('dashboard.features.proof_upload.label'),
                ],
                [
                    'description' => __('dashboard.features.chat.description'),
                    'enabled' => $settings->chat_enabled,
                    'label' => __('dashboard.features.chat.label'),
                ],
                [
                    'description' => __('dashboard.features.reviews.description'),
                    'enabled' => $settings->reviews_enabled,
                    'label' => __('dashboard.features.reviews.label'),
                ],
            ],
        ];
    }
}
