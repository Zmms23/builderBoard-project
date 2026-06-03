<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectTaskStatus: string implements HasLabel
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Blocked = 'blocked';

    public function getLabel(): string
    {
        return match ($this) {
            self::Todo => __('project.task_statuses.todo'),
            self::InProgress => __('project.task_statuses.in_progress'),
            self::Done => __('project.task_statuses.done'),
            self::Blocked => __('project.task_statuses.blocked'),
        };
    }
}
