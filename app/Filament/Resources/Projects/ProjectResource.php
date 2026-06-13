<?php

namespace App\Filament\Resources\Projects;

use App\Enums\PaymentStatus;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\Projects\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\Projects\RelationManagers\ProofUploadsRelationManager;
use App\Filament\Resources\Projects\RelationManagers\TimelineStagesRelationManager;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Resources\Projects\Tables\ProjectsTable;
use App\Models\Project;
use App\Support\TenantWorkScope;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $tenantOwnershipRelationshipName = 'company';

    protected static ?int $navigationSort = 18;

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
            PaymentsRelationManager::class,
            TimelineStagesRelationManager::class,
            ProofUploadsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withCount(['orders', 'proofUploads'])
            ->withSum([
                'payments as paid_payments_sum' => fn (Builder $query): Builder => $query
                    ->where('status', PaymentStatus::Paid->value),
            ], 'amount');

        return TenantWorkScope::projects($query);
    }

    public static function getModelLabel(): string
    {
        return __('project.navigation.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('project.navigation.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('project.navigation.workspace');
    }

    public static function getNavigationBadge(): ?string
    {
        if (! Filament::getTenant()) {
            return null;
        }

        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('project.navigation.badge');
    }
}
