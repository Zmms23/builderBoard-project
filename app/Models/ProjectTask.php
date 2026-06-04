<?php

namespace App\Models;

use App\Enums\ProjectTaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'order_id', 'assigned_to_id', 'name', 'status', 'sort', 'deadline', 'budget_amount', 'notes'])]
class ProjectTask extends Model
{
    protected function casts(): array
    {
        return [
            'budget_amount' => 'integer',
            'deadline' => 'date',
            'sort' => 'integer',
            'status' => ProjectTaskStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }
}
