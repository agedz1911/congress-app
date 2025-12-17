<div>
    <x-toast type="success" :message="session('success')" :duration="5000" />
    <x-toast type="error" :message="session('error')" />
    <x-toast type="info" :message="session('info')" />
    <div class="breadcrumbs text-sm text-zinc-700 dark:text-zinc-50">
        <ul>
            <li><a href="{{route('dashboard')}}" wire:navigate>Dashboard</a></li>

            <li><a href="{{route('myregistrations')}}" wire:navigate>MyRegistration</a></li>

            <li>Confirmation</li>
        </ul>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Order Summary --}}
        <div class="lg:col-span-1">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fa fa-file-invoice text-primary"></i>
                        Order Summary
                    </h2>

                    {{-- Registration Code --}}
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Registration Code</p>
                        <p class="font-bold text-lg">{{ $order->reg_code }}</p>
                    </div>

                    {{-- Order Status --}}
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Order Status</p>
                        <span
                            class="badge badge-{{ $order->status === 'New' ? 'info' : ($order->status === 'Processing' ? 'warning' : 'success') }}">
                            {{ $order->status }}
                        </span>
                    </div>

                    {{-- Participant --}}
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Participant</p>
                        <p class="font-semibold">{{ $order->participant->first_name }} {{ $order->participant->last_name
                            }}</p>
                        <p class="text-sm">{{ $order->participant->email }}</p>
                    </div>

                    {{-- Order Items --}}
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-2">Items</p>
                        @foreach($order->items as $item)
                        <div class="flex justify-between text-sm py-1">
                            <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                            <span class="font-semibold">{{Auth()->user()->country == 'Indonesia' ? 'IDR' : 'USD'}} {{
                                number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Discount --}}
                    @if($order->discount > 0)
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Discount</span>
                        <span class="text-error">- {{Auth()->user()->country == 'Indonesia' ? 'IDR' : 'USD'}} {{
                            number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    {{-- Total --}}
                    <div class="divider my-2"></div>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-primary">{{Auth()->user()->country == 'Indonesia' ? 'IDR' : 'USD'}} {{
                            number_format($order->total, 0, ',', '.') }}</span>
                    </div>

                    {{-- Kurs Information --}}
                    <div class="mt-4 p-3 bg-base-200 rounded-lg">
                        <p class="text-xs text-gray-500">Amount to Pay (with exchange rate)</p>
                        @if($currencyLabel !== 'IDR')
                        <p class="text-xs text-gray-400 mt-1">
                            Exchange Rate: 1 USD = Rp {{ number_format($currencyRate, 0, ',', '.') }}
                        </p>
                        @endif
                    </div>

                    {{-- Payment Method --}}
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">Payment Method</p>
                        <div class="flex items-center gap-2 mt-1">
                            @if($order->transaction->payment_method === 'Bank Transfer')
                            <i class="fa fa-building-columns text-info"></i>
                            @else
                            <i class="fa fa-credit-card text-success"></i>
                            @endif
                            <span class="font-semibold">{{ $order->transaction->payment_method }}</span>
                        </div>
                    </div>

                    {{-- Bank Account Info (if Bank Transfer) --}}
                    @if($order->transaction->payment_method === 'Bank Transfer')
                    <div class="mt-4 p-4 bg-info/10 rounded-lg border border-info/20">
                        <p class="font-semibold mb-2 text-info">
                            <i class="fa fa-bank"></i> Bank Account Details
                        </p>
                        <div class="space-y-1 text-sm">
                            <p><strong>Bank:</strong> Bank BCA</p>
                            <p><strong>Account Name:</strong> Congress Committee</p>
                            <p><strong>Account Number:</strong> 1234567890</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Payment Confirmation Form --}}
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-6">
                        <i class="fa fa-upload text-primary"></i>
                        {{ $isEditing ? 'Update Payment Confirmation' : 'Upload Payment Confirmation' }}
                    </h2>

                    @if($isEditing)
                    <div class="alert alert-info mb-6">
                        <i class="fa fa-info-circle"></i>
                        <span>You have already submitted payment confirmation. You can update it below.</span>
                    </div>
                    @endif

                    <form wire:submit.prevent="submitPaymentConfirmation">
                        {{-- Payment Date --}}
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    Payment Date <span class="text-error">*</span>
                                </span>
                            </label>
                            <input type="date" wire:model.defer="payment_date"
                                class="input input-bordered w-full @error('payment_date') input-error @enderror"
                                max="{{ date('Y-m-d') }}" />
                            @error('payment_date')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        {{-- Amount --}}
                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    Amount Paid <span class="text-error">*</span>
                                </span>
                            </label>
                            <input type="number" step="0.01" wire:model.defer="amount"
                                class="input input-bordered w-full @error('amount') input-error @enderror"
                                placeholder="Enter amount paid" />
                            @error('amount')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                            <label class="label">
                                <span class="label-text-alt text-gray-500">
                                    Recommended:
                                    @if($currencyLabel === 'IDR')
                                    Rp {{ number_format($order->transaction->kurs, 0, ',', '.') }}
                                    @else
                                    $ {{ number_format($order->transaction->kurs / $currencyRate, 2) }}
                                    @endif
                                </span>
                            </label>
                        </div>

                        {{-- Attachment --}}
                        <div class="form-control w-full mb-6">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    Payment Proof (Image) <span class="text-error">*</span>
                                </span>
                            </label>

                            {{-- Existing Attachment Preview --}}
                            @if($existingAttachment && !$attachment)
                            <div class="mb-4 p-4 bg-base-200 rounded-lg">
                                <p class="text-sm text-gray-600 mb-2">Current Attachment:</p>
                                <img src="{{ Storage::url($existingAttachment) }}" alt="Payment Proof"
                                    class="max-w-xs rounded-lg shadow-md" />
                            </div>
                            @endif

                            {{-- File Input --}}
                            <input type="file" wire:model="attachment"
                                class="file-input file-input-bordered w-full @error('attachment') file-input-error @enderror"
                                accept="image/*" />

                            @error('attachment')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror

                            <label class="label">
                                <span class="label-text-alt text-gray-500">
                                    Accepted formats: JPG, PNG, GIF (Max: 2MB)
                                </span>
                            </label>

                            {{-- Image Preview --}}
                            @if($attachment)
                            <div class="mt-4 relative">
                                <p class="text-sm text-gray-600 mb-2">Preview:</p>
                                <div class="relative inline-block">
                                    <img src="{{ $attachment->temporaryUrl() }}" alt="Preview"
                                        class="max-w-xs rounded-lg shadow-md" />
                                    <button type="button" wire:click="removeAttachment"
                                        class="btn btn-circle btn-sm btn-error absolute top-2 right-2">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endif

                            {{-- Loading Indicator --}}
                            <div wire:loading wire:target="attachment" class="mt-2">
                                <span class="loading loading-spinner loading-sm"></span>
                                <span class="text-sm ml-2">Uploading...</span>
                            </div>
                        </div>

                        {{-- Information Alert --}}
                        <div class="alert alert-warning mb-6">
                            <i class="fa fa-exclamation-triangle"></i>
                            <div>
                                <p class="font-semibold">Important Information:</p>
                                <ul class="text-sm mt-1 list-disc list-inside">
                                    <li>Make sure the payment proof image is clear and readable</li>
                                    <li>Your payment will be verified by admin within 1-2 business days</li>
                                    <li>You will receive email notification once verified</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3 justify-end">
                            <button type="button" wire:click="cancelEdit" class="btn btn-ghost">
                                <i class="fa fa-times"></i>
                                Cancel
                            </button>

                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="submitPaymentConfirmation, attachment">
                                <span wire:loading.remove wire:target="submitPaymentConfirmation">
                                    <i class="fa fa-paper-plane"></i>
                                    {{ $isEditing ? 'Update Confirmation' : 'Submit Confirmation' }}
                                </span>
                                <span wire:loading wire:target="submitPaymentConfirmation">
                                    <span class="loading loading-spinner loading-sm"></span>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>