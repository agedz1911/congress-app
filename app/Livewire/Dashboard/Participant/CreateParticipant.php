<?php

namespace App\Livewire\Dashboard\Participant;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateParticipant extends Component
{
    public $countries;

    public function mount()
    {    
        $this->countries = countries();
        if (!Auth::check()) {
            session()->flash('error', 'Please login to create participant.');
            return redirect()->route('login');
        }
    }
    public function render()
    {
        return view('livewire.dashboard.participant.create-participant');
    }
}
