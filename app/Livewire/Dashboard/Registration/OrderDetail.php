<?php

namespace App\Livewire\Dashboard\Registration;

use App\Models\Registration\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order Detail - Congress App')]
class OrderDetail extends Component
{
    public $regCode;
    public $order;

    public function mount($regCode)
    {
        // Load order dengan relationships
        $this->order = Order::with(['participant', 'items.product', 'transaction'])
            ->where('reg_code', $regCode)
            ->forUser(Auth::id()) // Pastikan user hanya bisa lihat order miliknya
            ->firstOrFail();
    }
    
    public function render()
    {
        return view('livewire.dashboard.registration.order-detail');
    }
}
