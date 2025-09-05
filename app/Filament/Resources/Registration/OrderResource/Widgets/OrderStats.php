<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Registration\Order;
use App\Models\Registration\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        $orders = [];
        $transactions = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $orders[] = Order::whereDate('created_at', $date)->count();
            $transactions[] = Transaction::whereDate('payment_date', $date)->count();
        }

        $newOrdersToday = Order::whereDate('created_at', today())->count();
        $changeDescription = "{$newOrdersToday} new orders today";

        $paidToday = Transaction::whereDate('payment_date', today())->count();
        $paidDesc = "{$paidToday} new paid order today";

        return [
            Stat::make('Total Orders', Order::count())
                ->description($changeDescription)
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->chart($orders)
                ->color('success'),
            Stat::make('Paid Registration', Transaction::where('payment_status', 'paid')->count())
                ->description($paidDesc)
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->chart($transactions)
                ->color('success'),
            Stat::make('Unpaid Registration', Transaction::where('payment_status', 'unpaid')->count())
                ->description('Unpaid Orders')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->chart($transactions)
                ->color('danger'),

        ];
    }
}
