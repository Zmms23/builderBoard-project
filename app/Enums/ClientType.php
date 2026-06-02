<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClientType: string implements HasLabel
{
    case Person = 'person';
    case Company = 'company';

    public function getLabel(): string
    {
        return match ($this) {
            self::Person => __('client.types.person'),
            self::Company => __('client.types.company'),
        };
    }
}
