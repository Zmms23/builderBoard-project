<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
