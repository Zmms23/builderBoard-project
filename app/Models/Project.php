<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['company_id', 'client_id', 'title', 'status', 'deadline', 'progress', 'budget_amount', 'notes'])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'budget_amount' => 'integer',
            'deadline' => 'date',
            'progress' => 'integer',
            'status' => ProjectStatus::class,
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
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return HasMany<ProjectTimelineStage, $this>
     */
    public function timelineStages(): HasMany
    {
        return $this->hasMany(ProjectTimelineStage::class)->orderBy('sort');
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany<ProofUpload, $this>
     */
    public function proofUploads(): HasMany
    {
        return $this->hasMany(ProofUpload::class);
    }
}
