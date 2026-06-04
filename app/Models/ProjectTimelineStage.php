<?php

namespace App\Models;

use App\Enums\ProjectTimelineStageStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'name', 'status', 'sort', 'starts_at', 'ends_at', 'notes'])]
class ProjectTimelineStage extends Model
{
    protected function casts(): array
    {
        return [
            'ends_at' => 'date',
            'sort' => 'integer',
            'starts_at' => 'date',
            'status' => ProjectTimelineStageStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
