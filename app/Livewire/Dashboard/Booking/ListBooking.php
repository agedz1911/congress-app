<?php

namespace App\Livewire\Dashboard\Booking;

use App\Models\Accommodation\Booking;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListBooking extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $search = '';
    public $perPage = 10;
    // public $bookings;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $bookings = Booking::with(['participant', 'bookingTransaction', 'hotel.rooms'])
            ->forUser(auth()->id())
            ->search($this->search)
            ->paginate($this->perPage);
        return view('livewire.dashboard.booking.list-booking', [
            'bookings' => $bookings
        ]);
    }
}
