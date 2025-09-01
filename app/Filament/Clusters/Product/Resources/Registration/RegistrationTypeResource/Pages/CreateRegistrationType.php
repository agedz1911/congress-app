<?php

namespace App\Filament\Clusters\Product\Resources\Registration\RegistrationTypeResource\Pages;

use App\Filament\Clusters\Product\Resources\Registration\RegistrationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRegistrationType extends CreateRecord
{
    protected static string $resource = RegistrationTypeResource::class;
}
