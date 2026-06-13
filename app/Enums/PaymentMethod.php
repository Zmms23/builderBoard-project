<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum PaymentMethod: string implements HasIcon, HasLabel
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Card = 'card';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Cash => __('payment.methods.cash'),
            self::BankTransfer => __('payment.methods.bank_transfer'),
            self::Card => __('payment.methods.card'),
            self::Other => __('payment.methods.other'),
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Cash => Heroicon::Banknotes,
            self::BankTransfer => Heroicon::BuildingLibrary,
            self::Card => Heroicon::CreditCard,
            self::Other => Heroicon::EllipsisHorizontal,
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Cash => 'success',
            self::BankTransfer => 'info',
            self::Card => 'primary',
            self::Other => 'gray',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $method): array => [$method->value => $method->getLabel()])
            ->all();
    }
}
