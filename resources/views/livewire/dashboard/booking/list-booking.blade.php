<div>
    <x-toast type="success" :message="session('success')" :duration="5000" />
    <x-toast type="error" :message="session('error')" />
    <x-toast type="info" :message="session('info')" />
    <div class="mb-3 gap-3 flex justify-end">
        <label class="input">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input wire:model.live.debounce.300ms='search' type="search" class="grow" placeholder="Search" />
        </label>
        <a href="{{route('accommodation')}}" wire:navigate class="btn btn-primary"><i class="fa fa-plus"></i> Add
            New</a>
    </div>

    {{-- @dd($bookings) --}}

    <div class="overflow-x-auto">
        <table class="table">
            <!-- head -->
            <thead>
                <tr>
                    <th>Booking Code</th>
                    <th>Hotel Name</th>
                    <th>Check in & Check out Date</th>
                    <th>Total Night</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            @foreach ($bookings as $booking)

            <tbody>
                <!-- row 1 -->
                <tr>
                    <th>{{$booking->booking_code}}</th>
                    <td>{{$booking->hotel->name}} <br>
                        @forelse ($booking->hotel->rooms as $item)
                        {{$item->room_type}}
                        @empty
                        @endforelse</td>
                    <td>{{\Carbon\Carbon::parse($booking->check_in_date)->format('d F Y')}} <br>
                        {{\Carbon\Carbon::parse($booking->check_out_date)->format('d F Y')}}</td>
                    <td>{{$booking->total_night}}</td>
                    <td>{{number_format($booking->subtotal)}}</td>
                    <td>{{number_format($booking->total)}}</td>
                    <td>{{$booking->status}}</td>
                    <td>{{$booking->bookingTransaction->payment_method}}</td>
                    <td>{{$booking->bookingTransaction->payment_status}}</td>
                </tr>
            </tbody>
            @endforeach
        </table>
    </div>
</div>