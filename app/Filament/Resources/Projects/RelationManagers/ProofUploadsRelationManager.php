<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Helpers\Price;
use App\Models\Order;
use App\Settings\CompanySettings;
use App\Support\TenantWorkScope;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProofUploadsRelationManager extends RelationManager
{
    protected static string $relationship = 'proofUploads';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return app(CompanySettings::class)->proof_upload_enabled
            && parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('proof_upload.navigation.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->label(__('proof_upload.fields.order'))
                    ->options(fn (): array => $this->orderOptions())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
                TextInput::make('title')
                    ->label(__('proof_upload.fields.title'))
                    ->required()
                    ->maxLength(255),
                FileUpload::make('photo_path')
                    ->label(__('proof_upload.fields.photo'))
                    ->image()
                    ->disk('public')
                    ->directory('proof-uploads')
                    ->visibility('public')
                    ->maxSize(4096)
                    ->openable()
                    ->downloadable()
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('expense_amount')
                    ->label(__('proof_upload.fields.expense'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->prefix(fn (): string => $this->currency())
                    ->formatStateUsing(fn (int|float|string|null $state): string => Price::fromAmount($state))
                    ->dehydrateStateUsing(fn (int|float|string|null $state): int => Price::toAmount($state))
                    ->visible(fn (): bool => app(CompanySettings::class)->budget_tracking_enabled),
                Toggle::make('is_client_visible')
                    ->label(__('proof_upload.fields.is_client_visible'))
                    ->helperText(__('proof_upload.help.is_client_visible'))
                    ->default(fn (): bool => app(CompanySettings::class)->client_progress_enabled)
                    ->visible(fn (): bool => app(CompanySettings::class)->client_progress_enabled),
                Textarea::make('comment')
                    ->label(__('proof_upload.fields.comment'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('photo_path')
                    ->label(__('proof_upload.columns.photo'))
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->label(__('proof_upload.columns.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order.number')
                    ->label(__('proof_upload.columns.order'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('uploadedBy.name')
                    ->label(__('proof_upload.columns.uploaded_by'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expense_amount')
                    ->label(__('proof_upload.columns.expense'))
                    ->formatStateUsing(fn (int|float|string|null $state): string => Price::format($state, $this->currency()))
                    ->sortable()
                    ->visible(fn (): bool => app(CompanySettings::class)->budget_tracking_enabled),
                TextColumn::make('is_client_visible')
                    ->label(__('proof_upload.columns.is_client_visible'))
                    ->formatStateUsing(fn (?bool $state): string => $state ? __('proof_upload.values.yes') : __('proof_upload.values.no'))
                    ->badge()
                    ->color(fn (?bool $state): string => $state ? 'success' : 'gray')
                    ->visible(fn (): bool => app(CompanySettings::class)->client_progress_enabled),
                TextColumn::make('created_at')
                    ->label(__('proof_upload.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(fn (array $data): array => $this->proofDefaults($data, assignUploader: true)),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateDataUsing(fn (array $data): array => $this->proofDefaults($data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    /**
     * @return array<int, string>
     */
    private function orderOptions(): array
    {
        $project = $this->getOwnerRecord();

        return TenantWorkScope::orders(Order::query())
            ->where('company_id', $project->company_id)
            ->where('project_id', $project->getKey())
            ->orderBy('number')
            ->get()
            ->mapWithKeys(fn (Order $order): array => [
                $order->id => "{$order->number} - {$order->title}",
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function proofDefaults(array $data, bool $assignUploader = false): array
    {
        $project = $this->getOwnerRecord();

        $defaults = [
            ...$data,
            'company_id' => $project->company_id,
            'project_id' => $project->getKey(),
        ];

        if ($assignUploader) {
            $defaults['uploaded_by_id'] = Filament::auth()->id();
        }

        return $defaults;
    }

    private function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }
}
