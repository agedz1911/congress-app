<?php

namespace App\Filament\Clusters\Product\Resources\Registration\RegistrationTypeResource\Pages;

use App\Filament\Clusters\Product\Resources\Registration\RegistrationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegistrationType extends EditRecord
{
    protected static string $resource = RegistrationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
