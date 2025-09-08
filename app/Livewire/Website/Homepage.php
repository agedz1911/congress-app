<?php

namespace App\Livewire\Website;

use Livewire\Attributes\Layout;
use Livewire\Component;


#[Layout('components.layouts.website')]
class Homepage extends Component
{
    public function render()
    {
        return view('livewire.website.homepage');
    }
}
