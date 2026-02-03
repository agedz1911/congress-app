<?php

namespace App\Livewire\Actions;

use App\Models\Accommodation\Booking;
use App\Models\Accommodation\BookingTransaction;
use App\Models\Manage\Coupon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


#[Title('Checkout Hotel')]
#[Layout('components.layouts.website')]
class CheckoutHotel extends Component
{
    public $cartItems = [];
    public $totalAmount = 0;
    public $currency = 'IDR';

    public $checkInDate;
    public $checkOutDate;
    public $participantId;
    public $paymentMethod = 'Bank Transfer';
    public $notes;

    public $couponCode = ''; // Property untuk input coupon code
    public $appliedCoupon = null; // Property untuk menyimpan coupon yang diterapkan
    public $discount = 0; // Property untuk menyimpan discount amount
    public $discountType = null; // 'percent' atau 'fixed'
    public $discountPercentage = 0; // Untuk display percentage
    public $finalTotal = 0; // Total setelah discount

    protected $rules = [
        'checkInDate' => 'required|date|after_or_equal:today',
        'checkOutDate' => 'required|date|after:checkInDate',
        'participantId' => 'required|exists:participants,id',
        'paymentMethod' => 'required|in:transfer,credit_card,e_wallet',
        'couponCode' => 'nullable|string|max:50',
    ];

    public function mount()
    {
        $cart = session()->get('hotel_cart', []);

        if (empty($cart)) {
            return redirect()->route('accommodation');
        }

        $this->cartItems = $cart;
        $this->calculateTotals();

        if (session()->has('applied_coupon')) {
            $this->couponCode = session('applied_coupon.code');
            $this->applyCoupon();
        }
    }

    public function applyCoupon()
    {
        // Validasi input
        $this->validate([
            'couponCode' => 'required|string|max:50'
        ]);

        if (empty($this->couponCode)) {
            session()->flash('error', 'Please enter a coupon code');
            return;
        }

        // Cari coupon di database
        $coupon = Coupon::where('name', $this->couponCode)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            session()->flash('error', 'Invalid coupon code');
            $this->resetCoupon();
            return;
        }

        // Validasi: Coupon masih aktif (tanggal)
        if (!$coupon->isCurrentlyActive()) {
            session()->flash('error', 'This coupon is not active or has expired');
            $this->resetCoupon();
            return;
        }

        // Validasi: Quota masih tersedia
        if (!$coupon->isQuotaAvailable()) {
            session()->flash('error', 'This coupon has reached its usage limit');
            $this->resetCoupon();
            return;
        }

        // Validasi: Minimum purchase (opsional - bisa ditambahkan di model)
        if ($this->totalAmount <= 0) {
            session()->flash('error', 'Cannot apply coupon with zero amount');
            $this->resetCoupon();
            return;
        }

        // Hitung discount
        $discountAmount = $coupon->computeDiscountForSubtotal($this->totalAmount);

        if ($discountAmount <= 0) {
            session()->flash('error', 'This coupon cannot be applied to your order');
            $this->resetCoupon();
            return;
        }

        // Terapkan coupon
        $this->appliedCoupon = $coupon;
        $this->discount = $discountAmount;
        $this->discountType = $coupon->type;
        $this->discountPercentage = $coupon->type === 'percent' ? $coupon->nominal : 0;
        $this->finalTotal = $this->totalAmount - $this->discount;

        // Simpan coupon info ke session untuk persistensi
        session()->put('applied_coupon', [
            'code' => $coupon->name,
            'discount' => $discountAmount,
            'type' => $coupon->type,
            'percentage' => $coupon->type === 'percent' ? $coupon->nominal : 0,
        ]);

        session()->flash('success', 'Coupon applied successfully! You saved ' . $this->currency . ' ' . number_format($discountAmount));
    }

    public function removeCoupon()
    {
        $this->resetCoupon();
        session()->forget('applied_coupon');
        session()->flash('success', 'Coupon removed successfully');
    }

    private function resetCoupon()
    {
        $this->appliedCoupon = null;
        $this->discount = 0;
        $this->discountType = null;
        $this->discountPercentage = 0;
        $this->finalTotal = $this->totalAmount;
    }

    private function calculateTotals()
    {
        // Hitung total berdasarkan jumlah malam
        if ($this->checkInDate && $this->checkOutDate) {
            $checkIn = \Carbon\Carbon::parse($this->checkInDate);
            $checkOut = \Carbon\Carbon::parse($this->checkOutDate);
            $totalNights = $checkIn->diffInDays($checkOut);
        } else {
            $totalNights = 1; // Default 1 malam
        }

        // Hitung total amount untuk semua item di cart
        $this->totalAmount = collect($this->cartItems)->sum(function ($item) use ($totalNights) {
            return $item['price'] * $totalNights;
        });

        // Set currency dari item pertama
        if (!empty($this->cartItems)) {
            $this->currency = $this->cartItems[0]['currency'];
        }

        // Hitung final total dengan discount
        if ($this->appliedCoupon) {
            $this->finalTotal = $this->totalAmount - $this->discount;
        } else {
            $this->finalTotal = $this->totalAmount;
        }
    }

    public function updatedCheckInDate()
    {
        $this->calculateTotals();

        // Jika ada coupon yang applied, recalculate discount
        if ($this->appliedCoupon) {
            $this->applyCoupon();
        }
    }

    public function updatedCheckOutDate()
    {
        $this->calculateTotals();

        // Jika ada coupon yang applied, recalculate discount
        if ($this->appliedCoupon) {
            $this->applyCoupon();
        }
    }

    public function processBooking()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Hitung total night
            $checkIn = \Carbon\Carbon::parse($this->checkInDate);
            $checkOut = \Carbon\Carbon::parse($this->checkOutDate);
            $totalNights = $checkIn->diffInDays($checkOut);

            foreach ($this->cartItems as $item) {
                // Hitung subtotal berdasarkan jumlah malam
                $subtotal = $item['price'] * $totalNights;

                // Hitung discount untuk item ini (proportional)
                if ($this->appliedCoupon && $this->discount > 0) {
                    // Distribute discount secara proportional ke setiap item
                    $itemRatio = $subtotal / $this->totalAmount;
                    $itemDiscount = $this->discount * $itemRatio;
                } else {
                    $itemDiscount = 0;
                }

                $total = $subtotal - $itemDiscount;

                // Create booking
                $booking = Booking::create([
                    'booking_code' => $item['booking_code'],
                    'hotel_id' => $item['hotel_id'],
                    'room_id' => $item['room_id'],
                    'participant_id' => $this->participantId,
                    'check_in_date' => $this->checkInDate,
                    'check_out_date' => $this->checkOutDate,
                    'total_night' => $totalNights,
                    'coupon' => $this->couponCode ?: null,
                    'discount' => $itemDiscount,
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'status' => 'pending',
                ]);

                // Create booking transaction (pending payment)
                BookingTransaction::create([
                    'booking_id' => $booking->id,
                    'payment_method' => $this->paymentMethod,
                    'payment_date' => null,
                    'payment_status' => 'pending',
                    'amount' => $total,
                    'attachment' => null,
                    'kurs' => $this->currency === 'USD' ? 15000 : 1, // Adjust kurs
                ]);
            }

            // Update coupon used count jika coupon digunakan
            if ($this->appliedCoupon) {
                $this->appliedCoupon->increment('used_count');
            }

            DB::commit();

            // Clear cart dan coupon session
            session()->forget('hotel_cart');
            session()->forget('applied_coupon');

            session()->flash('success', 'Booking created successfully! Please complete your payment.');

            return redirect()->route('mybookings');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating booking: ' . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.actions.checkout-hotel');
    }
}
