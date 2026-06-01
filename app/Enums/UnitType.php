<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UnitType: string implements HasLabel
{
    case Service = 'service';
    case Item = 'item';
    case SquareMeter = 'm2';
    case Hour = 'hour';
    case Room = 'room';

    public function getLabel(): string
    {
        return match ($this) {
            self::Service => __('subservice.units.service'),
            self::Item => __('subservice.units.item'),
            self::SquareMeter => __('subservice.units.m2'),
            self::Hour => __('subservice.units.hour'),
            self::Room => __('subservice.units.room'),
        };
    }
}
