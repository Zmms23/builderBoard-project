<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Enums\ProjectTaskStatus;
use App\Helpers\Price;
use App\Settings\CompanySettings;
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

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('project.tasks.fields.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('assigned_to_id')
                    ->label(__('project.tasks.fields.assignee'))
                    ->options(fn (): array => $this->assigneeOptions())
                    ->searchable()
                    ->preload()
                    ->native(false),
                Select::make('status')
                    ->label(__('project.tasks.fields.status'))
                    ->options(ProjectTaskStatus::class)
                    ->default(ProjectTaskStatus::Todo)
                    ->native(false)
                    ->required(),
                TextInput::make('sort')
                    ->label(__('project.tasks.fields.sort'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                DatePicker::make('deadline')
                    ->label(__('project.tasks.fields.deadline'))
                    ->native(false),
                TextInput::make('budget_amount')
                    ->label(__('project.tasks.fields.budget'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->prefix(fn (): string => $this->currency())
                    ->formatStateUsing(fn (int | float | string | null $state): string => Price::fromAmount($state))
                    ->dehydrateStateUsing(fn (int | float | string | null $state): int => Price::toAmount($state))
                    ->required(),
                Textarea::make('notes')
                    ->label(__('project.tasks.fields.notes'))
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
                    ->label(__('project.tasks.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label(__('project.tasks.columns.assignee'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('project.tasks.columns.status'))
                    ->formatStateUsing(fn (ProjectTaskStatus | string | null $state): string => $this->formatStatus($state))
                    ->badge()
                    ->color(fn (ProjectTaskStatus | string | null $state): string => $this->statusColor($state)),
                TextColumn::make('deadline')
                    ->label(__('project.tasks.columns.deadline'))
                    ->date()
                    ->sortable(),
                TextColumn::make('budget_amount')
                    ->label(__('project.tasks.columns.budget'))
                    ->formatStateUsing(fn (int | float | string | null $state): string => Price::format($state, $this->currency()))
                    ->sortable(),
                TextColumn::make('sort')
                    ->label(__('project.tasks.columns.sort'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label(__('project.tasks.columns.notes'))
                    ->limit(40)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('project.tasks.filters.status'))
                    ->options(ProjectTaskStatus::class),
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

    /**
     * @return array<int, string>
     */
    private function assigneeOptions(): array
    {
        return $this->getOwnerRecord()
            ->company
            ->members()
            ->orderBy('users.name')
            ->pluck('users.name', 'users.id')
            ->all();
    }

    private function formatStatus(ProjectTaskStatus | string | null $state): string
    {
        return $state instanceof ProjectTaskStatus
            ? $state->getLabel()
            : ProjectTaskStatus::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private function statusColor(ProjectTaskStatus | string | null $state): string
    {
        $status = $state instanceof ProjectTaskStatus
            ? $state
            : ProjectTaskStatus::tryFrom((string) $state);

        return match ($status) {
            ProjectTaskStatus::Todo => 'gray',
            ProjectTaskStatus::InProgress => 'warning',
            ProjectTaskStatus::Done => 'success',
            ProjectTaskStatus::Blocked => 'danger',
            default => 'gray',
        };
    }

    private function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }
}
