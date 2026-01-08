<?php

namespace App\Livewire\Website;

use App\Models\Accommodation\Hotel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.website')]
#[Title('Accommodation')]
class Accommodation extends Component
{
    public $hotels;

    public function mount()
    {
        $this->hotels = Hotel::with(['rooms' => function($query) {
            $query->orderBy('price_idr', 'asc')->limit(1);
        }])->get();
    }
    public function render()
    {
        return view('livewire.website.accommodation');
    }
}
