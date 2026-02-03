<div>
    <!-- Toast Messages -->
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
            <li>Hotel</li>
        </ul>
    </div>

    @if(session()->has('hotel_cart') && count(session('hotel_cart')) > 0)
    <div class="fixed center-0 right-0 p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl p-4 max-w-md">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-lg">
                    <i class="fa-solid fa-shopping-cart mr-2"></i>
                    Your Cart
                </h3>
                <span class="badge badge-primary">{{$cartCount}} items</span>
            </div>

            @php
            $cart = session('hotel_cart');
            @endphp

            @foreach($cart as $key => $item)
            <div class="border-b py-2 last:border-0">
                <div class="flex justify-between">
                    <div>
                        <p class="font-semibold">{{$item['hotel_name']}}</p>
                        <p class="text-sm text-gray-600">{{$item['room_type']}}</p>
                        <p class="text-sm text-gray-500">{{$item['total_night']}} night(s)</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-primary">
                            {{$item['currency']}} {{number_format($item['subtotal'])}}
                        </p>
                        <button wire:click="removeFromCart({{$key}})" class="text-xs text-red-500 hover:text-red-700">
                            <i class="fa-solid fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="mt-3 pt-3 border-t">
                <div class="flex justify-between items-center">
                    <span class="font-bold">Total:</span>
                    <span class="font-bold text-xl text-primary">
                        {{$cartCurrency}} {{number_format($cartTotal)}}
                    </span>
                </div>
                <a href="{{route('checkout-hotel')}}" class="btn btn-primary w-full mt-3">
                    <i class="fa-solid fa-credit-card mr-2"></i>
                    Proceed to Checkout
                </a>
            </div>
        </div>
    </div>
    @endif


    <section class="pt-10 pb-24 px-2 lg:px-5 ">
        <div class="bg-base-100">
            <div class="flex flex-col md:flex-row gap-1">
                <div class="w-full md:w-1/2 md:h-80  border-2 border-warning shadow-md p-2 rounded-lg">
                    <div class="w-full md:h-80 rounded">
                        <img src="{{asset('storage/' . $hotel->feature_image)}}" class="w-full md:h-75 object-cover" />
                    </div>
                </div>

                <div class="w-full md:w-1/2 md:h-80">
                    <div class="flex flex-wrap gap-1">
                        @foreach ($hotel->galleries as $item)
                        <div class="border-2 border-warning shadow-md p-2 rounded-lg">
                            <div class="w-full md:max-w-64">
                                <img src="{{asset('storage/' . $item)}}" class="w-full object-fill" />
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-5 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <div class="flex mb-3">
                        @for ($i = 1; $i <= 5; $i++) @if ($i <=$hotel->hotel_star)
                            <i class="fa-solid fa-star text-amber-400"></i>
                            @else
                            <i class="fa-solid fa-star"></i>
                            @endif
                            @endfor
                    </div>
                    <h2 class="text-xl md:text-3xl font-semibold">{{$hotel->name}}</h2>
                </div>
                <div>
                    {!! str($hotel->description)->markdown()->sanitizeHtml() !!}
                </div>
            </div>

            <div class="flex flex-wrap gap-5 mt-5">
                @foreach ($hotel->rooms as $room)
                <div class="card bg-base-100 w-96 shadow-sm">
                    <figure>
                        @if ($room->image !== null)
                        <img src="{{asset('storage/' . $room->image)}}" alt="Shoes" />
                        @else
                        <p>No Image data</p>
                        @endif
                    </figure>
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{$room->room_type}}</h2>
                        <p class="text-xl text-info md:text-xl">IDR {{$room->price_idr}} - USD {{$room->price_usd}}
                            <br>/room/night
                        </p>
                        <div class="mt-4">
                            {!! str($room->description)->markdown()->sanitizeHtml() !!}
                        </div>
                        <div class="card-actions justify-end">
                            @auth
                            <button wire:click='addToCart({{$room->id}})' class="btn btn-primary">Book Room</button>
                            @else
                            <a href="{{route('login')}}" wire:navigate class="btn btn-warning">Login</a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </section>
</div>