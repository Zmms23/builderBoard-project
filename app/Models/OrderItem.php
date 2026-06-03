<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'subservice_id', 'description', 'quantity', 'unit_price_amount', 'total_price_amount'])]
class OrderItem extends Model
{
    protected static function booted(): void
    {
        static::saving(function (OrderItem $orderItem): void {
            $orderItem->total_price_amount = (int) round(((float) $orderItem->quantity) * ((int) $orderItem->unit_price_amount));
        });

        static::saved(function (OrderItem $orderItem): void {
            $orderItem->order?->refreshEstimatedPrice();
        });

        static::deleted(function (OrderItem $orderItem): void {
            $orderItem->order?->refreshEstimatedPrice();
        });
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price_amount' => 'integer',
            'total_price_amount' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function subservice(): BelongsTo
    {
        return $this->belongsTo(Subservice::class);
    }
}
