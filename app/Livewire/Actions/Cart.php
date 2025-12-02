<?php

namespace App\Livewire\Actions;

use App\Models\Manage\Coupon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// #[Layout('components.layouts.website')]
#[Title('Cart - Congress App')]
class Cart extends Component
{
    public $step = 1;
    public $countries;
    public $cartItems = [];
    public $subtotal = 0;
    public $discount = 0;
    public $promoCode = '';
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
        $this->discount = min((float) $this->discount, (float) $this->subtotal);
        $this->total = $this->subtotal - $this->discount;
    }

    public function updateQuantity($cartKey, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($cartKey);
            return;
        }

        if (isset($this->cartItems[$cartKey])) {
            $this->cartItems[$cartKey]['quantity'] = $quantity;
            session()->put('cart', $this->cartItems);
            $this->calculateTotals();

            // Dispatch event to update cart count in other components
            $this->dispatch('cart-updated');

            session()->flash('success', 'Quantity updated successfully!');
        }
    }

    public function removeFromCart($cartKey)
    {
        if (isset($this->cartItems[$cartKey])) {
            unset($this->cartItems[$cartKey]);
            session()->put('cart', $this->cartItems);

            $this->recomputeDiscountIfCouponExists();
            $this->calculateTotals();

            // Dispatch event to update cart count in other components
            $this->dispatch('cart-updated');

            session()->flash('success', 'Item removed from cart successfully!');
        }
    }

    public function applyPromoCode()
    {
        $this->promoCode = trim($this->promoCode ?? '');

        if (blank($this->promoCode)) {
            $this->clearPromoCode();
            session()->flash('info', 'Kupon dihapus. Diskon direset ke 0');
            return;
        }

        $coupon = Coupon::Where('name', $this->promoCode)->first();
        if (!$coupon) {
            $this->discount = 0;
            session()->flash('error', 'Kode kupon tidak valid.');
            $this->calculateTotals();
            return;
        }

        if (!$coupon->isCurrentlyActive()) {
            $this->discount = 0;
            session()->flash('error', 'Kupon belum aktif, sudah berakhir, atau dinonaktifkan.');
            $this->calculateTotals();
            return;
        }

        if (!$coupon->isQuotaAvailable()) {
            $this->discount = 0;
            session()->flash('error', 'Kuota penggunaan kupon ini telah habis.');
            $this->calculateTotals();
            return;
        }

        $calculatedDiscount = $coupon->computeDiscountForSubtotal($this->subtotal);
        $this->discount = min($calculatedDiscount, $this->subtotal);

        session()->put('applied_coupon', [
            'code'     => $coupon->name,
            'type'     => $coupon->type,
            'nominal'  => $coupon->nominal,
            'discount' => $this->discount,
        ]);

        $quotaInfo = is_null($coupon->remainingQuota())
            ? 'Kuota: unlimited'
            : 'Sisa kuota: ' . $coupon->remainingQuota();

        $label = $coupon->type === 'percent'
            ? 'Diskon ' . rtrim(rtrim(number_format($coupon->nominal, 2), '0'), '.') . '% diterapkan.'
            : 'Diskon ' . number_format($coupon->nominal, 2) . ' diterapkan.';

        session()->flash('success', $label . ' ' . $quotaInfo);
        $this->calculateTotals();
    }

    public function clearPromoCode()
    {
        $this->promoCode = '';
        $this->discount = 0;
        session()->forget('applied_coupon');
        $this->calculateTotals();
        session()->flash('info', 'Kupon dihapus. Diskon direset ke 0.');
    }

    protected function recomputeDiscountIfCouponExists()
    {
        $applied = session()->get('applied_coupon');

        if (!$applied || blank($applied['code'] ?? null)) {
            $this->discount = 0;
            return;
        }

        // Jika subtotal berubah, hitung ulang nilai diskon kupon yang sama
        $coupon = Coupon::where('name', $applied['code'])->first();

        if (!$coupon || !$coupon->isCurrentlyActive() || !$coupon->isQuotaAvailable()) {
            // Jika kupon tidak lagi valid, hapus
            $this->discount = 0;
            session()->forget('applied_coupon');
            session()->flash('info', 'Kupon tidak lagi berlaku dan telah dihapus.');
            return;
        }

        $newDiscount = (float) $coupon->computeDiscountForSubtotal($this->subtotal);
        $this->discount = min($newDiscount, $this->subtotal);

        // Update session
        session()->put('applied_coupon', [
            'code'     => $coupon->name,
            'type'     => $coupon->type,
            'nominal'  => $coupon->nominal,
            'discount' => $this->discount,
        ]);
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
