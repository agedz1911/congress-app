<div class="w-full">
    <x-toast type="success" :message="session('success')" :duration="5000" />
    <x-toast type="error" :message="session('error')" />
    <x-toast type="info" :message="session('info')" />

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
                                class="input input-bordered flex-1 @if($searchError) input-error @endif" />
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

                        @if ($searchError)
                        <label class="label">
                            <span class="label-text-alt text-error">
                                <i class="fa fa-exclamation-circle"></i>
                                {{ $searchError }}
                            </span>
                        </label>
                        @endif
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
                        </div>

                        {{-- Order Items --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Items</p>
                            @foreach($order->items as $item)
                            <div class="flex justify-between text-sm py-1">
                                <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                                <span class="font-semibold">Rp {{ number_format($item->unit_price * $item->quantity, 0,
                                    ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Discount --}}
                        @if($order->discount > 0)
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-500">Discount</span>
                            <span class="text-error">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        {{-- Total --}}
                        <div class="divider my-2"></div>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>

                        {{-- Kurs Information --}}
                        <div class="mt-4 p-3 bg-base-200 rounded-lg">
                            <p class="text-xs text-gray-500">Amount to Pay (with exchange rate)</p>
                            <p class="font-bold text-lg">
                                {{ $this->getFormattedAmount() }}
                            </p>
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
                        {{-- Jika sudah verified/approved atau completed --}}
                        @if($isConfirmed || $order->transaction->payment_status === 'Verified' ||
                        $order->transaction->payment_status === 'Approved' ||
                        $order->status === 'Completed' || $order->status === 'Rejected')

                        <h2 class="card-title text-2xl mb-6">
                            <i class="fa fa-lock text-error"></i>
                            Order Confirmation Locked
                        </h2>

                        {{-- Status Alert --}}
                        <div class="alert alert-error mb-6">
                            <i class="fa fa-exclamation-circle text-xl"></i>
                            <div>
                                <p class="font-semibold text-lg">This order cannot be modified</p>
                                <p class="text-sm mt-1">Payment status: <span class="font-bold">{{
                                        $order->transaction->payment_status ?? 'Unpaid' }}</span></p>
                                <p class="text-sm">Order status: <span class="font-bold">{{ $order->status }}</span></p>
                            </div>
                        </div>

                        {{-- Display Current Information --}}
                        <div class="space-y-4">
                            <div class="divider">Current Information</div>

                            {{-- Payment Date --}}
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Payment Date</span>
                                </label>
                                <div class="input input-bordered w-full bg-base-200 flex items-center">
                                    <i class="fa fa-calendar-alt text-primary mr-3"></i>
                                    <span>{{ $payment_date ? \Carbon\Carbon::parse($payment_date)->format('d M Y') :
                                        'Not set' }}</span>
                                </div>
                            </div>

                            {{-- Amount --}}
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Amount Paid</span>
                                </label>
                                <div class="input input-bordered w-full bg-base-200 flex items-center">
                                    <i class="fa fa-money-bill-wave text-primary mr-3"></i>
                                    <span>Rp {{ number_format($amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            {{-- Payment Proof --}}
                            @if($existingAttachment)
                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold">Payment Proof</span>
                                </label>
                                <div class="p-4 bg-base-200 rounded-lg">
                                    <img src="{{ Storage::url($existingAttachment) }}" alt="Payment Proof"
                                        class="max-w-sm rounded-lg shadow-md" />
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Status Timeline --}}
                        <div class="timeline timeline-vertical mt-8 mb-6">
                            <div class="timeline-item">
                                <div class="timeline-marker badge badge-success">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="timeline-content pb-10">
                                    <time class="font-mono text-sm">Payment Submitted</time>
                                    <div class="text-sm">Confirmation received and recorded</div>
                                </div>
                            </div>

                            @if($order->transaction->payment_status === 'Processing')
                            <div class="timeline-item">
                                <div class="timeline-marker badge badge-info animate-pulse">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div class="timeline-content pb-10">
                                    <time class="font-mono text-sm">Under Review</time>
                                    <div class="text-sm">Admin is verifying your payment</div>
                                </div>
                            </div>
                            @elseif($order->transaction->payment_status === 'Verified' ||
                            $order->transaction->payment_status === 'Approved')
                            <div class="timeline-item">
                                <div class="timeline-marker badge badge-success">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="timeline-content pb-10">
                                    <time class="font-mono text-sm">Payment Verified</time>
                                    <div class="text-sm">Your payment has been confirmed by admin</div>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div class="timeline-marker badge badge-success">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <time class="font-mono text-sm">Registration Complete</time>
                                    <div class="text-sm">You are all set for the congress!</div>
                                </div>
                            </div>
                            @elseif($order->transaction->payment_status === 'Rejected')
                            <div class="timeline-item">
                                <div class="timeline-marker badge badge-error">
                                    <i class="fa fa-times"></i>
                                </div>
                                <div class="timeline-content pb-10">
                                    <time class="font-mono text-sm">Payment Rejected</time>
                                    <div class="text-sm">Please contact admin for more information</div>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Support Section --}}
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <div>
                                <p class="font-semibold">Need Help?</p>
                                <p class="text-sm mt-1">If you have questions about your order status, please contact
                                    our support team.</p>
                                <div class="mt-3">
                                    <a href="mailto:support@congress.com"
                                        class="link link-primary text-sm">support@congress.com</a>
                                </div>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div class="flex gap-3 justify-end pt-6 border-t mt-6">
                            <button type="button" wire:click="resetSearch" class="btn btn-outline btn-lg gap-2">
                                <i class="fa fa-search"></i>
                                Search Another Order
                            </button>
                        </div>

                        {{-- Jika masih bisa di-edit (Payment Unpaid atau Rejected) --}}
                        @else
                        <h2 class="card-title text-2xl mb-2">
                            <i class="fa fa-upload text-primary"></i>
                            {{ $isEditing ? 'Update Payment Confirmation' : 'Upload Payment Confirmation' }}
                        </h2>
                        <p class="text-sm text-gray-600 mb-6">
                            Please fill in the details below and upload your payment proof
                        </p>

                        @if($isEditing)
                        <div class="alert alert-info mb-6">
                            <i class="fa fa-info-circle"></i>
                            <div>
                                <p class="font-semibold">Update your submission</p>
                                <p class="text-sm">You have already submitted payment confirmation. You can update it
                                    below.</p>
                            </div>
                        </div>
                        @endif

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
                                <label class="label">
                                    <span class="label-text-alt text-gray-500">
                                        <i class="fa fa-info-circle"></i>
                                        Select the date when you made the payment
                                    </span>
                                </label>
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
                                        Recommended: {{ $this->getFormattedAmount() }}
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

                                {{-- Existing Attachment Preview --}}
                                @if($existingAttachment && !$attachment)
                                <div class="mb-4 p-4 bg-base-200 rounded-lg border-2 border-base-300">
                                    <p class="text-sm text-gray-700 mb-3 font-semibold flex items-center gap-2">
                                        <i class="fa fa-check-circle text-success"></i>
                                        Current Attachment
                                    </p>
                                    <div class="relative inline-block">
                                        <img src="{{ Storage::url($existingAttachment) }}" alt="Payment Proof"
                                            class="max-w-sm rounded-lg shadow-md" />
                                        <div class="absolute top-2 right-2 badge badge-success">
                                            <i class="fa fa-check"></i>
                                            Uploaded
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- File Input --}}
                                <div class="form-control">
                                    <label
                                        class="label cursor-pointer border-2 border-dashed border-primary/30 rounded-lg p-6 hover:border-primary/60 transition-colors">
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
                                <button type="button" wire:click="resetSearch" class="btn btn-outline btn-lg gap-2">
                                    <i class="fa fa-search"></i>
                                    Search Again
                                </button>

                                <button type="submit" class="btn btn-primary btn-lg gap-2" wire:loading.attr="disabled"
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
                        @endif
                    </div>
                </div>

                {{-- Additional Info Card --}}
                <div class="card bg-base-100 shadow-sm mt-6">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <i class="fa fa-question-circle text-primary"></i>
                            FAQ
                        </h3>

                        <div class="space-y-4">
                            {{-- FAQ Item 1 --}}
                            <div class="collapse collapse-plus bg-base-200">
                                <input type="radio" name="faq" />
                                <div class="collapse-title font-semibold">
                                    <i class="fa fa-question-circle text-primary mr-2"></i>
                                    What payment proof should I upload?
                                </div>
                                <div class="collapse-content">
                                    <p class="text-sm">Upload a screenshot of your bank transfer confirmation, payment
                                        receipt, or any proof showing you have made the payment. The image must clearly
                                        show the transaction details.</p>
                                </div>
                            </div>

                            {{-- FAQ Item 2 --}}
                            <div class="collapse collapse-plus bg-base-200">
                                <input type="radio" name="faq" />
                                <div class="collapse-title font-semibold">
                                    <i class="fa fa-question-circle text-primary mr-2"></i>
                                    How long does verification take?
                                </div>
                                <div class="collapse-content">
                                    <p class="text-sm">Our admin team typically verifies payment proofs within 1-2
                                        business days. You will receive an email notification once your payment has been
                                        verified and approved.</p>
                                </div>
                            </div>

                            {{-- FAQ Item 3 --}}
                            <div class="collapse collapse-plus bg-base-200">
                                <input type="radio" name="faq" />
                                <div class="collapse-title font-semibold">
                                    <i class="fa fa-question-circle text-primary mr-2"></i>
                                    Can I update my payment proof after submission?
                                </div>
                                <div class="collapse-content">
                                    <p class="text-sm">Yes, you can update your payment confirmation by searching for
                                        your registration code again and uploading a new payment proof. However, once
                                        your payment has been verified by admin, you cannot make changes.</p>
                                </div>
                            </div>

                            {{-- FAQ Item 4 --}}
                            <div class="collapse collapse-plus bg-base-200">
                                <input type="radio" name="faq" />
                                <div class="collapse-title font-semibold">
                                    <i class="fa fa-question-circle text-primary mr-2"></i>
                                    What if I can't find my registration code?
                                </div>
                                <div class="collapse-content">
                                    <p class="text-sm">Check your email inbox and spam folder for the confirmation
                                        email. If you still can't find it, contact our support team at
                                        support@congress.com with your participant email address.</p>
                                </div>
                            </div>

                            {{-- FAQ Item 5 --}}
                            <div class="collapse collapse-plus bg-base-200">
                                <input type="radio" name="faq" />
                                <div class="collapse-title font-semibold">
                                    <i class="fa fa-question-circle text-primary mr-2"></i>
                                    What happens after I submit payment confirmation?
                                </div>
                                <div class="collapse-content">
                                    <p class="text-sm">After submission, your payment proof will be reviewed by our
                                        admin team. Once verified, your order status will change to "Completed" and
                                        you'll receive a confirmation email. Your registration will then be finalized.
                                    </p>
                                </div>
                            </div>

                            {{-- FAQ Item 6 --}}
                            <div class="collapse collapse-plus bg-base-200">
                                <input type="radio" name="faq" />
                                <div class="collapse-title font-semibold">
                                    <i class="fa fa-question-circle text-primary mr-2"></i>
                                    My payment was rejected, what should I do?
                                </div>
                                <div class="collapse-content">
                                    <p class="text-sm">If your payment was rejected, please review the rejection reason
                                        provided and resubmit with the correct payment proof. Make sure the image is
                                        clear and shows all transaction details. Contact support if you need assistance.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
    </section>
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

            {{-- Additional Info --}}
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