<?php

namespace App\Filament\Resources\ProofUploadResource\Pages;

use App\Filament\Resources\ProofUploadResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProofUpload extends EditRecord
{
    protected static string $resource = ProofUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $order = ProofUploadResource::findOrder($data['order_id'] ?? null);

        return [
            ...$data,
            'project_id' => $order?->project_id,
        ];
    }
}
