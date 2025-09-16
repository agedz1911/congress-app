<?php

namespace App\View\Components\Navigation;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Livewire\Attributes\On;

class Trolley extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */

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

    
    public function render(): View|Closure|string
    {
        $this->loadCartCount();
        return view('components.navigation.trolley');
    }
}
