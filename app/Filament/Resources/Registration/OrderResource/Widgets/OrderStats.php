<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Registration\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        $orders = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $orders[] = Order::whereDate('created_at', $date)->count();
        }

        $newOrdersToday = Order::whereDate('created_at', today())->count();
        $changeDescription = "{$newOrdersToday} new orders today";

        return [
            Stat::make('Total Orders', Order::count())
                ->description($changeDescription)
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->chart($orders)
                ->color('success'),

        ];
    }
}
