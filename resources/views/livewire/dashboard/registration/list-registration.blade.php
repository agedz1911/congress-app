<div>
    <div class="mb-3 gap-3 flex justify-end">
        <label class="input">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input wire:model.live.debounce.300ms='search' type="search" class="grow" placeholder="Search" />
        </label>
        <a href="#" wire:navigate class="btn btn-primary"><i class="fa fa-plus"></i> Add
            New</a>
    </div>

    <div class="overflow-x-auto rounded-box border dark:border-zinc-50/5 boder-zinc-200">
        <table class="table ">
            <!-- head -->
            <thead class="text-zinc-700 dark:text-zinc-50 ">
                <tr>
                    <th>Order ID</th>
                    <th>Full Name</th>
                    <th>Country</th>
                    <th>Status</th>
                    <th>Product</th>

                    <th>Coupon</th>
                    <th>Discount</th>
                    <th>Total</th>
                    <th>Payment Method</th>
                    <th>Payment Date</th>
                    <th>Payment Status</th>
                    <th>Paid Amount</th>
                    <th>Total Kurs</th>
                </tr>
            </thead>
            <tbody class="text-zinc-700 dark:text-zinc-50">
                @foreach($orders as $order)
                <tr>
                    <td>{{$order->reg_code}}</td>
                    <td>{{$order->participant->first_name}} {{$order->participant->last_name}}</td>
                    <td>{{$order->participant->country}}</td>
                    <td>
                        @if ($order->status == 'Validated')
                        <div class="badge badge-sm badge-success">
                            <i class="fa fa-circle-check"></i>{{$order->status}}
                        </div>
                        @elseif ($order->status == 'Processing')
                        <div class="badge badge-sm badge-warning">
                            <i class="fa fa-rotate"></i> {{$order->status}}
                        </div>
                        @elseif ($order->status == 'New')
                        <div class="badge badge-sm badge-primary">
                            <i class="fa fa-wand-magic-sparkles"></i> {{$order->status}}
                        </div>
                        @elseif ($order->status == 'Cancelled')
                        <div class="badge badge-sm badge-error">
                            <i class="fa fa-circle-xmark"></i> {{$order->status}}
                        </div>
                        @endif
                    </td>
                    <td>
                        @forelse($order->items as $item)
                        <div class="flex flex-col">
                            <div class="badge badge-sm badge-outline badge-info">
                                {{ $item->product->name ?? 'N/A' }}
                            </div>
                            <p class="text-xs">
                                Qty: {{ $item->quantity }}, <br>
                                Price: {{ number_format($item->unit_price, 0, ',', '.') }}
                            </p>
                        </div>
                        @empty
                        <span class="text-muted">No items</span>
                        @endforelse
                    </td>

                    <td>{{$order->coupon}}</td>
                    <td>{{number_format($order->discount, 0, ',', '.')}}</td>
                    <td>{{number_format($order->total, 0, ',', '.')}}</td>
                    <td>
                        @if ($order->transaction->payment_method == 'Bank Transfer')
                        <div class="badge badge-sm badge-outline badge-primary">
                            <i class="fa fa-money-bill-transfer"></i> {{$order->transaction->payment_method}}
                        </div>
                        @else
                        <div class="badge badge-sm badge-outline badge-success">
                            <i class="fa fa-credit-card"></i> {{$order->transaction->payment_method}}
                        </div>
                        @endif
                    </td>
                    <td>
                        @if ($order->transaction->payment_date === null)
                        @else
                        {{\Carbon\Carbon::parse($order->transaction->payment_date)->format('d F, Y')}}</td>
                    @endif
                    <td>
                        @if ($order->transaction->payment_status == 'Paid')
                        <div class="badge badge-sm badge-success">
                            <i class="fa fa-circle-check"></i> {{$order->transaction->payment_status}}
                        </div>
                        @else
                        <div class="badge badge-sm badge-error">
                            <i class="fa fa-circle-xmark"></i> {{$order->transaction->payment_status}}
                        </div>
                        @endif
                    </td>
                    <td>{{number_format($order->transaction->amount, 0, ',', '.') }}</td>
                    <td>
                        {{number_format($order->transaction->kurs, 0, ',', '.')}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $orders->links() }}
</div>