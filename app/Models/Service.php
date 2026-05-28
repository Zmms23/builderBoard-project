<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['company_id', 'name', 'slug', 'description', 'base_price', 'is_active'])]
class Service extends Model
{
    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    

    /*
          @return HasMany<Service, $this>
     */
    /*
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
    */


    /*
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    */
}


