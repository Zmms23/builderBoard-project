<?php

namespace App\Filament\Resources\ProofUploadResource\Pages;

use App\Filament\Resources\ProofUploadResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateProofUpload extends CreateRecord
{
    protected static string $resource = ProofUploadResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $order = ProofUploadResource::findOrder($data['order_id'] ?? null);

        return [
            ...$data,
            'company_id' => Filament::getTenant()?->getKey(),
            'project_id' => $order?->project_id,
            'uploaded_by_id' => Filament::auth()->id(),
        ];
    }
}
