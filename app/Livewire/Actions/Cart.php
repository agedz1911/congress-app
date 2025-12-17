<?php

namespace App\Livewire\Actions;

use App\Models\Currency;
use App\Models\Manage\Coupon;
use App\Models\Registration\Order;
use App\Models\Registration\OrderItem;
use App\Models\Registration\Participant;
use App\Models\Registration\Product;
use App\Models\Registration\RegistrationType;
use App\Models\Registration\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public $participants = [];
    public $selectedParticipantId = null;
    public $hasParticipants = false;

    public $paymentMethod = 'bank_transfer';
    public $selectedParticipantDetails = null;
    public $agreeTerms = false;

    public function mount()
    {
        $this->countries = countries();
        if (!Auth::check()) {
            session()->flash('error', 'Please login to access cart.');
            return redirect()->route('login');
        }

        $this->loadCartFromSession();
        $this->calculateTotals();
        $this->loadUserParticipants();

        if (session()->has('cart_step')) {
            $this->step = session()->get('cart_step');
            session()->forget('cart_step');
        }
        if (session()->has('payment_method')) {
            $this->paymentMethod = session()->get('payment_method');
        }
    }

    public function loadCartFromSession()
    {
        $this->cartItems = session()->get('cart', []);

        $regtypeIds = collect($this->cartItems)->pluck('regtype_id')->unique()->filter();
        $regtypes = RegistrationType::whereIn('id', $regtypeIds)->get()->keyBy('id');

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
            if (isset($item['regtype_id']) && isset($regtypes[$item['regtype_id']])) {
                $this->cartItems[$key]['regtype_name'] = $regtypes[$item['regtype_id']]->name;
            } else {
                $this->cartItems[$key]['regtype_name'] = 'N/A';
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

    public function loadUserParticipants()
    {
        $participantsCollection = Participant::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $this->participants = $participantsCollection->toArray();
        $this->hasParticipants = count($this->participants) > 0;

        if (session()->has('selected_participant_id')) {
            $this->selectedParticipantId = session()->get('selected_participant_id');
        }
    }

    public function loadSelectedParticipantDetails()
    {
        if ($this->selectedParticipantId) {
            $this->selectedParticipantDetails = collect($this->participants)
                ->firstWhere('id', $this->selectedParticipantId);
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
        if (empty($this->cartItems)) {
            session()->flash('error', 'Your cart is empty. Please add items to cart first.');
            return;
        }

        $this->loadUserParticipants();
        $this->step = 2;
    }

    public function redirectToAddParticipant()
    {
        session()->put('return_to_cart_step', 2);
        return redirect()->route('createparticipants');
    }

    public function unselectParticipant()
    {
        $this->selectedParticipantId = null;
        session()->forget('selected_participant_id');
        session()->flash('info', 'Participant selection removed.');
    }

    public function backToParticipant()
    {
        $this->loadUserParticipants();
        $this->step = 2;
    }

    public function continueToPaymentMethod()
    {
        if ($this->hasParticipants && !$this->selectedParticipantId) {
            session()->flash('error', 'Please select a participant to continue.');
            return;
        }

        if ($this->selectedParticipantId) {
            session()->put('selected_participant_id', $this->selectedParticipantId);
        }

        $this->step = 3;
    }

    public function backToPaymentMethod()
    {
        $this->step = 3;
    }

    public function continueToReview()
    {
        if (!$this->paymentMethod) {
            session()->flash('error', 'Please select a payment method.');
            return;
        }

        session()->put('payment_method', $this->paymentMethod);

        $this->loadSelectedParticipantDetails();
        $this->step = 4;
    }

    public function submitOrder()
    {
        if (!$this->agreeTerms) {
            session()->flash('error', 'Please agree to the terms and conditions to continue.');
            return;
        }

        if (empty($this->cartItems)) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        if (!$this->selectedParticipantId) {
            session()->flash('error', 'Please select a participant.');
            return;
        }

        if (!$this->paymentMethod) {
            session()->flash('error', 'Please select a payment method.');
            return;
        }

        DB::beginTransaction();

        try {
            $regCode = $this->generateRegistrationCode();

            $appliedCoupon = session()->get('applied_coupon');
            $couponCode = $appliedCoupon['code'] ?? null;

            $participant = Participant::find($this->selectedParticipantId);

            $currencyRate = $this->calculateKurs($participant);
            $kursValue = $this->total * $currencyRate;

            $order = Order::create([
                'reg_code' => $regCode,
                'participant_id' => $this->selectedParticipantId,
                'total' => $this->total,
                'discount' => $this->discount,
                'coupon' => $couponCode,
                'status' => 'New', // Default status
            ]);

            foreach ($this->cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                ]);

                // Optional: Update product quota
                $product = Product::find($item['product_id']);
                if ($product && $product->quota !== null && $product->quota > 0) {
                    $product->decrement('quota', $item['quantity']);
                }
            }

            $transaction = Transaction::create([
                'order_id' => $order->id,
                'payment_method' => $this->paymentMethod, // Langsung dari property
                'payment_date' => null, // Will be set when payment confirmed
                'payment_status' => 'Unpaid', // Default status
                'amount' => 0,
                'kurs' => $kursValue,
                'attachment' => null, // Will be uploaded by user for bank transfer
            ]);

            if ($couponCode) {
                $coupon = Coupon::where('name', $couponCode)->first();
                if ($coupon && $coupon->isQuotaAvailable()) {
                    $coupon->increment('used_count'); // Increment used_count
                }
            }

            // $this->sendOrderConfirmationEmail($order);
            $this->clearCartAndSessions();
            DB::commit();

            session()->flash('success', 'Order submitted successfully!');

            // session()->forget(['cart', 'applied_coupon', 'selected_participant_id', 'payment_method']);

            // return redirect()->route('myregistrations');
            return redirect()->route('order.detail', ['regCode' => $regCode]);
        } catch (\Exception  $e) {
            DB::rollBack();

            Log::error('Order submission failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'participant_id' => $this->selectedParticipantId,
                'cart_items' => $this->cartItems,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Failed to submit order: ' . $e->getMessage());
        }
    }

    protected function calculateKurs($participant)
    {
        $kursValue = 1;

        if (!$participant) {
            return $kursValue;
        }

        try {
            $countryLower = strtolower(trim($participant->country));

            // Query by label instead of region
            if ($countryLower === 'indonesia') {
                $currency = Currency::where('label', 'IDR')->first();
            } else {
                // For non-Indonesia, use USD
                $currency = Currency::where('label', 'USD')->first();
            }

            if ($currency && $currency->kurs) {
                $kursValue = (float) $currency->kurs;

                // Log::info('Kurs calculated', [
                //     'country' => $participant->country,
                //     'currency_label' => $currency->label,
                //     'kurs' => $kursValue
                // ]);
            }
        } catch (\Exception $e) {
            Log::error('calculateKurs error: ' . $e->getMessage());
        }

        return $kursValue;
    }

    protected function generateRegistrationCode()
    {
        do {
            // Format: REG-YYYYMMDD-XXXXX
            $regCode = 'REG-' . random_int(10000, 99999);
        } while (Order::where('reg_code', $regCode)->exists());

        return $regCode;
    }

    protected function clearCartAndSessions()
    {
        // Clear cart items
        $this->cartItems = [];

        // Clear all cart related sessions
        session()->forget([
            'cart',
            'applied_coupon',
            'selected_participant_id',
            'payment_method',
            'cart_step',
            'return_to_cart_step'
        ]);

        // Reset component properties
        $this->subtotal = 0;
        $this->discount = 0;
        $this->total = 0;
        $this->promoCode = '';
        $this->selectedParticipantId = null;
        $this->selectedParticipantDetails = null;
        $this->paymentMethod = 'Bank Transfer';
        $this->agreeTerms = false;
        $this->step = 1;
    }

    public function render()
    {
        return view('livewire.actions.cart');
    }
}
