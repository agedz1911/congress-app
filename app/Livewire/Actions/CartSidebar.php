<?php

namespace App\Livewire\Actions;

use Livewire\Attributes\On;
use Livewire\Component;

class CartSidebar extends Component
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

    
    public function render()
    {
        return view('livewire.actions.cart-sidebar');
    }
}
