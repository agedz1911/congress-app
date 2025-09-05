<?php

namespace App\Filament\Resources\Accommodation\BookingResource\Widgets;

use App\Models\Accommodation\Booking;
use App\Models\Accommodation\BookingTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStats extends BaseWidget
{
    protected function getStats(): array
    {
        $orders = [];
        $transactions = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $orders[] = Booking::whereDate('created_at', $date)->count();
            $transactions[] = BookingTransaction::whereDate('payment_date', $date)->count();
        }

        $newOrdersToday = Booking::whereDate('created_at', today())->count();
        $changeDescription = "{$newOrdersToday} new orders today";

        $paidToday = BookingTransaction::whereDate('payment_date', today())->count();
        $paidDesc = "{$paidToday} new paid order today";

        return [
            Stat::make('Total Orders', Booking::count())
                ->description($changeDescription)
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->chart($orders)
                ->color('success'),
            Stat::make('Paid Registration', BookingTransaction::where('payment_status', 'paid')->count())
                ->description($paidDesc)
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->chart($transactions)
                ->color('success'),
            Stat::make('Unpaid Registration', BookingTransaction::where('payment_status', 'unpaid')->count())
                ->description('Unpaid Orders')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->chart($transactions)
                ->color('danger'),
        ];
    }
}
