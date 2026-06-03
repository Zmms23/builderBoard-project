<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use App\Helpers\Price;
use App\Settings\CompanySettings;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label(__('project.columns.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order.number')
                    ->label(__('project.columns.order'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label(__('project.columns.client'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('project.columns.status'))
                    ->formatStateUsing(fn (ProjectStatus | string | null $state): string => static::formatProjectStatus($state))
                    ->badge()
                    ->color(fn (ProjectStatus | string | null $state): string => static::projectStatusColor($state)),
                TextColumn::make('deadline')
                    ->label(__('project.columns.deadline'))
                    ->date()
                    ->sortable(),
                TextColumn::make('progress')
                    ->label(__('project.columns.progress'))
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('budget_amount')
                    ->label(__('project.columns.budget'))
                    ->formatStateUsing(fn (int | float | string | null $state): string => Price::format($state, static::currency()))
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('project.columns.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('project.filters.status'))
                    ->options(ProjectStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function formatProjectStatus(ProjectStatus | string | null $state): string
    {
        return $state instanceof ProjectStatus
            ? $state->getLabel()
            : ProjectStatus::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private static function projectStatusColor(ProjectStatus | string | null $state): string
    {
        $status = $state instanceof ProjectStatus
            ? $state
            : ProjectStatus::tryFrom((string) $state);

        return match ($status) {
            ProjectStatus::Planning => 'gray',
            ProjectStatus::Active => 'success',
            ProjectStatus::OnHold => 'warning',
            ProjectStatus::Completed => 'primary',
            ProjectStatus::Canceled => 'danger',
            default => 'gray',
        };
    }

    private static function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }
}
