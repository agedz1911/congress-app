<div>
    <div class="mb-3 gap-3 flex justify-end">
        <label class="input">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input wire:model.live.debounce.300ms='search' type="search" class="grow" placeholder="Search" />
        </label>
        <a href="{{route('registration')}}" wire:navigate class="btn btn-primary"><i class="fa fa-plus"></i> Add
            New</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        @forelse($orders as $order)
        <div class="relative">
            <!-- Ticket Card -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg overflow-hidden border border-zinc-200 dark:border-zinc-700 hover:shadow-xl transition-shadow duration-300">

                <!-- Header Section dengan Gradient -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 text-white relative">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs opacity-90 uppercase tracking-wider">Order ID</p>
                            <h3 class="text-xl font-bold">{{$order->reg_code}}</h3>
                        </div>
                        <div class="text-right">
                            @if ($order->status == 'Validated')
                            <div class="badge badge-success gap-1">
                                <i class="fa fa-circle-check"></i> {{$order->status}}
                            </div>
                            @elseif ($order->status == 'Processing')
                            <div class="badge badge-warning gap-1">
                                <i class="fa fa-rotate"></i> {{$order->status}}
                            </div>
                            @elseif ($order->status == 'New')
                            <div class="badge badge-info gap-1">
                                <i class="fa fa-wand-magic-sparkles"></i> {{$order->status}}
                            </div>
                            @elseif ($order->status == 'Cancelled')
                            <div class="badge badge-error gap-1">
                                <i class="fa fa-circle-xmark"></i> {{$order->status}}
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Perforated Edge Effect -->
                    <div class="absolute -bottom-3 left-0 right-0 flex justify-between px-2">
                        @for($i = 0; $i < 20; $i++)
                            <div class="w-3 h-3 bg-zinc-100 dark:bg-zinc-900 rounded-full">
                    </div>
                    @endfor
                </div>
            </div>

            <!-- Content Section -->
            <div class="p-6 space-y-4">

                <!-- Participant Info -->
                <div class="flex items-start gap-3 pb-4 border-b border-dashed border-zinc-300 dark:border-zinc-600">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                        {{strtoupper(substr($order->participant->first_name, 0, 1))}}{{strtoupper(substr($order->participant->last_name, 0, 1))}}
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg text-zinc-800 dark:text-zinc-100">
                            {{$order->participant->first_name}} {{$order->participant->last_name}}
                        </h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                            <i class="fa fa-globe text-xs"></i> {{$order->participant->country}}
                        </p>
                    </div>
                </div>

                <!-- Products Section with Registration Type -->
                <div>
                    <p class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <i class="fa fa-box"></i> Products & Registration Type
                    </p>
                    <div class="space-y-3">
                        @forelse($order->items as $item)
                        <div class="bg-zinc-50 dark:bg-zinc-700/50 rounded-lg p-4 border border-zinc-200 dark:border-zinc-600">
                            <!-- Product Name & Registration Type -->
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <div class="badge badge-info gap-1">
                                    <i class="fa fa-ticket"></i>
                                    {{ $item->product->name ?? 'N/A' }}
                                </div>
                                @if($item->product && $item->product->regtype)
                                <div class="badge badge-primary gap-1">
                                    <i class="fa fa-tag"></i>
                                    {{ $item->product->regtype->name }}
                                </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-white dark:bg-zinc-800 rounded p-2">
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Quantity</p>
                                    <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $item->quantity }}</p>
                                </div>
                                <div class="bg-white dark:bg-zinc-800 rounded p-2">
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Unit Price</p>
                                    <p class="font-semibold text-zinc-800 dark:text-zinc-100">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <!-- Registration Type Details (Optional - jika ingin detail lebih) -->
                            @if($item->product && $item->product->regtype)
                            <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-600">
                                <div class="flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-400">
                                    <i class="fa fa-info-circle"></i>
                                    <span class="font-medium">Registration Type:</span>
                                    <span class="text-zinc-800 dark:text-zinc-100">{{ $item->product->regtype->name }}</span>
                                </div>

                                <!-- Price Type Indicator (Early Bird, Regular, On-Site) -->
                                @if($item->product->is_early_bird &&
                                now()->between($item->product->early_bird_start, $item->product->early_bird_end))
                                <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded text-xs">
                                    <i class="fa fa-bolt"></i>
                                    <span class="font-medium">Early Bird Price</span>
                                </div>
                                @elseif($item->product->is_regular &&
                                now()->between($item->product->regular_start, $item->product->regular_end))
                                <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs">
                                    <i class="fa fa-calendar-check"></i>
                                    <span class="font-medium">Regular Price</span>
                                </div>
                                @elseif($item->product->is_on_site)
                                <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded text-xs">
                                    <i class="fa fa-location-dot"></i>
                                    <span class="font-medium">On-Site Price</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Subtotal -->
                            <div class="mt-3 pt-3 border-t border-dashed border-zinc-300 dark:border-zinc-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Subtotal</span>
                                    <span class="text-lg font-bold text-zinc-800 dark:text-zinc-100">
                                        Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 bg-zinc-100 dark:bg-zinc-700/30 rounded-lg">
                            <i class="fa fa-box-open text-2xl text-zinc-400 mb-2"></i>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 italic">No items</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Financial Info -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    @if($order->coupon)
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                            <i class="fa fa-ticket"></i> Coupon
                        </p>
                        <p class="font-semibold text-green-600 dark:text-green-400">{{$order->coupon}}</p>
                    </div>
                    @endif

                    @if($order->discount > 0)
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3">
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                            <i class="fa fa-percent"></i> Discount
                        </p>
                        <p class="font-semibold text-orange-600 dark:text-orange-400">Rp {{number_format($order->discount, 0, ',', '.')}}</p>
                    </div>
                    @endif
                </div>

                <!-- Total Amount -->
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs font-semibold uppercase text-zinc-600 dark:text-zinc-400">Total Amount</p>
                            <p class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">Rp {{number_format($order->total, 0, ',', '.')}}</p>
                        </div>
                        <div class="text-4xl opacity-20">
                            <i class="fa fa-receipt"></i>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-dashed border-zinc-300 dark:border-zinc-600">
                    <div>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-1">Payment Method</p>
                        @if ($order->transaction->payment_method == 'Bank Transfer')
                        <div class="badge badge-sm badge-outline badge-primary gap-1">
                            <i class="fa fa-money-bill-transfer"></i> Bank Transfer
                        </div>
                        @else
                        <div class="badge badge-sm badge-outline badge-success gap-1">
                            <i class="fa fa-credit-card"></i> {{$order->transaction->payment_method}}
                        </div>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-1">Payment Status</p>
                        @if ($order->transaction->payment_status == 'Paid')
                        <div class="badge badge-sm badge-success gap-1">
                            <i class="fa fa-circle-check"></i> Paid
                        </div>
                        @else
                        <div class="badge badge-sm badge-error gap-1">
                            <i class="fa fa-circle-xmark"></i> {{$order->transaction->payment_status}}
                        </div>
                        @endif
                    </div>
                </div>

                @if ($order->transaction->payment_date)
                <div class="text-center pt-2">
                    <p class="text-xs text-zinc-600 dark:text-zinc-400">
                        <i class="fa fa-calendar"></i> Paid on {{\Carbon\Carbon::parse($order->transaction->payment_date)->format('d F, Y')}}
                    </p>
                </div>
                @endif

                <!-- Action Button -->
                <div class="pt-2 flex flex-col gap-2">
                    <a class="btn btn-primary w-full" wire:navigate href="{{route('order.detail', ['regCode' => $order->reg_code])}}">
                        <i class="fa fa-eye"></i> View Details
                    </a>
                    @if ($order->transaction->payment_status != 'Paid')
                    <a class="btn btn-primary btn-outline w-full" wire:navigate href="{{route('order.confirm', ['regCode' => $order->reg_code])}}">
                        <i class="fa fa-file-upload"></i> Payment Confirmation
                    </a>
                    @endif
                </div>
            </div>

            <!-- Side Notches (Optional decorative element) -->
            <div class="absolute top-1/2 -left-3 w-6 h-6 bg-zinc-100 dark:bg-zinc-900 rounded-full transform -translate-y-1/2"></div>
            <div class="absolute top-1/2 -right-3 w-6 h-6 bg-zinc-100 dark:bg-zinc-900 rounded-full transform -translate-y-1/2"></div>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="text-center py-12 bg-zinc-50 dark:bg-zinc-800 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600">
            <i class="fa fa-ticket text-6xl text-zinc-300 dark:text-zinc-600 mb-4"></i>
            <h3 class="text-xl font-semibold text-zinc-700 dark:text-zinc-300 mb-2">No registrations found</h3>
            <p class="text-zinc-500 dark:text-zinc-400 mb-4">Start by creating your first registration</p>
            <a href="{{route('registration')}}" wire:navigate class="btn btn-primary">
                <i class="fa fa-plus"></i> Register!
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $orders->links() }}
</div>
</div>