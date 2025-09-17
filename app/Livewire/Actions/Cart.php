<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.website')]
#[Title('Cart - Congress App')]
class Cart extends Component
{
    public $step = 1;
    public $countries;
    public $cartItems = [];
    public $subtotal = 0;
    public $discount = 0;
    public $total = 0;

    public function mount()
    {
        $this->countries = countries();
        if (!Auth::check()) {
            session()->flash('error', 'Please login to access cart.');
            return redirect()->route('login');
        }

        $this->loadCartFromSession();
        $this->calculateTotals();
    }

    public function loadCartFromSession()
    {
        $this->cartItems = session()->get('cart', []);

        // Migrate old cart structure to new one
        foreach ($this->cartItems as $key => $item) {
            if (isset($item['price_idr'])) {
                // Old structure, migrate to new
                $priceType = $item['price_type'] ?? 'early_bird';
                $currency = 'IDR'; // Default to IDR for old items
                $price = $item['price_idr'];

                $this->cartItems[$key] = [
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'regtype_id' => $item['regtype_id'],
                    'price' => $price,
                    'currency' => $currency,
                    'price_type' => $priceType,
                    'quantity' => $item['quantity'] ?? 1,
                ];
            }
        }

        // Save migrated cart back to session
        session()->put('cart', $this->cartItems);

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cartItems as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }
        $this->total = $this->subtotal - $this->discount;
    }

    public function removeFromCart($cartKey)
    {
        if (isset($this->cartItems[$cartKey])) {
            unset($this->cartItems[$cartKey]);
            session()->put('cart', $this->cartItems);
            $this->calculateTotals();

            // Dispatch event to update cart count in other components
            $this->dispatch('cart-updated');

            session()->flash('success', 'Item removed from cart successfully!');
        }
    }

    public function backToRegistration()
    {
        return redirect()->route('registration');
    }

    public function backToOrderSummary()
    {
        $this->step = 1;
    }

    public function continueToParticipant()
    {

        $this->step = 2;
    }

    public function backToParticipant()
    {

        $this->step = 2;
    }

    public function continueToPaymentMethod()
    {

        $this->step = 3;
    }

    public function backToPaymentMethod()
    {
        $this->step = 3;
    }

    public function continueToReview()
    {

        $this->step = 4;
    }

    public function render()
    {
        return view('livewire.actions.cart');
    }
}
