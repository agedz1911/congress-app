<div class="w-full">
    <x-toast type="success" :message="session('success')" :duration="5000" />
    <x-toast type="error" :message="session('error')" />

    {{-- Header Section --}}
    <section class="breadcrumbs relative pb-0">
        <div class="absolute inset-0 bg-gradient-to-b from-[#0059A8]/10 to-[#0059A8]/80"></div>
        <div class="py-16 lg:py-28 text-center relative">
            <h2 class="text-white uppercase text-2xl font-semibold tracking-wide lg:text-4xl">Payment Confirmation</h2>
        </div>
    </section>

    <section class="pt-10 pb-24 px-2 lg:px-5">
        {{-- Search Section --}}
        @if (!$orderFound)
        <div class="max-w-2xl mx-auto mb-12">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-6">
                        <i class="fa fa-search text-primary"></i>
                        Find Your Order
                    </h2>

                    <p class="text-gray-600 mb-6">
                        Enter your registration code to continue with payment confirmation.
                    </p>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">Registration Code <span
                                    class="text-error">*</span></span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" wire:model.defer="regCode" placeholder="e.g., REG-12345"
                                class="input input-bordered flex-1" />
                            <button type="button" wire:click="searchOrder" class="btn btn-primary"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fa fa-search"></i>
                                    Search
                                </span>
                                <span wire:loading>
                                    <span class="loading loading-spinner loading-sm"></span>
                                    Searching...
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info mt-6">
                        <i class="fa fa-info-circle"></i>
                        <span>You can find your registration code in the confirmation email sent to you.</span>
                    </div>
                </div>
            </div>
        </div>

        @else
        {{-- Order Found - Show Details and Form --}}
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
                            <p class="font-semibold">{{ $order->participant->first_name }} {{
                                $order->participant->last_name }}</p>
                            <p class="text-sm">{{ $order->participant->email }}</p>
                            <p class="text-sm">{{ $order->participant->country }}</p>
                        </div>

                        {{-- Order Items --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Items</p>
                            @foreach($order->items as $item)
                            <div class="flex justify-between text-sm py-1">
                                <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                                <span class="font-semibold">{{ $order->participant->country == 'Indonesia' ? 'IDR' :
                                    'USD' }} {{ number_format($item->unit_price * $item->quantity, 0, ',', '.')
                                    }}</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Discount --}}
                        @if($order->discount > 0)
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-500">Discount</span>
                            <span class="text-error">- {{ $order->participant->country == 'Indonesia' ? 'IDR' : 'USD' }}
                                {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        {{-- Total --}}
                        <div class="divider my-2"></div>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-primary">{{ $order->participant->country == 'Indonesia' ? 'IDR' : 'USD' }}
                                {{ number_format($order->total, 0, ',', '.') }}</span>
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

                        {{-- Back Button --}}
                        <button type="button" wire:click="resetSearch" class="btn btn-ghost w-full mt-6">
                            <i class="fa fa-arrow-left"></i>
                            Search Different Order
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right Column: Payment Confirmation Form --}}
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-2">
                            <i class="fa fa-upload text-primary"></i>
                            Upload Payment Confirmation
                        </h2>
                        <p class="text-sm text-gray-600 mb-6">
                            Please fill in the details below and upload your payment proof
                        </p>

                        <form wire:submit.prevent="submitPaymentConfirmation" class="space-y-6">
                            {{-- Payment Date --}}
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold text-base">
                                        <i class="fa fa-calendar-alt text-primary"></i>
                                        Payment Date <span class="text-error">*</span>
                                    </span>
                                </label>
                                <input type="date" wire:model.defer="payment_date"
                                    class="input input-bordered w-full input-lg @error('payment_date') input-error @enderror"
                                    max="{{ date('Y-m-d') }}" />
                                @error('payment_date')
                                <label class="label">
                                    <span class="label-text-alt text-error">
                                        <i class="fa fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                </label>
                                @enderror
                            </div>

                            {{-- Amount --}}
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold text-base">
                                        <i class="fa fa-money-bill-wave text-primary"></i>
                                        Amount Paid <span class="text-error">*</span>
                                    </span>
                                </label>
                                <input type="number" step="0.01" wire:model.defer="amount"
                                    class="input input-bordered w-full input-lg @error('amount') input-error @enderror"
                                    placeholder="Enter amount paid" />
                                @error('amount')
                                <label class="label">
                                    <span class="label-text-alt text-error">
                                        <i class="fa fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                </label>
                                @enderror
                                <label class="label">
                                    <span class="label-text-alt text-gray-600 font-semibold">
                                        <i class="fa fa-check-circle text-success"></i>
                                        Must be: {{ $order->participant->country == 'Indonesia' ? 'IDR' : 'USD' }} {{
                                        number_format($order->total, 0, ',', '.') }}
                                    </span>
                                </label>
                            </div>

                            {{-- Attachment --}}
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold text-base">
                                        <i class="fa fa-image text-primary"></i>
                                        Payment Proof (Image) <span class="text-error">*</span>
                                    </span>
                                </label>

                                {{-- File Input --}}
                                <div class="form-control">
                                    <label
                                        class="label w-full cursor-pointer border-2 border-dashed border-primary/30 rounded-lg p-6 hover:border-primary/60 transition-colors">
                                        <div class="text-center w-full">
                                            <i class="fa fa-cloud-upload-alt text-4xl text-primary mb-3 block"></i>
                                            <span class="text-base font-semibold text-gray-700">Click to upload or drag
                                                and drop</span>
                                            <p class="text-sm text-gray-500 mt-1">JPG, PNG, or GIF (Max 2MB)</p>
                                        </div>
                                        <input type="file" wire:model="attachment" class="hidden" accept="image/*" />
                                    </label>
                                </div>

                                @error('attachment')
                                <label class="label">
                                    <span class="label-text-alt text-error">
                                        <i class="fa fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                </label>
                                @enderror

                                {{-- Image Preview --}}
                                @if($attachment)
                                <div class="mt-6">
                                    <p class="text-sm text-gray-700 mb-3 font-semibold flex items-center gap-2">
                                        <i class="fa fa-eye text-primary"></i>
                                        Preview
                                    </p>
                                    <div class="relative inline-block">
                                        <img src="{{ $attachment->temporaryUrl() }}" alt="Preview"
                                            class="max-w-sm rounded-lg shadow-lg border-2 border-primary/20" />
                                        <button type="button" wire:click="removeAttachment"
                                            class="btn btn-circle btn-error btn-sm absolute -top-3 -right-3 shadow-lg"
                                            title="Remove image">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif

                                {{-- Loading Indicator --}}
                                <div wire:loading wire:target="attachment"
                                    class="mt-4 flex items-center gap-2 p-3 bg-info/10 rounded-lg">
                                    <span class="loading loading-spinner loading-sm text-info"></span>
                                    <span class="text-sm text-info font-semibold">Uploading image...</span>
                                </div>
                            </div>

                            {{-- Information Alert --}}
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle text-lg"></i>
                                <div>
                                    <p class="font-semibold">Important Information:</p>
                                    <ul class="text-sm mt-2 space-y-1 ml-4">
                                        <li><i class="fa fa-check text-success"></i> Make sure the payment proof image
                                            is clear and readable</li>
                                        <li><i class="fa fa-check text-success"></i> Your payment will be verified by
                                            admin within 1-2 business days</li>
                                        <li><i class="fa fa-check text-success"></i> You will receive email notification
                                            once verified</li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex gap-3 justify-end pt-6 border-t">
                                <button type="button" wire:click="resetSearch" class="btn btn-outline gap-2">
                                    <i class="fa fa-search"></i>
                                    Search Again
                                </button>

                                <button type="submit" class="btn btn-primary gap-2" wire:loading.attr="disabled"
                                    wire:target="submitPaymentConfirmation, attachment">
                                    <span wire:loading.remove wire:target="submitPaymentConfirmation">
                                        <i class="fa fa-paper-plane"></i>
                                        Submit Confirmation
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
            @endif
    </section>

    {{-- Footer Support Section --}}
    <section class="bg-base-200 py-12 px-2 lg:px-5">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="avatar placeholder mb-4">
                        <div class="bg-primary text-white rounded-full w-16">
                            <span class="text-2xl"><i class="fa fa-headset"></i></span>
                        </div>
                    </div>
                    <h3 class="font-semibold mb-2">Customer Support</h3>
                    <p class="text-sm text-gray-600">Need help? Contact our support team</p>
                    <a href="mailto:support@congress.com"
                        class="link link-primary text-sm mt-2">support@congress.com</a>
                </div>

                <div class="text-center">
                    <div class="avatar placeholder mb-4">
                        <div class="bg-secondary text-white rounded-full w-16">
                            <span class="text-2xl"><i class="fa fa-phone"></i></span>
                        </div>
                    </div>
                    <h3 class="font-semibold mb-2">Phone Support</h3>
                    <p class="text-sm text-gray-600">Call us during business hours</p>
                    <a href="tel:+621234567890" class="link link-secondary text-sm mt-2">+62 (123) 456-7890</a>
                </div>

                <div class="text-center">
                    <div class="avatar placeholder mb-4">
                        <div class="bg-accent text-white rounded-full w-16">
                            <span class="text-2xl"><i class="fa fa-envelope"></i></span>
                        </div>
                    </div>
                    <h3 class="font-semibold mb-2">Email Support</h3>
                    <p class="text-sm text-gray-600">We'll respond within 24 hours</p>
                    <a href="mailto:info@congress.com" class="link link-accent text-sm mt-2">info@congress.com</a>
                </div>
            </div>

            <div class="divider my-8"></div>

            <div class="text-center">
                <p class="text-sm text-gray-600 mb-4">
                    <i class="fa fa-shield-alt text-primary mr-2"></i>
                    Your payment information is secure and encrypted
                </p>
                <p class="text-xs text-gray-500">
                    © 2024 Congress Registration System. All rights reserved.
                </p>
            </div>
        </div>
    </section>
</div>