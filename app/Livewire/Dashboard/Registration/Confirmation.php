<?php

namespace App\Livewire\Dashboard\Registration;

use App\Models\Currency;
use App\Models\Registration\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Registration Confirmation - Congress App')]
class Confirmation extends Component
{
    use WithFileUploads;
    public $regCode;
    public $order;

    public $payment_date;
    public $amount;
    public $attachment;

    public $existingAttachment;
    public $isEditing = false;

    public $currencyRate = 1;
    public $currencyLabel = 'IDR';

    protected $rules = [
        'payment_date' => 'required|date|before_or_equal:today',
        'amount' => 'required|numeric|min:0',
        'attachment' => 'required|image|max:2048', // Max 2MB
    ];

    protected $messages = [
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

    public function mount($regCode)
    {
        // Load order dengan relationships
        $this->order = Order::with(['participant', 'items.product', 'transaction'])
            ->where('reg_code', $regCode)
            ->forUser(Auth::id())
            ->firstOrFail();

        // Check if transaction exists
        if (!$this->order->transaction) {
            session()->flash('error', 'Transaction not found for this order.');
            return redirect()->route('myregistrations');
        }

        // Get currency rate based on participant country
        $this->setCurrencyRate();

        // Load existing data if already submitted
        if ($this->order->transaction->payment_date) {
            $this->isEditing = true;
            $this->payment_date = $this->order->transaction->payment_date->format('Y-m-d');
            $this->amount = $this->order->transaction->amount;
            $this->existingAttachment = $this->order->transaction->attachment;
        } else {
            // Pre-fill amount with kurs value
            $this->amount = $this->order->transaction->kurs;
        }
    }

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

    public function getFormattedAmount()
    {
        $kurs = $this->order->transaction->kurs;

        if ($this->currencyLabel === 'IDR') {
            return 'Rp ' . number_format($kurs, 0, ',', '.');
        } else {
            // Convert from IDR to USD
            $amountInUSD = $kurs / $this->currencyRate;
            return '$ ' . number_format($amountInUSD, 0);
        }
    }

    public function updatedAttachment()
    {
        $this->validateOnly('attachment');
    }

    public function submitPaymentConfirmation()
    {
        // Validate form
        $this->validate();

        DB::beginTransaction();

        try {
            $transaction = $this->order->transaction;

            // Upload attachment
            $attachmentPath = null;
            if ($this->attachment) {
                // Delete old attachment if exists
                if ($transaction->attachment && Storage::disk('public')->exists($transaction->attachment)) {
                    Storage::disk('public')->delete($transaction->attachment);
                }

                // Generate unique filename
                $originalName = pathinfo($this->attachment->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $this->attachment->getClientOriginalExtension();
                $filename = $this->order->reg_code . '_' . time() . '.' . $extension;

                // Store new attachment in Payment_Receipt folder
                $attachmentPath = $this->attachment->storeAs('Payment_Receipt', $filename, 'public');
            } elseif ($this->existingAttachment) {
                // Keep existing attachment if no new file uploaded
                $attachmentPath = $this->existingAttachment;
            }

            // Update transaction
            $transaction->update([
                'payment_date' => $this->payment_date,
                'amount' => $this->amount,
                'attachment' => $attachmentPath,
                'payment_status' => 'Unpaid', // Tetap Unpaid sampai admin verify
            ]);

            // Update order status to Processing
            $this->order->update([
                'status' => 'Processing',
            ]);

            DB::commit();

            session()->flash('success', 'Payment confirmation submitted successfully! Please wait for admin verification.');

            return redirect()->route('myregistrations');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'Failed to submit payment confirmation: ' . $e->getMessage());
        }
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    public function cancelEdit()
    {
        return redirect()->route('myregistrations');
    }

    public function render()
    {
        return view('livewire.dashboard.registration.confirmation');
    }
}
