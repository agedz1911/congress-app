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

    <section class="pt-10 pb-24 px-2 lg:px-5 bg-competition">
        <div class="flex flex-wrap gap-3 items-start justify-center">
            @foreach ($hotels as $hotel)
            <a wire:navigate href="{{route('hotel.room', ['id' => $hotel->id])}}" class="hover:shadow-md hover:rounded-lg">
                <div class="card bg-base-100 w-full max-w-sm shadow-sm">
                    <figure>
                        <img src="{{asset('storage/' . $hotel->feature_image)}}" alt="Shoes" />
                    </figure>
                    <div class="card-body">
                        <h2 class="card-title">{{$hotel->name}}</h2>
                        <div class="flex mb-3">
                            @for ($i = 1; $i <= 5; $i++) @if ($i <=$hotel->hotel_star)
                                <i class="fa-solid fa-star text-amber-400"></i>
                                @else
                                <i class="fa-solid fa-star"></i>
                                @endif
                                @endfor
                        </div>
                        <p><i class="fa fa-info-circle text-success"></i> {{$hotel->distance}}</p>
                        <div class="mt-4">
                            @foreach ($hotel->rooms as $room)
                            <p class="text-error text-lg font-bold">IDR {{number_format($hotel->rooms->min('price_idr'),
                            0,
                            ',', '.')}}</p>
                            @endforeach
                            <p class="font-extralight">Excluding taxes</p>
                        </div>
                        <!-- <div class="card-actions justify-end">
                            Book Now
                        </div> -->
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
</div>