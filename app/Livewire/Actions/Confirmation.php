<?php

namespace App\Livewire\Actions;

use App\Models\Registration\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Payment Confirmation')]
#[Layout('components.layouts.website')]
class Confirmation extends Component
{

    public function render()
    {
        return view('livewire.actions.confirmation');
    }
}
