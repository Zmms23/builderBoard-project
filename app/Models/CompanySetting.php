<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'company_id',
    'logo_path',
    'phone',
    'email',
    'address',
    'website',
    'currency',
    'primary_color',
    'client_progress_enabled',
    'budget_tracking_enabled',
    'proof_upload_enabled',
    'chat_enabled',
    'reviews_enabled',
])]
class CompanySetting extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'client_progress_enabled' => 'boolean',
            'budget_tracking_enabled' => 'boolean',
            'proof_upload_enabled' => 'boolean',
            'chat_enabled' => 'boolean',
            'reviews_enabled' => 'boolean',
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
