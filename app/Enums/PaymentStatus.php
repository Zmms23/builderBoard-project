<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => __('payment.statuses.pending'),
            self::Paid => __('payment.statuses.paid'),
            self::Failed => __('payment.statuses.failed'),
            self::Refunded => __('payment.statuses.refunded'),
        };
    }
}
