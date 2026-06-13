<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['company_id', 'project_id', 'client_id', 'assigned_user_id', 'number', 'title', 'status', 'deadline', 'progress', 'estimated_price_amount', 'notes'])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'estimated_price_amount' => 'integer',
            'status' => OrderStatus::class,
            'deadline' => 'date',
            'progress' => 'integer',
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
     * @return BelongsTo<User, $this>
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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

    public function refreshEstimatedPrice(): void
    {
        $this->forceFill([
            'estimated_price_amount' => $this->items()->sum('total_price_amount'),
        ])->save();
    }
}
