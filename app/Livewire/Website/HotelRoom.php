<?php

namespace App\Livewire\Website;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.website')]
#[Title('Accommodation')]
class HotelRoom extends Component
{
    public $hotel;

    public function mount($id)
    {
        $this->hotel = \App\Models\Accommodation\Hotel::with('rooms')->findOrFail($id);
    }
    
    public function render()
    {
        return view('livewire.website.hotel-room');
    }
}
