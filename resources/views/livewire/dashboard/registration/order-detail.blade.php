<div class="">
    <div class="">
        <div class="breadcrumbs text-sm text-zinc-700 dark:text-zinc-50">
            <ul>
                <li><a href="{{route('dashboard')}}" wire:navigate>Dashboard</a></li>
                <li><a href="{{route('myregistrations')}}" wire:navigate>MyRegistration</a></li>
                <li>View</li>
            </ul>
        </div>

        {{-- Order Summary Card --}}
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <div class="flex justify-between">
                    <h2 class="card-title text-2xl mb-4">Order Details</h2>
                    @if ($order->transaction->payment_status === 'Unpaid')
                    <div class="badge badge-error">{{$order->transaction->payment_status}}</div>
                    @else
                    <div class="badge badge-success">{{$order->transaction->payment_status}}</div>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="text-sm text-gray-500">Registration Code</label>
                        <p class="font-mono font-bold text-lg">{{ $order->reg_code }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Status</label>
                        <p>
                            @if ($order->status === 'New')
                            <span class="badge badge-primary">{{ ucfirst($order->status) }}</span>
                            @elseif ($order->status === 'Processing')
                            <span class="badge badge-warning">{{ ucfirst($order->status) }}</span>
                            @elseif ($order->status === 'Validated')
                            <span class="badge badge-success">{{ ucfirst($order->status) }}</span>
                            @else
                            <span class="badge badge-error">{{ ucfirst($order->status) }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Total Amount</label>
                        <p class="font-bold text-lg">{{Auth()->user()->country != 'Indonesia' ? 'USD' : 'IDR'}} {{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <div>
                            <label class="text-sm text-gray-500">Payment Method</label>
                            <p class="font-semibold">{{ ucwords(str_replace('_', ' ',
                                $order->transaction->payment_method))
                                }}</p>
                        </div>
                        <div class="w-full max-w-60">
                            @if($order->transaction->attachment !=null )
                            <div class="">
                                <label class="text-sm text-gray-500"> Attachment</label>
                                <img class="rounded-lg shadow-md" src="{{ asset('storage/' . $order->transaction->attachment)}}" alt="Payment Proof">
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="divider">Order Items</div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{Auth()->user()->country != 'Indonesia' ? 'USD' : 'IDR'}} {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td>{{Auth()->user()->country != 'Indonesia' ? 'USD' : 'IDR'}} {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($order->discount > 0)
                <div class="flex justify-end mt-4 text-success">
                    <span class="mr-4">Discount @if($order->coupon)({{ $order->coupon }})@endif:</span>
                    <span class="font-bold">- {{Auth()->user()->country != 'Indonesia' ? 'USD' : 'IDR'}} {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Payment Instructions --}}
        @if($order->transaction->payment_method === 'Bank Transfer' && $order->transaction->payment_status === 'Unpaid')
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <h3 class="card-title">Payment Instructions</h3>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <span>Please complete your payment within 24 hours</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="font-semibold">Bank Name</label>
                        <p>Bank Central Asia (BCA)</p>
                    </div>
                    <div>
                        <label class="font-semibold">Account Number</label>
                        <p class="font-mono text-lg">1234567890</p>
                    </div>
                    <div>
                        <label class="font-semibold">Account Name</label>
                        <p>PT Event Organizer</p>
                    </div>
                    <div>
                        <label class="font-semibold">Amount to Transfer</label>
                        <p class="text-xl font-bold text-primary">{{Auth()->user()->country != 'Indonesia' ? 'USD' : 'IDR'}} {{ number_format($order->total, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="card-actions justify-end mt-6">
                    {{-- <a href="{{ route('payment.upload', $order->reg_code) }}" class="btn btn-primary">
                        Upload Payment Proof
                    </a> --}}
                </div>
            </div>
        </div>
        @endif

        {{-- Participant Info --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">Participant Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500">Name</label>
                        <p class="font-semibold">{{ $order->participant->first_name }} {{ $order->participant->last_name
                            }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Email</label>
                        <p class="font-semibold">{{ $order->participant->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Institution</label>
                        <p class="font-semibold">{{ $order->participant->institution ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Country</label>
                        <p class="font-semibold">{{ $order->participant->country }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-center gap-4 mt-8">
            <a href="{{ route('myregistrations') }}" wire:navigate class="btn btn-outline">Back to Dashboard</a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print Order
            </button>
            <a href="{{route('order.confirm', ['regCode' => $order->reg_code])}}" wire:navigate class="btn btn-ghost"><i
                    class="fa fa-file-upload"></i> Payment Confirmation</a>
        </div>
    </div>
</div>