<?php

namespace App\Filament\Resources\ProofUploadResource\Pages;

use App\Filament\Resources\ProofUploadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProofUploads extends ListRecords
{
    protected static string $resource = ProofUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
