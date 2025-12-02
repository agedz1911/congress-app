<?php

namespace App\Livewire\Dashboard\Registration;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Registration')]
class MyRegistration extends Component
{
    public function render()
    {
        return view('livewire.dashboard.registration.my-registration');
    }
}
