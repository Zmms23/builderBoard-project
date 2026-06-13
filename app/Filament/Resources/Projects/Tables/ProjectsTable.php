<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use App\Helpers\Price;
use App\Models\Project;
use App\Settings\CompanySettings;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
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
                    ->formatStateUsing(fn (ProjectStatus|string|null $state): string => static::formatProjectStatus($state))
                    ->badge()
                    ->color(fn (ProjectStatus|string|null $state): string => static::projectStatusColor($state)),
                TextColumn::make('deadline')
                    ->label(__('project.columns.deadline'))
                    ->date()
                    ->sortable(),
                TextColumn::make('progress')
                    ->label(__('project.columns.progress'))
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('orders_count')
                    ->label(__('project.columns.orders_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('proof_uploads_count')
                    ->label(__('project.columns.proof_uploads_count'))
                    ->numeric()
                    ->sortable()
                    ->visible(fn (): bool => app(CompanySettings::class)->proof_upload_enabled),
                TextColumn::make('budget_amount')
                    ->label(__('project.columns.budget'))
                    ->formatStateUsing(fn (int|float|string|null $state): string => Price::format($state, static::currency()))
                    ->sortable(),
                TextColumn::make('paid_payments_sum')
                    ->label(__('project.columns.paid'))
                    ->formatStateUsing(fn (int|float|string|null $state): string => Price::format($state, static::currency()))
                    ->sortable()
                    ->visible(fn (): bool => app(CompanySettings::class)->budget_tracking_enabled),
                TextColumn::make('remaining_budget')
                    ->label(__('project.columns.remaining'))
                    ->state(fn (Project $record): int => max(0, (int) $record->budget_amount - (int) ($record->paid_payments_sum ?? 0)))
                    ->formatStateUsing(fn (int|float|string|null $state): string => Price::format($state, static::currency()))
                    ->visible(fn (): bool => app(CompanySettings::class)->budget_tracking_enabled),
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
                Action::make('start')
                    ->label(__('project.actions.start.label'))
                    ->icon(Heroicon::Play)
                    ->color('success')
                    ->visible(fn (Project $record): bool => static::canManageStatus($record) && static::hasStatus($record, [
                        ProjectStatus::Planning,
                        ProjectStatus::OnHold,
                    ]))
                    ->action(function (Project $record): void {
                        $record->update([
                            'status' => ProjectStatus::Active,
                        ]);
                    })
                    ->successNotificationTitle(__('project.actions.start.success')),
                Action::make('complete')
                    ->label(__('project.actions.complete.label'))
                    ->icon(Heroicon::CheckBadge)
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn (Project $record): bool => static::canManageStatus($record) && static::hasStatus($record, [
                        ProjectStatus::Active,
                        ProjectStatus::OnHold,
                    ]))
                    ->action(function (Project $record): void {
                        $record->update([
                            'status' => ProjectStatus::Completed,
                            'progress' => 100,
                        ]);
                    })
                    ->successNotificationTitle(__('project.actions.complete.success')),
                EditAction::make()
                    ->label(__('project.actions.open_workspace.label')),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function formatProjectStatus(ProjectStatus|string|null $state): string
    {
        return $state instanceof ProjectStatus
            ? $state->getLabel()
            : ProjectStatus::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private static function projectStatusColor(ProjectStatus|string|null $state): string
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

    private static function canManageStatus(Project $record): bool
    {
        return Filament::auth()->user()?->can('Update:Project') === true
            && ! static::hasStatus($record, [
                ProjectStatus::Completed,
                ProjectStatus::Canceled,
            ]);
    }

    /**
     * @param  array<int, ProjectStatus>  $statuses
     */
    private static function hasStatus(Project $record, array $statuses): bool
    {
        foreach ($statuses as $status) {
            if ($record->status === $status || $record->status === $status->value) {
                return true;
            }
        }

        return false;
    }

    private static function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }
}
