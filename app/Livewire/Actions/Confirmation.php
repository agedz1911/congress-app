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

    public $regCode = '';
    public $order;
    public $orderFound = false;
    public $payment_date;
    public $amount;
    public $attachment;

    protected $rules = [
        'regCode' => 'required|string|min:3',
        'payment_date' => 'required|date|before_or_equal:today',
        'amount' => 'required|numeric|min:0',
        'attachment' => 'required|image|max:2048',
    ];

    protected $messages = [
        'regCode.required' => 'Registration code is required.',
        'payment_date.required' => 'Payment date is required.',
        'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        'amount.required' => 'Amount is required.',
        'amount.numeric' => 'Amount must be a number.',
        'attachment.required' => 'Payment proof attachment is required.',
        'attachment.image' => 'Attachment must be an image.',
        'attachment.max' => 'Attachment size must not exceed 2MB.',
    ];

    public function searchOrder()
    {
        // Validasi input
        $this->validate([
            'regCode' => 'required|string|min:3'
        ]);

        try {
            // Cari order
            $this->order = Order::with(['participant', 'items.product', 'transaction'])
                ->where('reg_code', strtoupper($this->regCode))
                ->first();

            if (!$this->order) {
                session()->flash('error', 'Registration code not found. Please check and try again.');
                return;
            }

            // Cek apakah sudah submit konfirmasi (attachment ada)
            if ($this->order->transaction->attachment) {
                session()->flash('error', 'You have already submitted payment confirmation for this registration.');
                $this->resetSearch();
                return;
            }

            // Set amount ke total order
            $this->amount = $this->order->total;
            $this->orderFound = true;
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while searching. Please try again.');
        }
    }

    public function submitPaymentConfirmation()
    {
        if (!$this->order) {
            session()->flash('error', 'Order not found. Please search again.');
            return;
        }

        // Validasi form
        $this->validate();

        // Validasi amount harus sama dengan total order
        if ((float)$this->amount !== (float)$this->order->total) {
            session()->flash('error', 'Amount paid must match the order total. Expected: Rp ' . number_format($this->order->total, 0, ',', '.'));
            return;
        }

        DB::beginTransaction();

        try {
            $transaction = $this->order->transaction;

            // Double check apakah sudah ada attachment
            if ($transaction->attachment) {
                DB::rollBack();
                session()->flash('error', 'You have already submitted payment confirmation for this registration.');
                return;
            }

            // Upload attachment
            $extension = $this->attachment->getClientOriginalExtension();
            $filename = $this->order->reg_code . '_' . time() . '.' . $extension;
            $attachmentPath = $this->attachment->storeAs('Payment_Receipt', $filename, 'public');

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
                'payment_status' => 'Unpaid',
            ]);

            // Update order status
            $this->order->update([
                'status' => 'Processing',
            ]);

            DB::commit();

            session()->flash('success', 'Payment confirmation submitted successfully! Your payment is under review.');

            $this->resetSearch();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to submit payment confirmation: ' . $e->getMessage());
        }
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    public function resetSearch()
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.actions.confirmation');
    }
}
