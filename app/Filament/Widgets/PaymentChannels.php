<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Settings\CompanySettings;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class PaymentChannels extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.payment-channels';

    public static function canView(): bool
    {
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();

        return $tenant instanceof Company
            && $user?->canAccessTenant($tenant) === true
            && $user->can('ViewAny:Payment')
            && app(CompanySettings::class)->budget_tracking_enabled;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $settings = app(CompanySettings::class);

        return [
            'bankAccountName' => $settings->bank_account_name,
            'bankAccountNumber' => $settings->bank_account_number,
            'bankName' => $settings->bank_name,
            'bankTransferEnabled' => $settings->bank_transfer_enabled,
            'cashPaymentsEnabled' => $settings->cash_payments_enabled,
            'hasBankDetails' => filled($settings->bank_account_number),
            'paymentInstructions' => $settings->payment_instructions,
        ];
    }
}
