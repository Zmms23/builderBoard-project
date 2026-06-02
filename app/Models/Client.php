<?php

namespace App\Models;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['company_id', 'name', 'type', 'status', 'phone', 'email', 'address', 'notes'])]
class Client extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ClientStatus::class,
            'type' => ClientType::class,
        ];
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
