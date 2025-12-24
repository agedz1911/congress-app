<div>
    <x-toast type="success" :message="session('success')" :duration="5000" />
    <x-toast type="error" :message="session('error')" />
    <x-toast type="info" :message="session('info')" />

    <div class="breadcrumbs text-sm text-zinc-700 dark:text-zinc-50">
        <ul>
            <li><a href="{{route('dashboard')}}" wire:navigate>Dashboard</a></li>
            <li><a href="{{route('myregistrations')}}" wire:navigate>My Registration</a></li>
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
                        <p class="font-bold text-lg">{{ $orderRegCode }}</p>
                    </div>

                    {{-- Order Status --}}
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Order Status</p>
                        <span
                            class="badge badge-{{ $orderStatus === 'New' ? 'info' : ($orderStatus === 'Processing' ? 'warning' : 'success') }}">
                            {{ $orderStatus }}
                        </span>
                    </div>

                    {{-- Participant --}}
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Participant</p>
                        <p class="font-semibold">{{ $participantName }}</p>
                        <p class="text-sm">{{ $participantEmail }}</p>
                        <p class="text-sm">{{ $participantCountry }}</p>
                    </div>

                    {{-- Order Items --}}
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-2">Items</p>
                        @foreach($orderItems as $item)
                        <div class="flex justify-between text-sm py-1">
                            <span>{{ $item['product_name'] }} x{{ $item['quantity'] }}</span>
                            <span class="font-semibold">{{$participantCountry == 'Indonesia' ? 'IDR' : 'USD'}} {{ number_format($item['total_price'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Discount --}}
                    @if($orderDiscount > 0)
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Discount</span>
                        <span class="text-error">- {{$participantCountry == 'Indonesia' ? 'IDR' : 'USD'}} {{ number_format($orderDiscount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    {{-- Total --}}
                    <div class="divider my-2"></div>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-primary">{{$participantCountry == 'Indonesia' ? 'IDR' : 'USD'}} {{ number_format($orderTotal, 0, ',', '.') }}</span>
                    </div>

                    {{-- Payment Method --}}
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">Payment Method</p>
                        <div class="flex items-center gap-2 mt-1">
                            @if($paymentMethod === 'Bank Transfer')
                            <i class="fa fa-building-columns text-info"></i>
                            @else
                            <i class="fa fa-credit-card text-success"></i>
                            @endif
                            <span class="font-semibold">{{ $paymentMethod }}</span>
                        </div>
                    </div>

                    {{-- Bank Account Info (if Bank Transfer) --}}
                    @if($paymentMethod === 'Bank Transfer')
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
                                    <i class="fa fa-calendar-alt text-primary"></i>
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
                                    <i class="fa fa-money-bill-wave text-primary"></i>
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
                                <span class="label-text-alt text-gray-600 font-semibold">
                                    <i class="fa fa-check-circle text-success"></i>
                                    Must be: {{$participantCountry == 'Indonesia' ? 'IDR' : 'USD'}} {{ number_format($orderTotal, 0, ',', '.') }}
                                </span>
                            </label>
                        </div>

                        {{-- Attachment --}}
                        <div class="form-control w-full mb-6">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    <i class="fa fa-image text-primary"></i>
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
                            <div class="form-control">
                                <label class="label w-full cursor-pointer border-2 border-dashed border-primary/30 rounded-lg p-6 hover:border-primary/60 transition-colors">
                                    <div class="text-center w-full">
                                        <i class="fa fa-cloud-upload-alt text-4xl text-primary mb-3 block"></i>
                                        <span class="text-base font-semibold text-gray-700">Click to upload or drag and drop</span>
                                        <p class="text-sm text-gray-500 mt-1">JPG, PNG, or GIF (Max 2MB)</p>
                                    </div>
                                    <input type="file" wire:model="attachment" class="hidden" accept="image/*" />
                                </label>
                            </div>

                            @error('attachment')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror

                            {{-- Image Preview --}}
                            @if($attachment)
                            <div class="mt-4">
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
                        <div class="alert alert-warning mb-6">
                            <i class="fa fa-exclamation-triangle text-lg"></i>
                            <div>
                                <p class="font-semibold">Important Information:</p>
                                <ul class="text-sm mt-2 space-y-1 ml-4">
                                    <li><i class="fa fa-check text-success"></i> Make sure the payment proof image is clear and readable</li>
                                    <li><i class="fa fa-check text-success"></i> Your payment will be verified by admin within 1-2 business days</li>
                                    <li><i class="fa fa-check text-success"></i> You will receive email notification once verified</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3 justify-end pt-6 border-t">
                            <button type="button" wire:click="cancelEdit" class="btn btn-ghost gap-2">
                                <i class="fa fa-times"></i>
                                Cancel
                            </button>

                            <button type="submit" class="btn btn-primary gap-2" wire:loading.attr="disabled"
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