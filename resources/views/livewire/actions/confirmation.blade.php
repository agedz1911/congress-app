<div class="w-full">
    <x-toast type="success" :message="session('success')" :duration="5000" />
    <x-toast type="error" :message="session('error')" />
    <x-toast type="info" :message="session('info')" />
    <section class="breadcrumbs relative pb-0">
        <div class="absolute inset-0 bg-gradient-to-b from-[#0059A8]/10 to-[#0059A8]/80"></div>
        <div class="py-16 lg:py-28 text-center relative">
            <h2 class="text-white uppercase text-2xl font-semibold tracking-wide lg:text-4xl">Confirmation</h2>
        </div>
    </section>

    <section class="pt-10 pb-24 px-2 lg:px-5">
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
                            <p class="font-bold text-lg"></p>
                        </div>

                        {{-- Order Status --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Order Status</p>
                            <span class="badge ">

                            </span>
                        </div>

                        {{-- Participant --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Participant</p>
                            <p class="font-semibold"></p>
                            <p class="text-sm"></p>
                        </div>

                        {{-- Order Items --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Items</p>

                            <div class="flex justify-between text-sm py-1">
                                <span> x</span>
                                <span class="font-semibold"></span>
                            </div>

                        </div>

                        {{-- Discount --}}

                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-500">Discount</span>
                            <span class="text-error">- </span>
                        </div>


                        {{-- Total --}}
                        <div class="divider my-2"></div>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-primary"></span>
                        </div>

                        {{-- Kurs Information --}}
                        <div class="mt-4 p-3 bg-base-200 rounded-lg">
                            <p class="text-xs text-gray-500">Amount to Pay (with exchange rate)</p>
                            <p class="font-bold text-lg">

                            </p>
                        </div>

                        {{-- Payment Method --}}
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">Payment Method</p>
                            <div class="flex items-center gap-2 mt-1">

                                <i class="fa fa-building-columns text-info"></i>

                                <i class="fa fa-credit-card text-success"></i>

                                <span class="font-semibold"></span>
                            </div>
                        </div>

                        {{-- Bank Account Info (if Bank Transfer) --}}

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

                    </div>
                </div>
            </div>

            {{-- Right Column: Payment Confirmation Form --}}
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-6">
                            <i class="fa fa-upload text-primary"></i>

                        </h2>


                        <div class="alert alert-info mb-6">
                            <i class="fa fa-info-circle"></i>
                            <span>You have already submitted payment confirmation. You can update it below.</span>
                        </div>


                        <form>
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

                                <div class="mb-4 p-4 bg-base-200 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-2">Current Attachment:</p>
                                    <img src="" alt="Payment Proof" class="max-w-xs rounded-lg shadow-md" />
                                </div>


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

                                <div class="mt-4 relative">
                                    <p class="text-sm text-gray-600 mb-2">Preview:</p>
                                    <div class="relative inline-block">
                                        <img src="" alt="Preview" class="max-w-xs rounded-lg shadow-md" />
                                        <button type="button"
                                            class="btn btn-circle btn-sm btn-error absolute top-2 right-2">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>


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

                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span>
                                        <i class="fa fa-paper-plane"></i>

                                    </span>
                                    <span>
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
    </section>
</div>