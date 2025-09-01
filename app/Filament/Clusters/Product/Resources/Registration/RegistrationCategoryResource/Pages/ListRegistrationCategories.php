<?php

namespace App\Filament\Clusters\Product\Resources\Registration\RegistrationCategoryResource\Pages;

use App\Filament\Clusters\Product\Resources\Registration\RegistrationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistrationCategories extends ListRecords
{
    protected static string $resource = RegistrationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
