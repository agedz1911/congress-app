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
    public $orderId;
    public $payment_date;
    public $amount;
    public $attachment;
    public $existingAttachment;
    public $isEditing = false;

    // Order data (primitives only)
    public $orderRegCode;
    public $orderStatus;
    public $orderTotal;
    public $orderDiscount;
    public $participantName;
    public $participantCountry;
    public $participantEmail;
    public $paymentMethod;
    public $orderItems = [];

    protected $rules = [
        'payment_date' => 'required|date|before_or_equal:today',
        'amount' => 'required|numeric|min:0',
        'attachment' => 'required|image|max:2048',
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
        // Load order
        $order = Order::with(['participant', 'items.product', 'transaction'])
            ->where('reg_code', $regCode)
            ->forUser(Auth::id())
            ->firstOrFail();

        // Check if transaction exists
        if (!$order->transaction) {
            session()->flash('error', 'Transaction not found for this order.');
            return redirect()->route('myregistrations');
        }

        // Store order ID for later use
        $this->orderId = $order->id;
        $this->orderRegCode = $order->reg_code;
        $this->orderStatus = $order->status;
        $this->orderTotal = $order->total;
        $this->orderDiscount = $order->discount;
        $this->participantName = $order->participant->first_name . ' ' . $order->participant->last_name;
        $this->participantEmail = $order->participant->email;
        $this->participantCountry = $order->participant->country;
        $this->paymentMethod = $order->transaction->payment_method;

        // Store order items
        $this->orderItems = $order->items->map(function ($item) {
            return [
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->unit_price * $item->quantity
            ];
        })->toArray();

        // Load existing data if already submitted
        if ($order->transaction->payment_date) {
            $this->isEditing = true;
            $this->payment_date = $order->transaction->payment_date->format('Y-m-d');
            $this->amount = $order->transaction->amount;
            $this->existingAttachment = $order->transaction->attachment;
        } else {
            // Pre-fill amount dengan total order
            $this->amount = $order->total;
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

        // Validasi amount harus sama dengan total order
        if ((float)$this->amount !== (float)$this->orderTotal) {
            session()->flash('error', 'Amount paid must match the order total. Expected: Rp ' . number_format($this->orderTotal, 0, ',', '.'));
            return;
        }

        DB::beginTransaction();

        try {
            $order = Order::findOrFail($this->orderId);
            $transaction = $order->transaction;

            // Upload attachment
            $attachmentPath = null;
            if ($this->attachment) {
                // Delete old attachment if exists
                if ($transaction->attachment && Storage::disk('public')->exists($transaction->attachment)) {
                    Storage::disk('public')->delete($transaction->attachment);
                }

                // Generate unique filename
                $extension = $this->attachment->getClientOriginalExtension();
                $filename = $order->reg_code . '_' . time() . '.' . $extension;

                // Store new attachment
                $attachmentPath = $this->attachment->storeAs('Payment_Receipt', $filename, 'public');
            } elseif ($this->existingAttachment) {
                // Keep existing attachment if no new file uploaded
                $attachmentPath = $this->existingAttachment;
            }

            // Update transaction dengan amount yang sudah disesuaikan
            $transaction->update([
                'payment_date' => $this->payment_date,
                'amount' => $this->amount,
                'attachment' => $attachmentPath,
                'payment_status' => 'UnPaid', // Status Processing sampai admin verify
                // kurs tetap tersimpan dari sebelumnya
            ]);

            // Update order status to Processing
            $order->update([
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
