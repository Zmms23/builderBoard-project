<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Projects\Widgets\ProjectWorkspaceOverview;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Widgets\WidgetConfiguration;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('project.workspace.title', [
            'project' => $this->getRecordTitle(),
        ]);
    }

    /**
     * @return array<WidgetConfiguration>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            ProjectWorkspaceOverview::make([
                'record' => $this->getRecord(),
            ]),
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
