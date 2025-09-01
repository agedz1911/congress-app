<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Product extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Registration';

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Product Registration';
}
