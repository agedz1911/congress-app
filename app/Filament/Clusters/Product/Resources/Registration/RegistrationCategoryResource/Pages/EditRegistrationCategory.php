<?php

namespace App\Filament\Clusters\Product\Resources\Registration\RegistrationCategoryResource\Pages;

use App\Filament\Clusters\Product\Resources\Registration\RegistrationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegistrationCategory extends EditRecord
{
    protected static string $resource = RegistrationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
