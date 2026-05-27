<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Currency: string implements HasLabel
{
    case GEL = 'GEL';
    case USD = 'USD';
    case EUR = 'EUR';

    public function getLabel(): string
    {
        return $this->value;
    }
}
