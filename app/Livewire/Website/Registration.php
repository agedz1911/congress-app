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
    public $products;
    public $symposiums;
    public $workshops;

    public function mount()
    {
        $this->products = Product::with(['regtype.regcategory'])->get();

        $this->symposiums = $this->products->filter(function ($product) {
            return optional($product->regtype)->regcategory->title === 'Symposium';
        });

        $this->workshops = $this->products->filter(function ($product) {
            return optional($product->regtype)->regcategory->title === 'Workshop';
        });
    }
    public function render()
    {
        return view('livewire.website.registration');
    }
}
