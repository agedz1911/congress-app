<?php

namespace App\Filament\Resources\Accommodation\HotelResource\Pages;

use App\Filament\Resources\Accommodation\HotelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotels extends ListRecords
{
    protected static string $resource = HotelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
