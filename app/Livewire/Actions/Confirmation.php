<?php

namespace App\Livewire\Actions;

use App\Models\Currency;
use App\Models\Registration\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Payment Confirmation')]
#[Layout('components.layouts.website')]
class Confirmation extends Component
{
    use WithFileUploads;

    // Search field
    public $regCode = '';
    public $order;
    public $orderFound = false;

    // Form fields
    public $payment_date;
    public $amount;
    public $attachment;

    // For preview
    public $existingAttachment;
    public $isEditing = false;
    public $isConfirmed = false; // Status konfirmasi sudah selesai
    public $canSubmit = true;

    // Currency rate
    public $currencyRate = 1;
    public $currencyLabel = 'IDR';

    // Error handling
    public $searchError = '';

    protected $rules = [
        'regCode' => 'required|string|min:3',
        'payment_date' => 'required|date|before_or_equal:today',
        'amount' => 'required|numeric|min:0',
        'attachment' => 'required|image|max:2048',
    ];

    protected $messages = [
        'regCode.required' => 'Registration code is required.',
        'regCode.string' => 'Registration code must be a string.',
        'regCode.min' => 'Registration code must be at least 3 characters.',
        'payment_date.required' => 'Payment date is required.',
        'payment_date.date' => 'Payment date must be a valid date.',
        'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        'amount.required' => 'Amount is required.',
        'amount.numeric' => 'Amount must be a number.',
        'amount.min' => 'Amount must be greater than or equal to 0.',
        'attachment.required' => 'Payment proof attachment is required.',
        'attachment.image' => 'Attachment must be an image.',
        'attachment.max' => 'Attachment size must not exceed 2MB.',
    ];

    public function searchOrder()
    {
        // Reset error
        $this->searchError = '';
        $this->canSubmit = true;

        // Validate reg code
        $this->validate([
            'regCode' => 'required|string|min:3'
        ]);

        try {
            // Search order by registration code (without authentication check)
            $this->order = Order::with(['participant', 'items.product', 'transaction'])
                ->where('reg_code', strtoupper($this->regCode))
                ->first();

            if (!$this->order) {
                $this->searchError = 'Registration code not found. Please check and try again.';
                $this->orderFound = false;
                $this->canSubmit = false;
                return;
            }

            // Check if transaction exists
            if (!$this->order->transaction) {
                $this->searchError = 'Transaction not found for this registration.';
                $this->orderFound = false;
                $this->canSubmit = false;
                return;
            }

            // CEK APAKAH SUDAH PERNAH SUBMIT KONFIRMASI

            $hasSubmittedBefore = $this->order->transaction->payment_date !== null &&
                $this->order->transaction->attachment !== null;

            if ($hasSubmittedBefore) {
                // Jika sudah submit sebelumnya, tidak boleh submit lagi
                $this->searchError = 'This registration has already submitted payment confirmation. You cannot submit again. Please contact support if you need to make changes.';
                $this->isConfirmed = true;
                $this->canSubmit = false;
                $this->orderFound = true;

                // Load data untuk display only
                $this->loadOrderData();


                return;
            }

            // CEK STATUS PEMBAYARAN
            if (
                $this->order->transaction->payment_status === 'Verified' ||
                $this->order->transaction->payment_status === 'Approved'
            ) {
                $this->searchError = 'This order has already been verified. No further confirmation is needed.';
                $this->isConfirmed = true;
                $this->canSubmit = false;
                $this->orderFound = true;

                $this->loadOrderData();

                return;
            }

            // CEK STATUS ORDER
            if ($this->order->status === 'Completed' || $this->order->status === 'Rejected') {
                $this->searchError = 'This order cannot be modified. Status: ' . $this->order->status . '. Please contact support for assistance.';
                $this->isConfirmed = true;
                $this->canSubmit = false;
                $this->orderFound = true;

                $this->loadOrderData();

                return;
            }

            // Get currency rate based on participant country
            $this->setCurrencyRate();

            // FORM PERTAMA KALI DIAKSES - PRE-FILL AMOUNT
            $this->amount = $this->order->transaction->kurs;

            $this->orderFound = true;
            $this->isConfirmed = false;
            $this->canSubmit = true;
            $this->isEditing = false;
        } catch (\Exception $e) {
            $this->searchError = 'An error occurred while searching. Please try again.';
            $this->orderFound = false;
            $this->canSubmit = false;
        }
    }

    /**
     * Load order data untuk display only
     */
    protected function loadOrderData()
    {
        $this->setCurrencyRate();

        if ($this->order->transaction->payment_date) {
            $this->payment_date = $this->order->transaction->payment_date->format('Y-m-d');
        }

        if ($this->order->transaction->amount) {
            $this->amount = $this->order->transaction->amount;
        } else {
            $this->amount = $this->order->transaction->kurs;
        }

        if ($this->order->transaction->attachment) {
            $this->existingAttachment = $this->order->transaction->attachment;
        }
    }

    /**
     * Set currency rate based on participant country
     */
    protected function setCurrencyRate()
    {
        $countryLower = strtolower(trim($this->order->participant->country ?? ''));

        try {
            if ($countryLower === 'indonesia') {
                $currency = Currency::where('label', 'IDR')->first();
            } else {
                $currency = Currency::where('label', 'USD')->first();
            }

            if ($currency && $currency->kurs) {
                $this->currencyRate = (float) $currency->kurs;
                $this->currencyLabel = $currency->label;
            } else {
                // Fallback to default
                $this->currencyRate = $countryLower === 'indonesia' ? 1 : 17000;
                $this->currencyLabel = $countryLower === 'indonesia' ? 'IDR' : 'USD';
            }
        } catch (\Exception $e) {
            // Fallback on error
            $this->currencyRate = $countryLower === 'indonesia' ? 1 : 17000;
            $this->currencyLabel = $countryLower === 'indonesia' ? 'IDR' : 'USD';
        }
    }

    /**
     * Get formatted amount based on currency
     */
    public function getFormattedAmount()
    {
        if (!$this->order) {
            return '';
        }

        $kurs = $this->order->transaction->kurs;

        if ($this->currencyLabel === 'IDR') {
            return 'Rp ' . number_format($kurs, 0, ',', '.');
        } else {
            // Convert from IDR to USD
            $amountInUSD = $kurs / $this->currencyRate;
            return '$ ' . number_format($amountInUSD, 2);
        }
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusBadgeClass()
    {
        $status = $this->order->transaction->payment_status ?? 'Unpaid';

        return match ($status) {
            'Verified', 'Approved' => 'badge-success',
            'Processing' => 'badge-info',
            'Rejected' => 'badge-error',
            default => 'badge-warning'
        };
    }

    /**
     * Get payment status badge text
     */
    public function getPaymentStatusText()
    {
        return $this->order->transaction->payment_status ?? 'Unpaid';
    }

    public function updatedAttachment()
    {
        $this->validateOnly('attachment');
    }

    public function submitPaymentConfirmation()
    {
        if (!$this->order) {
            session()->flash('error', 'Order not found. Please search again.');
            return;
        }

        // Cek apakah sudah submit sebelumnya
        $hasSubmittedBefore = $this->order->transaction->payment_date !== null &&
            $this->order->transaction->attachment !== null;

        if ($hasSubmittedBefore) {
            session()->flash('error', 'This registration has already submitted payment confirmation. You cannot submit again.');
            $this->canSubmit = false;

            return;
        }

        // Cek status pembayaran
        if (
            $this->order->transaction->payment_status === 'Verified' ||
            $this->order->transaction->payment_status === 'Approved'
        ) {
            session()->flash('error', 'This order has already been verified. No further confirmation is needed.');
            $this->canSubmit = false;

            return;
        }

        // Cek status order
        if ($this->order->status === 'Completed' || $this->order->status === 'Rejected') {
            session()->flash('error', 'This order cannot be modified. Current status: ' . $this->order->status);
            $this->canSubmit = false;

            return;
        }

        // Validate form
        $this->validate([
            'payment_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:0',
            'attachment' => 'required|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $transaction = $this->order->transaction;

            // Double check sebelum save
            $checkSubmitted = $transaction->payment_date !== null && $transaction->attachment !== null;

            if ($checkSubmitted) {
                DB::rollBack();
                session()->flash('error', 'This registration has already submitted payment confirmation. Operation cancelled.');
                $this->canSubmit = false;

                return;
            }

            // Upload attachment
            $attachmentPath = null;
            if ($this->attachment) {
                // Delete old attachment if exists (dari edit)
                if ($transaction->attachment && Storage::disk('public')->exists($transaction->attachment)) {
                    Storage::disk('public')->delete($transaction->attachment);
                }

                // Generate unique filename
                $extension = $this->attachment->getClientOriginalExtension();
                $filename = $this->order->reg_code . '_' . time() . '.' . $extension;

                // Store new attachment in Payment_Receipt folder
                $attachmentPath = $this->attachment->storeAs('Payment_Receipt', $filename, 'public');
            }

            if (!$attachmentPath) {
                DB::rollBack();
                session()->flash('error', 'Failed to upload attachment. Please try again.');

                return;
            }

            // Update transaction
            $transaction->update([
                'payment_date' => $this->payment_date,
                'amount' => $this->amount,
                'attachment' => $attachmentPath,
                'payment_status' => 'Processing', // Set ke Processing untuk di-review admin
            ]);

            // Update order status to Processing
            $this->order->update([
                'status' => 'Processing',
            ]);


            DB::commit();

            session()->flash('success', 'Payment confirmation submitted successfully! Your payment is under review. You will receive an email notification once verified.');

            // Reset form dan state
            $this->reset(['regCode', 'payment_date', 'amount', 'attachment', 'orderFound', 'order', 'isConfirmed', 'canSubmit']);
            $this->canSubmit = true;
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'Failed to submit payment confirmation: ' . $e->getMessage());
            $this->canSubmit = false;
        }
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    public function resetSearch()
    {
        $this->reset([
            'regCode',
            'payment_date',
            'amount',
            'attachment',
            'orderFound',
            'order',
            'searchError',
            'isConfirmed',
            'existingAttachment',
            'isEditing',
            'canSubmit'
        ]);
        $this->canSubmit = true;
    }

    public function render()
    {
        return view('livewire.actions.confirmation');
    }
}
