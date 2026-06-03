<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasLabel
{
    case Planning = 'planning';
    case Active = 'active';
    case OnHold = 'on_hold';
    case Completed = 'completed';
    case Canceled = 'canceled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Planning => __('project.statuses.planning'),
            self::Active => __('project.statuses.active'),
            self::OnHold => __('project.statuses.on_hold'),
            self::Completed => __('project.statuses.completed'),
            self::Canceled => __('project.statuses.canceled'),
        };
    }
}
