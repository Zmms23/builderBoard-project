<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Enums\ProjectTimelineStageStatus;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TimelineStagesRelationManager extends RelationManager
{
    protected static string $relationship = 'timelineStages';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('project.timeline.fields.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->label(__('project.timeline.fields.status'))
                    ->options(ProjectTimelineStageStatus::class)
                    ->default(ProjectTimelineStageStatus::Pending)
                    ->native(false)
                    ->required(),
                TextInput::make('sort')
                    ->label(__('project.timeline.fields.sort'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                DatePicker::make('starts_at')
                    ->label(__('project.timeline.fields.starts_at'))
                    ->native(false),
                DatePicker::make('ends_at')
                    ->label(__('project.timeline.fields.ends_at'))
                    ->native(false),
                Textarea::make('notes')
                    ->label(__('project.timeline.fields.notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')
                    ->label(__('project.timeline.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('project.timeline.columns.status'))
                    ->formatStateUsing(fn (ProjectTimelineStageStatus | string | null $state): string => $this->formatStatus($state))
                    ->badge()
                    ->color(fn (ProjectTimelineStageStatus | string | null $state): string => $this->statusColor($state)),
                TextColumn::make('sort')
                    ->label(__('project.timeline.columns.sort'))
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label(__('project.timeline.columns.starts_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label(__('project.timeline.columns.ends_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label(__('project.timeline.columns.notes'))
                    ->limit(40)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('project.timeline.filters.status'))
                    ->options(ProjectTimelineStageStatus::class),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    private function formatStatus(ProjectTimelineStageStatus | string | null $state): string
    {
        return $state instanceof ProjectTimelineStageStatus
            ? $state->getLabel()
            : ProjectTimelineStageStatus::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private function statusColor(ProjectTimelineStageStatus | string | null $state): string
    {
        $status = $state instanceof ProjectTimelineStageStatus
            ? $state
            : ProjectTimelineStageStatus::tryFrom((string) $state);

        return match ($status) {
            ProjectTimelineStageStatus::Pending => 'gray',
            ProjectTimelineStageStatus::InProgress => 'warning',
            ProjectTimelineStageStatus::Completed => 'success',
            ProjectTimelineStageStatus::Blocked => 'danger',
            default => 'gray',
        };
    }
}
