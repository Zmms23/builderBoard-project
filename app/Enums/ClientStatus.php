<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClientStatus: string implements HasLabel
{
    case Lead = 'lead';
    case Active = 'active';
    case Inactive = 'inactive';

    public function getLabel(): string
    {
        return match ($this) {
            self::Lead => __('client.statuses.lead'),
            self::Active => __('client.statuses.active'),
            self::Inactive => __('client.statuses.inactive'),
        };
    }
}
