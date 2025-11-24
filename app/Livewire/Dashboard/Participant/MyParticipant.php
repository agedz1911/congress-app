<?php

namespace App\Livewire\Dashboard\Participant;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Participants')]
class MyParticipant extends Component
{
    public function render()
    {
        return view('livewire.dashboard.participant.my-participant');
    }
}
