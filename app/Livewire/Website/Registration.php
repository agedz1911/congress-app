<?php

namespace App\Livewire\Website;

use App\Models\Registration\Product;
use App\Models\Registration\RegistrationCategory;
use Filament\Notifications\Notification;
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

    public function addToCart($productId, $type = null)
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

        // Determine the registration type based on current time and product dates
        if ($type === 'onsite' || now()->isAfter($product->regular_end)) {
            $priceType = 'on_site';
        } elseif (now()->isAfter($product->early_bird_end) && now()->isBefore($product->regular_end)) {
            $priceType = 'regular';
        } else {
            $priceType = 'early_bird';
        }

        // Determine currency based on user country
        $user = Auth::user();
        $currency = 'idr'; // default currency
        if ($user && isset($user->country) && strtolower($user->country) !== 'indonesia') {
            $currency = 'usd';
        }

        $priceField = $priceType . '_' . $currency;

        $this->cartItems[$cartKey] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'regtype_id' => $product->regtype_id,
            'category' => optional($product->regtype)->regcategory->title ?? 'Unknown',
            'price' => $product->{$priceField},
            'currency' => strtoupper($currency),
            'price_type' => $priceType,
            'quantity' => 1,
        ];

        session()->put('cart', $this->cartItems);
        $this->loadCartSession();

        $this->dispatch('cart-updated');

        session()->flash('success', 'Item added to cart successfully');
    }

    public function processToCheckout()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to proceed to checkout.');
            return redirect()->route('login');
        }

        if (empty($this->cartItems)) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        return redirect()->route('reg-cart');
    }

    public function render()
    {
        return view('livewire.website.registration');
    }
}
