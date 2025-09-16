<?php

namespace App\Livewire\Actions;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.website')]
#[Title('Cart - Congress App')]
class Cart extends Component
{
    public $step = 1;
    public $countries;

    public function mount()
    {
        $this->countries = countries();
    }

    public function backToRegistration()
    {
        return redirect()->route('registration');
    }

    public function backToOrderSummary()
    {
        $this->step = 1;
    }

    public function continueToParticipant()
    {
        
        $this->step = 2;
    }

    public function backToParticipant()
    {
        
        $this->step = 2;
    }

    public function continueToPaymentMethod()
    {
        
        $this->step = 3;
    }

    public function backToPaymentMethod()
    {
        $this->step = 3;
    }

    public function continueToReview()
    {
        
        $this->step = 4;
    }

    public function render()
    {
        return view('livewire.actions.cart');
    }
}
