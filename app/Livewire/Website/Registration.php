<?php

namespace App\Livewire\Website;

use App\Models\Registration\Product;
use App\Models\Registration\RegistrationCategory;
use Illuminate\Support\Facades\Auth;
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

    public $cartItems = [];
    public $cartCount = 0;

    public function mount()
    {
        $this->products = Product::with(['regtype.regcategory'])->get();

        $this->symposiums = $this->products->filter(function ($product) {
            return optional($product->regtype)->regcategory->title === 'Symposium';
        });

        $this->workshops = $this->products->filter(function ($product) {
            return optional($product->regtype)->regcategory->title === 'Workshop';
        });

        $this->loadCartSession();
    }

    public function loadCartSession()
    {
        $this->cartItems = session()->get('cart', []);
        $this->cartCount = count($this->cartItems);
    }

    public function addToCart($productId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to add items to cart.');
            return redirect()->route('login');
        }

        $product = Product::find($productId);

        if (!$product) {
            session()->flash('error', 'Product not found');
            return;
        }

        $cartKey = 'product_' . $productId;

        if (isset($this->cartItems[$cartKey])) {
            session()->flash('info', 'Item already in cart');
            return;
        }

        $this->cartItems[$cartKey] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'regtype_id' => $product->regtype_id,
            'early_bird_idr' => $product->early_bird_idr,
            'early_bird_usd' => $product->early_bird_usd,
            'regular_idr' => $product->regular_idr,
            'regular_usd' => $product->regular_usd,
            'on_site_idr' => $product->on_site_idr,
            'on_site_usd' => $product->on_site_usd,
            'quantity' => 1,
        ];

        session()->put('cart', $this->cartItems);
        $this->loadCartSession();

        $this->dispatch('cart-updated');

        session()->flash('success', 'Item added to cart sucess');
    }

    public function render()
    {
        return view('livewire.website.registration');
    }
}
