<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => __('order.statuses.draft'),
            self::Pending => __('order.statuses.pending'),
            self::Approved => __('order.statuses.approved'),
            self::Rejected => __('order.statuses.rejected'),
        };
    }
}
