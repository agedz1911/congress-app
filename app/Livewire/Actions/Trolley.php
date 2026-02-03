<?php

namespace App\Livewire\Actions;

use Livewire\Attributes\On;
use Livewire\Component;

class Trolley extends Component
{
    public $cartCount = 0;
    public $cartHotelCount = 0;

    public function mount()
    {
        $this->loadCartCount();
        $this->loadCartHotelCount();
    }

    public function loadCartCount()
    {
        $cartItems = session()->get('cart', []);
        $this->cartCount = count($cartItems);
    }

    #[On('cart-updated')]
    public function updateCartCount()
    {
        $this->loadCartCount();
        $this->loadCartHotelCount();
    }

    public function loadCartHotelCount()
    {
        $cartHotelItems = session()->get('hotel_cart', []);
        $this->cartHotelCount = count($cartHotelItems);
    }

    public function goToCart()
    {
        return redirect()->route('cart');
    }

    public function render()
    {
        $this->loadCartCount();
        return view('livewire.actions.trolley');
    }
}
