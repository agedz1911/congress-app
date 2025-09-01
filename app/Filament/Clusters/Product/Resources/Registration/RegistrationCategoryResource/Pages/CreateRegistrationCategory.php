<?php

namespace App\Filament\Clusters\Product\Resources\Registration\RegistrationCategoryResource\Pages;

use App\Filament\Clusters\Product\Resources\Registration\RegistrationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRegistrationCategory extends CreateRecord
{
    protected static string $resource = RegistrationCategoryResource::class;
}
