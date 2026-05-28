<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PricingType: string implements HasLabel
{
    case Fixed = 'fixed';
    case PerItem = 'per_item';
    case PerSquareMeter = 'per_m2';
    case PerHour = 'per_hour';

    public function getLabel(): string
    {
        return match ($this) {
            self::Fixed => __('subservice.pricing_types.fixed'),
            self::PerItem => __('subservice.pricing_types.per_item'),
            self::PerSquareMeter => __('subservice.pricing_types.per_m2'),
            self::PerHour => __('subservice.pricing_types.per_hour'),
        };
    }
}
