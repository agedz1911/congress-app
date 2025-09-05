<?php

namespace App\Filament\Widgets;

use App\Models\Accommodation\Booking;
use App\Models\Registration\Order;
use App\Models\Registration\Participant;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        Participant::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        $chartData = [];
        $chartParticipant = [];
        $orders = [];
        $bookings = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData[] = User::whereDate('created_at', $date)->count();
            $chartParticipant[] = Participant::whereDate('created_at', $date)->count();
            $orders[] = Order::whereDate('created_at', $date)->count();
            $bookings[] = Booking::whereDate('created_at', $date)->count();
        }

        $newUsersToday = User::whereDate('created_at', today())->count();
        $changeDescription = "{$newUsersToday} new users today";

        $newParticipantToday = Participant::whereDate('created_at', today())->count();
        $changeParticipant = "{$newParticipantToday} new participants today";

        $newOrdersToday = Order::whereDate('created_at', today())->count();
        $changeOrder = "{$newOrdersToday} new registrations today";

        
        $newBookingsToday = Booking::whereDate('created_at', today())->count();
        $changeBooking = "{$newBookingsToday} new bookings today";

        return [
            Stat::make('Total Users', User::count())
                ->description($changeDescription)
                ->descriptionIcon('heroicon-o-user-group', IconPosition::Before)
                ->chart($chartData)
                ->color('success'),
            Stat::make('Total Participants', Participant::count())
                ->description($changeParticipant)
                ->descriptionIcon('heroicon-o-user-group', IconPosition::Before)
                ->chart($chartParticipant)
                ->color('info'),
            Stat::make('Total Registration Orders', Order::count())
                ->description($changeOrder)
                ->descriptionIcon('heroicon-o-user-group', IconPosition::Before)
                ->chart($orders)
                ->color('danger'),
            Stat::make('Total Hotel Bokings', Booking::count())
                ->description($changeBooking)
                ->descriptionIcon('heroicon-o-user-group', IconPosition::Before)
                ->chart($bookings)
                ->color('warning'),
        ];
    }
}
