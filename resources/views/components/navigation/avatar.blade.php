@if (Route::has('login'))
@auth
<div class="dropdown dropdown-end">
    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
        <div class="w-10 rounded-full">
            <img alt="Profile"
                src="https://ui-avatars.com/api/?name={{ Auth::user()->name}}+{{ Auth::user()->last_name}}" />
        </div>
    </div>
    <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
        <li>
            <a wire:navigate href="{{route('settings.profile')}}" class="justify-between">
                Profile
                <span class="badge">New</span>
            </a>
        </li>
        <li>
            <a href="{{route('dashboard')}}" wire:navigate>Dashboard</a>
        </li>

        <form class="w-full" action="{{route('logout')}}" method="POST">
            @csrf
            <button type="submit" class="w-full btn btn-ghost btn-xs mr-2 hover:cursor-pointer" href="{{route('logout')}}"> Logout <i class="fa-solid fa-right-from-bracket"></i></button>
        </form>

    </ul>
</div>
@else
<a href="{{ route('login') }}" wire:navigate
    class=" hidden lg:block px-5 py-1.5 dark:text-[#EDEDEC] text-warning border border-transparent hover:border-warning dark:hover:border-amber-700 rounded-md text-sm leading-normal">
    Login
</a>

@if (Route::has('register')) 
<a href="{{ route('register') }}" wire:navigate
    class=" hidden lg:block px-5 py-1.5 dark:text-[#EDEDEC] border-warning hover:border-amber-100 border text-warning dark:border-amber-700 dark:hover:border-[#62605b] rounded-md text-sm leading-normal">
    Register
</a>
@endif
@endauth
@endif