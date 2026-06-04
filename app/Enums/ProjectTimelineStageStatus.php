<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectTimelineStageStatus: string implements HasLabel
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Blocked = 'blocked';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => __('project.timeline_stage_statuses.pending'),
            self::InProgress => __('project.timeline_stage_statuses.in_progress'),
            self::Completed => __('project.timeline_stage_statuses.completed'),
            self::Blocked => __('project.timeline_stage_statuses.blocked'),
        };
    }
}
