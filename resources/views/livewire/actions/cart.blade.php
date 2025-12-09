<div>
    <x-toast type="success" :message="session('success')" />
    <x-toast type="error" :message="session('error')" />
    <x-toast type="info" :message="session('info')" />

    <div class="justify-center flex mt-5 lg:mt-10">
        <ul class="steps w-full steps-vertical lg:steps-horizontal">
            <li class="step {{$step >= 1 ? 'step-primary' : '' }}">Order Summary</li>
            <li class="step {{$step >= 2 ? 'step-primary' : '' }}">Detail Participant</li>
            <li class="step {{$step >= 3 ? 'step-primary' : '' }}">Payment Method</li>
            <li class="step {{$step >= 4 ? 'step-primary' : '' }}">Review & Order</li>
        </ul>
    </div>
    @if ($step == 1)

    <section class="py-5 lg:py-10">
        <div class="flex flex-col lg:flex-row justify-between px-5 lg:px-10 py-5 lg:py-10">

            <div class="overflow-x-auto w-full max-w-4xl rounded-2xl ">
                <table class="table">
                    <!-- head -->
                    <thead class=" bg-slate-200 dark:bg-slate-700">
                        <tr>
                            <th style="width: 40%;">Category Product</th>
                            <th>Price</th>
                            <th style="width: 10%;">Quantity</th>
                            <th>Unit Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cartItems as $cartKey => $item)
                        <tr>
                            <td>
                                <div>
                                    <p class="font-semibold">{{ $item['name'] }}</p>
                                    <span class="font-normal text-xs">{{ ucfirst(str_replace('_', ' ',
                                        $item['price_type'])) }}</span> <br>
                                    <span class="font-normal text-xs">dsad</span>
                                </div>
                            </td>
                            <td>{{ $item['currency'] }} {{ number_format($item['price'], 0, ',', '.') }}</td>
                            <td>
                                <input type="number" wire:change="updateQuantity('{{$cartKey}}', $event.target.value)"
                                    value="{{ $item['quantity'] }}" min="1" class="input input-sm" />
                            </td>
                            <td>{{ $item['currency'] }} {{ number_format($item['price'] * $item['quantity'], 0, ',',
                                '.') }}</td>
                            <td>
                                <button wire:click="removeFromCart('{{$cartKey}}')" class="btn btn-xs">
                                    <i class="fa fa-trash text-error text-xs"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                Your cart is empty. <a href="{{route('registration')}}" wire:navigate
                                    class="text-primary hover:underline">Continue shopping</a>
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
                <button wire:click='backToRegistration' class="btn btn-error rounded-lg mt-4"><i
                        class="fa fa-angles-left text-xs"></i> Back to Product
                    Registration</button>
            </div>

            <div>
                <h4 class="text-xl font-semibold mb-3">Order Summary</h4>
                <div class="card w-96 max-w-xl card-md shadow-sm">
                    <div class="card-body">
                        <div class="flex justify-between">
                            <h2 class="card-title">Subtotal</h2>
                            <h2 class="card-title">{{ $subtotal ? ($cartItems ?
                                $cartItems[array_key_first($cartItems)]['currency'] . ' ' . number_format($subtotal, 0,
                                ',', '.') : '0') : '0' }}</h2>
                        </div>
                        <h2 class="text-lg font-semibold">Promo Code</h2>
                        <div class="join mb-4">
                            <div>
                                <label class="input validator join-item">
                                    <i class="fa fa-tag text-primary mr-1"></i>
                                    <input wire:model.defer="promoCode" id="promoCode" autocomplete="off" type="text"
                                        placeholder="promo code" />
                                </label>

                            </div>
                            <button wire:click='applyPromoCode' class="btn btn-accent join-item">Apply</button>
                        </div>
                        @if($discount > 0)
                        <div class="flex justify-between">
                            <h2 class="card-title">Discount</h2>
                            <h2 class="card-title">

                                -{{number_format($discount, 0, ',', '.')}}

                            </h2>
                        </div>
                        @endif
                        <div class="flex justify-between mb-4">
                            <h2 class="card-title">Total</h2>
                            <h2 class="card-title text-success">{{ $subtotal ? ($cartItems ?
                                $cartItems[array_key_first($cartItems)]['currency'] . ' ' . number_format($subtotal, 0,
                                ',', '.') : '0') : '0' }}</h2>
                        </div>
                        <p></p>
                        <div class="justify-end card-actions">
                            <button wire:click="continueToParticipant" class="btn btn-primary btn-block">Continue to
                                detail participant <i class="fa fa-angles-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if ($step == 2)
    <section class="p-5 lg:p-10">
        <div class="card w-full bg-base-100 shadow-sm">
            <div class="card-body">
                <span class="badge badge-xs badge-warning">Participants</span>

                <div class="w-full py-5">
                    @if (!$hasParticipants)
                    {{-- Tampilkan jika user belum punya participant --}}
                    <div class="w-full flex flex-col justify-center items-center mb-5 p-8 bg-base-200 rounded-lg">
                        <i class="fa fa-user-plus text-4xl text-gray-400 mb-4"></i>
                        <h4 class="text-lg font-semibold mb-2">You don't have any Participant Details yet</h4>
                        <p class="text-gray-500 mb-4 text-center">Please add participant details to continue with your
                            order</p>
                        <button wire:click="redirectToAddParticipant" class="btn btn-primary">
                            <i class="fa fa-user-plus"></i> Add Detail Participants
                        </button>
                    </div>
                    @else
                    {{-- Tampilkan jika user sudah punya participant --}}
                    <div class="w-full mb-5">
                        <h4 class="text-lg font-semibold mb-3">Select Participant for this Order</h4>

                        <fieldset class="fieldset w-full">
                            <legend class="fieldset-legend">Participant List</legend>
                            {{-- Gunakan wire:model.live agar langsung reactive --}}
                            <select wire:model.live="selectedParticipantId" class="select w-full select-bordered">
                                <option value="">-- Select a Participant --</option>
                                @foreach ($participants as $participant)
                                <option value="{{ $participant['id'] }}">
                                    {{ $participant['first_name'] }} {{ $participant['last_name'] ?? '' }}
                                    ({{ $participant['email'] }})
                                    @if(!empty($participant['institution']))
                                    - {{ $participant['institution'] }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @error('selectedParticipantId')
                            <span class="label text-xs italic text-error">{{ $message }}</span>
                            @enderror
                        </fieldset>

                        {{-- Preview participant yang dipilih --}}
                        @if ($selectedParticipantId)
                        @php
                        $selectedParticipant = collect($participants)->firstWhere('id', $selectedParticipantId);
                        @endphp
                        @if ($selectedParticipant)
                        <div class="mt-4 p-4 bg-base-200 rounded-lg border-2 border-primary">
                            <div class="flex justify-between items-center mb-2">
                                <h5 class="font-semibold">Selected Participant Details:</h5>
                                <button wire:click="unselectParticipant" class="btn btn-sm btn-ghost btn-circle"
                                    title="Remove selection">
                                    <i class="fa fa-times text-error"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="font-medium text-gray-600">Name:</span>
                                    <p class="font-semibold">
                                        {{ $selectedParticipant['first_name'] }} {{ $selectedParticipant['last_name'] ??
                                        '' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-600">Email:</span>
                                    <p class="font-semibold">{{ $selectedParticipant['email'] }}</p>
                                </div>
                                @if(!empty($selectedParticipant['phone_number']))
                                <div>
                                    <span class="font-medium text-gray-600">Phone:</span>
                                    <p class="font-semibold">{{ $selectedParticipant['phone_number'] }}</p>
                                </div>
                                @endif
                                @if(!empty($selectedParticipant['institution']))
                                <div>
                                    <span class="font-medium text-gray-600">Institution:</span>
                                    <p class="font-semibold">{{ $selectedParticipant['institution'] }}</p>
                                </div>
                                @endif
                                @if(!empty($selectedParticipant['country']))
                                <div>
                                    <span class="font-medium text-gray-600">Country:</span>
                                    <p class="font-semibold">{{ $selectedParticipant['country'] }}</p>
                                </div>
                                @endif
                                @if(!empty($selectedParticipant['city']))
                                <div>
                                    <span class="font-medium text-gray-600">City:</span>
                                    <p class="font-semibold">{{ $selectedParticipant['city'] }}</p>
                                </div>
                                @endif
                                @if(!empty($selectedParticipant['title']))
                                <div>
                                    <span class="font-medium text-gray-600">Title:</span>
                                    <p class="font-semibold">{{ $selectedParticipant['title'] }}</p>
                                </div>
                                @endif
                                @if(!empty($selectedParticipant['speciality']))
                                <div>
                                    <span class="font-medium text-gray-600">Speciality:</span>
                                    <p class="font-semibold">{{ $selectedParticipant['speciality'] }}</p>
                                </div>
                                @endif
                            </div>

                            {{-- Tombol untuk unselect --}}
                            <div class="mt-3 pt-3 border-t border-gray-300">
                                <button wire:click="unselectParticipant" class="btn btn-sm btn-outline btn-error">
                                    <i class="fa fa-times"></i> Remove Selection
                                </button>
                            </div>
                        </div>
                        @endif
                        @endif

                        {{-- Tombol untuk menambah participant baru --}}
                        <div class="mt-4">
                            <button wire:click="redirectToAddParticipant" class="btn btn-outline btn-sm">
                                <i class="fa fa-plus"></i> Add New Participant
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-between p-5">
                <button wire:click='backToOrderSummary' class="btn btn-error">
                    <i class="fa fa-angles-left text-xs"></i> Back to Summary
                </button>
                <button wire:click='continueToPaymentMethod' class="btn btn-primary" @if(!$hasParticipants ||
                    !$selectedParticipantId) disabled @endif>
                    Continue to Payment Method <i class="fa fa-angles-right text-xs"></i>
                </button>
            </div>
        </div>
    </section>
    @endif

    @if ($step == 3)


    <section class="py-5 lg:py-10 flex flex-col items-center">
        <div class="card w-full max-w-3xl bg-base-100 card-lg shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Select Payment Method</h2>
                <div class="w-full flex justify-between flex-col lg:flex-row py-5">
                    <label class="cursor-pointer">
                        <div class="flex items-center p-6 border-2 rounded-xl transition-all hover:border-primary hover:bg-base-200
                        {{ $paymentMethod === 'bank_transfer' ? 'border-primary bg-base-200' : 'border-base-300' }}">
                            <input type="radio" name="payment_method" value="bank_transfer"
                                wire:model.live="paymentMethod" class="radio radio-primary" />
                            <div class="ml-4 flex-1">
                                <div class="flex items-center gap-2">
                                    <i class="fa fa-building-columns text-2xl text-primary"></i>
                                    <span class="font-semibold text-lg">Bank Transfer</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Transfer directly to our bank account</p>
                            </div>
                            @if($paymentMethod === 'bank_transfer')
                            <i class="fa fa-check-circle text-primary text-xl"></i>
                            @endif
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <div class="flex items-center p-6 border-2 rounded-xl transition-all hover:border-primary hover:bg-base-200
                        {{ $paymentMethod === 'credit_card' ? 'border-primary bg-base-200' : 'border-base-300' }}">
                            <input type="radio" name="payment_method" value="credit_card"
                                wire:model.live="paymentMethod" class="radio radio-primary" />
                            <div class="ml-4 flex-1">
                                <div class="flex items-center gap-2">
                                    <i class="fa fa-credit-card text-2xl text-primary"></i>
                                    <span class="font-semibold text-lg">Credit Card</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Pay securely with your credit card</p>
                            </div>
                            @if($paymentMethod === 'credit_card')
                            <i class="fa fa-check-circle text-primary text-xl"></i>
                            @endif
                        </div>
                    </label>
                </div>

                @if($paymentMethod === 'bank_transfer')
                <div class="alert alert-info mt-4">
                    <i class="fa fa-info-circle"></i>
                    <span>After placing your order, you will receive bank transfer instructions via email.</span>
                </div>
                @elseif($paymentMethod === 'credit_card')
                <div class="alert alert-info mt-4">
                    <i class="fa fa-info-circle"></i>
                    <span>You will be redirected to our secure payment gateway to complete your payment.</span>
                </div>
                @endif

                <div class="justify-between card-actions">
                    <button wire:click='backToParticipant' class="btn btn-error"><i
                            class="fa fa-angles-left text-xs"></i> Back to detail Participant</button>
                    <button wire:click='continueToReview' class="btn btn-primary" @if(!$paymentMethod) disabled @endif>
                        Continue to Review <i class="fa fa-angles-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if ($step == 4)


    <section class="px-5 lg:px-10 py-5 lg:py-10">
        <div>
            <h2 class="font-semibold text-xl">Order Review</h2>
            <div class="flex flex-col lg:flex-row justify-between ">
                <div class="overflow-x-auto w-full max-w-4xl rounded-2xl ">
                    <table class="table">
                        <!-- head -->
                        <thead class="bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th style="width: 40%;">Category Product</th>
                                <th>Price</th>
                                <th style="width: 10%;">Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)

                            <tr>
                                <td>
                                    <div>
                                        <p class="font-semibold">{{ $item['name'] }} <br>
                                            <span class="font-normal text-xs">{{ ucfirst(str_replace('_', ' ',
                                                $item['price_type'])) }}</span> <br>
                                            <span class="font-normal text-xs">satu lagi</span>
                                        </p>
                                    </div>
                                </td>
                                <td>{{ $item['currency'] }} {{ number_format($item['price'], 0, ',', '.') }}</td>
                                <td>
                                    {{ $item['quantity'] }}
                                </td>
                                <td>{{ $item['currency'] }} {{ number_format($item['price'] * $item['quantity'], 0, ',',
                                    '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>
                    <h4 class="text-xl font-semibold mb-3">Order Summary</h4>
                    <div class="card w-96 max-w-xl card-md shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title text-xl mb-4">Order Summary</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between text-base">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-semibold">
                                        {{ $cartItems ? $cartItems[array_key_first($cartItems)]['currency'] : '' }}
                                        {{ number_format($subtotal, 0, ',', '.') }}
                                    </span>
                                </div>

                                @if($discount > 0)
                                <div class="flex justify-between text-base text-success">
                                    <span>Discount</span>
                                    <span class="font-semibold">
                                        - {{ $cartItems ? $cartItems[array_key_first($cartItems)]['currency'] : '' }}
                                        {{ number_format($discount, 0, ',', '.') }}
                                    </span>
                                </div>
                                @if(session()->has('applied_coupon'))
                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fa fa-tag"></i>
                                    <span>Coupon: {{ session('applied_coupon')['code'] }}</span>
                                </div>
                                @endif
                                @endif

                                <div class="divider my-2"></div>

                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-primary">
                                        {{ $cartItems ? $cartItems[array_key_first($cartItems)]['currency'] : '' }}
                                        {{ number_format($total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-xl mb-4">
                        <i class="fa fa-credit-card text-primary"></i>
                        Payment Method
                    </h3>
                    <div class="flex items-center gap-3 p-4 bg-base-200 rounded-lg">
                        @if($paymentMethod === 'bank_transfer')
                        <i class="fa fa-building-columns text-2xl text-primary"></i>
                        <div>
                            <p class="font-semibold">Bank Transfer</p>
                            <p class="text-sm text-gray-500">Transfer to our bank account</p>
                        </div>
                        @else
                        <i class="fa fa-credit-card text-2xl text-primary"></i>
                        <div>
                            <p class="font-semibold">Credit Card</p>
                            <p class="text-sm text-gray-500">Pay with credit card</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 mt-5 shadow-sm">
                <div class="card-body">
                    <h1 class="mb-3 card-title font-semibold">Terms and Conditions</h1>
                    <p class="text-justify text-gray-400 text-sm">Lorem ipsum dolor sit amet, consectetur
                        adipisicing elit. In impedit itaque debitis reprehenderit assumenda. Esse aspernatur a in. Sunt
                        provident quisquam repudiandae voluptates possimus optio reiciendis consectetur molestias
                        repellat fugit.</p>
                    <h1 class="mb-3 mt-5 card-title font-semibold">Cancellation Policy</h1>
                    <p class="text-justify text-gray-400 text-sm">Lorem ipsum dolor sit amet, consectetur
                        adipisicing elit. In impedit itaque debitis reprehenderit assumenda. Esse aspernatur a in. Sunt
                        provident quisquam repudiandae voluptates possimus optio reiciendis consectetur molestias
                        repellat fugit.</p>

                    <div class="flex flex-col justify-center items-center py-10">
                        <h1 class="text-center text-lg mb-4">By clicking "I Agree" you agree and consent to our Terms
                            and Conditions.</h1>
                        <div class="form-control">
                            <label
                                class="label cursor-pointer justify-start gap-4 p-4 bg-warning/10 rounded-lg border-2 border-warning">
                                <input type="checkbox" wire:model.live="agreeTerms" class="checkbox checkbox-warning" />
                                <span class="label-text font-medium">
                                    I agree to the terms and conditions and cancellation policy
                                </span>
                            </label>
                        </div>

                        <div class="flex w-full max-w-xl justify-between mt-10 gap-4">
                            <button wire:click='backToPaymentMethod' class="btn btn-outline btn-error">
                                <i class="fa fa-angles-left text-xs"></i> Back to Payment
                            </button>
                            <button wire:click='submitOrder' class="btn btn-primary btn-block" @if(!$agreeTerms)
                                disabled @endif wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitOrder">
                                    <i class="fa fa-check"></i> Submit Order
                                </span>
                                <span wire:loading wire:target="submitOrder">
                                    <span class="loading loading-spinner loading-xs"></span>
                                    Processing...
                                </span>
                            </button>
                        </div>
                        @if(!$agreeTerms)
                        <div class="alert alert-warning mt-4">
                            <i class="fa fa-exclamation-triangle text-sm"></i>
                            <span class="text-xs">Please agree to terms to submit order</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
</div>