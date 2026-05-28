<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum Currency: string implements HasIcon, HasLabel
{
    case GEL = 'GEL';
    case USD = 'USD';
    case EUR = 'EUR';

    public function getLabel(): string
    {
        return $this->value;
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::GEL => Heroicon::Banknotes,
            self::USD => Heroicon::CurrencyDollar,
            self::EUR => Heroicon::CurrencyEuro,
        };
    }
}
