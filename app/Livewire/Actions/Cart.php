<?php

namespace App\Livewire\Actions;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.website')]
#[Title('Cart - Congress App')]
class Cart extends Component
{
    public function render()
    {
        return view('livewire.actions.cart');
    }
}
