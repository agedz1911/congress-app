<div>
    <x-toast type="success" :message="session('success')" />
    <x-toast type="error" :message="session('error')" />
    <x-toast type="info" :message="session('info')" />

    <section class="breadcrumbs-web relative pb-0">
        <div class="absolute inset-0 bg-linear-to-b from-[#0059A8]/10 to-[#0059A8]/80"></div>
        <div class="py-16 lg:py-28 text-center relative">
            <h2 class="text-white uppercase text-2xl font-semibold tracking-wide lg:text-4xl">Accommodation</h2>
        </div>
    </section>

    <div class="breadcrumbs text-sm text-zinc-700 dark:text-zinc-50 px-2 lg:px-5 relative">
        <ul>
            <li><a wire:navigate href="{{route('home')}}">Home</a></li>
            <li><a wire:navigate href="{{route('accommodation')}}">Accommodation</a></li>
            <li><a wire:navigate href="">Hotel</a></li>
            <li>Check Out</li>
        </ul>
    </div>

    <section class="py-10 px-2 lg:px-5">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold mb-6">Checkout</h2>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Booking Form -->
                <div class="lg:col-span-2">
                    <form wire:submit="processBooking">
                        <div class="bg-base-100 rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-semibold mb-4">
                                <i class="fa-solid fa-calendar-alt mr-2"></i>
                                Booking Details
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Check-in Date</label>
                                    <input type="date" wire:model="checkInDate" class="input input-bordered w-full">
                                    @error('checkInDate') <span class="text-error text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Check-out Date</label>
                                    <input type="date" wire:model="checkOutDate" class="input input-bordered w-full">
                                    @error('checkOutDate') <span class="text-error text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-base-100 rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-semibold mb-4">
                                <i class="fa-solid fa-user mr-2"></i>
                                Participant Information
                            </h3>

                            <div>
                                <label class="block text-sm font-medium mb-2">Participant</label>
                                <select wire:model="participantId" class="select select-bordered w-full">
                                    <option value="">Select Participant</option>
                                    @foreach(auth()->user()->participants ?? [] as $participant)
                                    <option value="{{$participant->id}}">{{$participant->first_name}}</option>
                                    @endforeach
                                </select>
                                @error('participantId') <span class="text-error text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Coupon Section -->
                        <div class="bg-base-100 rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-semibold mb-4">
                                <i class="fa-solid fa-tag mr-2"></i>
                                Coupon Code
                            </h3>

                            @if(!$appliedCoupon)
                            <div class="flex gap-2">
                                <input type="text" wire:model.debounce.500ms="couponCode"
                                    placeholder="Enter coupon code" class="input input-bordered flex-1">
                                <button type="button" wire:click="applyCoupon" class="btn btn-secondary">
                                    <i class="fa-solid fa-ticket mr-2"></i>
                                    Apply
                                </button>
                            </div>
                            @else
                            <div
                                class="flex items-center justify-between bg-success/10 border border-success/30 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-success rounded-full p-2">
                                        <i class="fa-solid fa-check text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-success">Coupon Applied!</p>
                                        <p class="text-sm">
                                            @if($discountType === 'percent')
                                            {{$discountPercentage}}% OFF
                                            @else
                                            Fixed Discount
                                            @endif
                                            - {{$currency}} {{number_format($discount)}}
                                        </p>
                                    </div>
                                </div>
                                <button type="button" wire:click="removeCoupon"
                                    class="btn btn-sm btn-outline btn-error">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                            @endif

                            @if($appliedCoupon)
                            <div class="mt-3 text-sm text-gray-600">
                                <p><i class="fa-solid fa-info-circle mr-1"></i> Coupon: <span
                                        class="font-semibold">{{$appliedCoupon->name}}</span></p>
                                @if($appliedCoupon->remainingQuota() !== null)
                                <p><i class="fa-solid fa-users mr-1"></i> Remaining:
                                    {{$appliedCoupon->remainingQuota()}} uses</p>
                                @else
                                <p><i class="fa-solid fa-infinity mr-1"></i> Unlimited uses</p>
                                @endif
                                @if($appliedCoupon->ends_at)
                                <p><i class="fa-solid fa-calendar mr-1"></i> Expires:
                                    {{$appliedCoupon->ends_at->format('d M Y')}}</p>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="bg-base-100 rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-semibold mb-4">
                                <i class="fa-solid fa-credit-card mr-2"></i>
                                Payment Method
                            </h3>

                            <div class="space-y-3">
                                <label
                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-base-200 @if($paymentMethod === 'transfer') bg-base-200 @endif">
                                    <input type="radio" wire:model="paymentMethod" value="transfer"
                                        class="radio radio-primary">
                                    <span class="ml-3">
                                        <i class="fa-solid fa-university mr-2"></i>
                                        Bank Transfer
                                    </span>
                                </label>

                                <label
                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-base-200 @if($paymentMethod === 'credit_card') bg-base-200 @endif">
                                    <input type="radio" wire:model="paymentMethod" value="credit_card"
                                        class="radio radio-primary">
                                    <span class="ml-3">
                                        <i class="fa-solid fa-credit-card mr-2"></i>
                                        Credit Card
                                    </span>
                                </label>

                                <label
                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-base-200 @if($paymentMethod === 'e_wallet') bg-base-200 @endif">
                                    <input type="radio" wire:model="paymentMethod" value="e_wallet"
                                        class="radio radio-primary">
                                    <span class="ml-3">
                                        <i class="fa-solid fa-wallet mr-2"></i>
                                        E-Wallet
                                    </span>
                                </label>
                            </div>
                            @error('paymentMethod') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-full mt-6">
                            <i class="fa-solid fa-check mr-2"></i>
                            Complete Booking
                        </button>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-base-100 rounded-lg shadow-md p-6 sticky top-4">
                        <h3 class="text-xl font-semibold mb-4">Order Summary</h3>

                        @foreach($cartItems as $item)
                        <div class="border-b py-3 last:border-0">
                            <div class="flex gap-3">
                                @if($item['room_image'])
                                <img src="{{asset('storage/' . $item['room_image'])}}" alt="{{$item['room_type']}}"
                                    class="w-20 h-20 object-cover rounded">
                                @else
                                <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fa-solid fa-image text-gray-400"></i>
                                </div>
                                @endif
                                <div class="flex-1">
                                    <p class="font-semibold">{{$item['hotel_name']}}</p>
                                    <p class="text-sm text-gray-600">{{$item['room_type']}}</p>
                                    <p class="text-sm text-primary font-bold">
                                        {{$item['currency']}} {{number_format($item['price'])}}/night
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <div class="mt-4 pt-4 border-t">
                            <div class="flex justify-between mb-2">
                                <span>Total Night</span>
                                <span>..</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span>Subtotal</span>
                                <span>{{$currency}} {{number_format($totalAmount)}}</span>
                            </div>

                            @if($discount > 0)
                            <div class="flex justify-between mb-2 text-success">
                                <span>
                                    @if($discountType === 'percent')
                                    Discount ({{$discountPercentage}}%)
                                    @else
                                    Discount
                                    @endif
                                </span>
                                <span>-{{$currency}} {{number_format($discount)}}</span>
                            </div>
                            @endif

                            <div class="flex justify-between font-bold text-lg mt-3 pt-3 border-t">
                                <span>Total</span>
                                @if($discount > 0)
                                <div class="text-right">
                                    <span class="text-gray-400 line-through text-sm block">{{$currency}}
                                        {{number_format($totalAmount)}}</span>
                                    <span class="text-primary text-xl">{{$currency}}
                                        {{number_format($finalTotal)}}</span>
                                </div>
                                @else
                                <span class="text-primary text-xl">{{$currency}} {{number_format($finalTotal)}}</span>
                                @endif
                            </div>
                        </div>

                        @if($appliedCoupon)
                        <div class="mt-4 p-3 bg-success/10 rounded-lg">
                            <div class="flex items-center gap-2 text-success">
                                <i class="fa-solid fa-tag"></i>
                                <span class="font-semibold">You saved {{$currency}} {{number_format($discount)}}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>