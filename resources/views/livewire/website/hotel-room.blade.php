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
                            <button class="btn btn-primary">Book Room</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </section>
</div>