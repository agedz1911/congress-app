<div>
    <div class="breadcrumbs text-sm text-zinc-700 dark:text-zinc-50">
        <ul>
            <li><a href="{{route('dashboard')}}" wire:navigate>Dashboard</a></li>

            <li><a href="{{route('myregistrations')}}" wire:navigate>MyRegistration</a></li>

            <li>Confirmation</li>
        </ul>
    </div>
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="flex justify-between">
                <h2 class="card-title text-2xl mb-4">Payment Confirmation</h2>
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
                    <p class="font-bold text-lg">IDR {{ number_format($order->total, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Payment Method</label>
                    <p class="font-semibold">{{ ucwords(str_replace('_', ' ', $order->transaction->payment_method))
                        }}</p>
                </div>
            </div>
        </div>
    </div>
</div>