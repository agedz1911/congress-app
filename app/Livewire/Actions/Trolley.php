<?php

namespace App\Livewire\Actions;

use Livewire\Attributes\On;
use Livewire\Component;

class Trolley extends Component
{
    public $cartCount = 0;

    public function mount()
    {
        $this->loadCartCount();
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
