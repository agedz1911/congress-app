<?php

namespace App\Filament\Resources\Accommodation\BookingResource\Pages;

use App\Filament\Resources\Accommodation\BookingResource;
use App\Filament\Resources\Accommodation\BookingResource\Widgets\BookingStats;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BookingStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'new' => Tab::make()->query(fn($query) => $query->where('status', 'new')),
            'processing' => Tab::make()->query(fn($query) => $query->where('status', 'Processing')),
            'validated' => Tab::make()->query(fn($query) => $query->where('status', 'Validated')),
            'cancelled' => Tab::make()->query(fn($query) => $query->where('status', 'cancelled')),
        ];
    }
}
