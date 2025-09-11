<div class="w-full">
    <section class="breadcrumbs relative pb-0">
        <div class="absolute inset-0 bg-gradient-to-b from-[#0059A8]/10 to-[#0059A8]/80"></div>
        <div class="py-16 lg:py-28 text-center relative">
            <h2 class="text-white uppercase text-2xl font-semibold tracking-wide lg:text-4xl">Registration</h2>
        </div>
    </section>

    <section class="pt-10 pb-24 px-2 lg:px-5 bg-competition">
        <!-- name of each tab group should be unique -->
        <div class="tabs tabs-border justify-center">
            <input type="radio" name="reg_tabs" class="tab" aria-label="INDONESIAN PARTICIPANT" checked="checked" />
            <div class="tab-content border-base-300 p-10">

                <h3 class="text-xl font-semibold mb-2">Symposium</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Early Bird</th>
                                <th>Regular</th>
                                <th>Onsite Registration</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($symposiums as $product)
                            <tr>
                                <th>{{ $loop->iteration }}</th>

                                <td>{{ $product->name }}</td>
                                <td>IDR {{ number_format($product->early_bird_idr, 0, ',', '.') }} <br>
                                    @auth
                                    @if (now()->isBefore($product->early_bird_end))
                                    <button class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else

                                    @endauth
                                </td>
                                <td>IDR {{ number_format($product->regular_idr, 0, ',', '.') }} <br>
                                    @auth

                                    @if (now()->isAfter($product->early_bird_end) &&
                                    now()->isBefore($product->regular_end))
                                    <button class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else

                                    @endauth
                                </td>
                                <td>IDR {{ number_format($product->on_site_idr, 0, ',', '.') }} <br>
                                    @auth

                                    @if (now()->isAfter($product->regular_end))
                                    <button wire:click='addToCart({{$product->id}}, "onsite")'
                                        class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else
                                    @endauth
                                </td>
                                <td>
                                    @auth
                                    @else
                                    <a href="{{route('login')}}" wire:navigate class="btn btn-primary rounded-xl">
                                        Login
                                    </a>
                                    @endauth
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7">Tidak ada data symposium.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h3 class="text-xl font-semibold mb-2">Workshop</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Early Bird</th>
                                <th>Regular</th>
                                <th>Onsite Registration</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($workshops as $product)
                            <tr>
                                <th>{{ $loop->iteration }}</th>
                                <td>{{ $product->name }}</td>
                                <td>IDR {{ number_format($product->early_bird_idr, 0, ',', '.') }} <br>
                                    @auth
                                    @if (now()->isBefore($product->early_bird_end))
                                    <button class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else

                                    @endauth
                                </td>
                                <td>IDR {{ number_format($product->regular_idr, 0, ',', '.') }} <br>
                                    @auth

                                    @if (now()->isAfter($product->early_bird_end) &&
                                    now()->isBefore($product->regular_end))
                                    <button class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else

                                    @endauth
                                </td>
                                <td>IDR {{ number_format($product->on_site_idr, 0, ',', '.') }} <br>
                                    @auth

                                    @if (now()->isAfter($product->regular_end))
                                    <button wire:click='addToCart({{$product->id}}, "onsite")'
                                        class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else
                                    @endauth
                                </td>
                                <td>
                                    @auth
                                    @else
                                    <a href="{{route('login')}}" wire:navigate class="btn btn-primary rounded-xl">
                                        Login
                                    </a>
                                    @endauth
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">Tidak ada data symposium.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            <input type="radio" name="reg_tabs" class="tab" aria-label="FOREIGN PARTICIPANT" />
            <div class="tab-content border-base-300 p-10">
                <h3 class="text-xl font-semibold mb-2">Symposium</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Early Bird</th>
                                <th>Regular</th>
                                <th>Onsite Registration</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($symposiums as $product)
                            <tr>
                                <th>{{ $loop->iteration }}</th>
                                <td>{{ $product->name }}</td>
                                <td>USD {{ number_format($product->early_bird_usd, 0, ',', '.') }} <br>
                                    @auth
                                    @if (now()->isBefore($product->early_bird_end))
                                    <button class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else

                                    @endauth
                                </td>
                                <td>USD {{ number_format($product->regular_usd, 0, ',', '.') }} <br>
                                    @auth

                                    @if (now()->isAfter($product->early_bird_end) &&
                                    now()->isBefore($product->regular_end))
                                    <button class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else

                                    @endauth
                                </td>
                                <td>USD {{ number_format($product->on_site_usd, 0, ',', '.') }} <br>
                                    @auth

                                    @if (now()->isAfter($product->regular_end))
                                    <button wire:click='addToCart({{$product->id}}, "onsite")'
                                        class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else
                                    @endauth
                                </td>
                                <td>
                                    @auth
                                    @else
                                    <a href="{{route('login')}}" wire:navigate class="btn btn-primary rounded-xl">
                                        Login
                                    </a>
                                    @endauth
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">Tidak ada data symposium.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h3 class="text-xl font-semibold mb-2">Workshop</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Early Bird</th>
                                <th>Regular</th>
                                <th>Onsite Registration</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($workshops as $product)
                            <tr>
                                <th>{{ $loop->iteration }}</th>
                                <td>{{ $product->name }}</td>
                                <td>USD {{ number_format($product->early_bird_usd, 0, ',', '.') }} <br>
                                    @auth
                                    @if (now()->isBefore($product->early_bird_end))
                                    <button class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else

                                    @endauth
                                </td>
                                <td>USD {{ number_format($product->regular_usd, 0, ',', '.') }} <br>
                                    @auth

                                    @if (now()->isAfter($product->early_bird_end) &&
                                    now()->isBefore($product->regular_end))
                                    <button class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else

                                    @endauth
                                </td>
                                <td>USD {{ number_format($product->on_site_usd, 0, ',', '.') }} <br>
                                    @auth

                                    @if (now()->isAfter($product->regular_end))
                                    <button wire:click='addToCart({{$product->id}}, "onsite")'
                                        class="btn btn-sm btn-primary">Add to Cart</button>
                                    @else
                                    <button class="btn btn-disabled line-through btn-sm">Add to
                                        Cart</button>
                                    @endif
                                    @else
                                    @endauth
                                </td>
                                <td>
                                    @auth
                                    @else
                                    <a href="{{route('login')}}" wire:navigate class="btn btn-primary rounded-xl">
                                        Login
                                    </a>
                                    @endauth
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">Tidak ada data symposium.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </section>
</div>