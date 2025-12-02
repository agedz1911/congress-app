<?php

namespace App\Livewire\Dashboard\Registration;

use App\Models\Registration\Order;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListRegistration extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = Order::with(['participant', 'transaction', 'items.product'])
            ->forUser(auth()->id())
            ->search($this->search)
            ->latest()
            ->paginate($this->perPage);
        return view('livewire.dashboard.registration.list-registration', [
            'orders' => $orders,
        ]);
    }
}
