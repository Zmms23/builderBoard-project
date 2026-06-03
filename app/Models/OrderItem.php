<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'subservice_id', 'description', 'quantity', 'unit_price', 'total_price'])]
class OrderItem extends Model
{
    protected static function booted(): void
    {
        static::saving(function (OrderItem $orderItem): void {
            $orderItem->total_price = ((float) $orderItem->quantity) * ((float) $orderItem->unit_price);
        });
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
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
