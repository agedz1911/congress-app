<?php

namespace App\Livewire\Website;

use App\Models\Registration\Product;
use App\Models\Registration\RegistrationCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.website')]
#[Title('Registration - BURN 2025')]
class Registration extends Component
{
    public $categories;

    public function mount()
    {
        $this->categories = Product::with(['regtype'])->get();
    }
    public function render()
    {
        return view('livewire.website.registration');
    }
}
