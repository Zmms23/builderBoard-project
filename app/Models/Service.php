<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['company_id', 'name', 'description', 'base_price_amount', 'is_active'])]
class Service extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'base_price_amount' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany<Subservice, $this>
     */
    public function subservices(): HasMany
    {
        return $this->hasMany(Subservice::class);
    }
}
