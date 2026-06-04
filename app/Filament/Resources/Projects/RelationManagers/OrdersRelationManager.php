<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $relatedResource = OrderResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->fillForm(fn (): array => $this->orderDefaults())
                    ->mutateDataUsing(fn (array $data): array => [
                        ...$data,
                        ...$this->orderDefaults(),
                    ]),
            ]);
    }

    /**
     * @return array{project_id: int|string|null, client_id: int|string|null}
     */
    private function orderDefaults(): array
    {
        $project = $this->getOwnerRecord();

        return [
            'project_id' => $project->getKey(),
            'client_id' => $project->client_id,
        ];
    }
}
