<div class="dropdown dropdown-end">
    <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
        <div class="indicator">
            <i class="fa fa-cart-shopping text-lg"></i>
            @if ($cartCount > 0)
            <span class="badge badge-sm indicator-item bg-base-100/50"> <span
                    class="text-warning">{{$cartCount}}</span></span>
            @else
            <span class="badge badge-sm indicator-item bg-base-100/50"> <span class="text-warning">0</span></span>
            @endif
        </div>
    </div>
    <div tabindex="0" class="card card-compact dropdown-content bg-base-100 z-1 mt-3 w-52 shadow">
        <div class="card-body">
            @if ($cartCount)
            <span class="text-lg font-bold">{{$cartCount}} Items</span>
            @else
            <span class="text-lg font-bold">0 Items</span>
            @endif
            {{-- <span class="text-info">Subtotal: $999</span> --}}
            <div class="card-actions">
                <a href="{{route('reg-cart')}}" class="btn btn-primary btn-block">View cart</a>
            </div>
        </div>
    </div>
</div>