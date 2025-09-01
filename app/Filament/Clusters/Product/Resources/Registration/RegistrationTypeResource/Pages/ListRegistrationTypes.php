<?php

namespace App\Filament\Clusters\Product\Resources\Registration\RegistrationTypeResource\Pages;

use App\Filament\Clusters\Product\Resources\Registration\RegistrationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistrationTypes extends ListRecords
{
    protected static string $resource = RegistrationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
