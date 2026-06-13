<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Enums\ProjectTimelineStageStatus;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function afterCreate(): void
    {
        $this->record->timelineStages()->createMany(
            collect($this->defaultTimelineStages())
                ->map(fn (string $name, int $index): array => [
                    'name' => $name,
                    'sort' => $index + 1,
                    'status' => ProjectTimelineStageStatus::Pending->value,
                ])
                ->all()
        );
    }

    /**
     * @return array<int, string>
     */
    private function defaultTimelineStages(): array
    {
        return [
            __('project.timeline.default_stages.planning'),
            __('project.timeline.default_stages.demolition'),
            __('project.timeline.default_stages.electrical'),
            __('project.timeline.default_stages.plumbing'),
            __('project.timeline.default_stages.painting'),
            __('project.timeline.default_stages.final'),
        ];
    }
}
