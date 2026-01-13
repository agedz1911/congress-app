<ul class="flex gap-4 uppercase">
    <li>
        <a href="/" wire:navigate
            class="{{ request()->is('/') ? 'text-amber-400' : 'text-white' }} hover:text-amber-500 hover:underline ">Home
        </a>
    </li>
    <div class="dropdown dropdown-hover">
        <div tabindex="0"
            class="{{ request()->is('organizing-committee') || request()->is('faculties') ? 'text-amber-400' : 'text-white' }} hover:cursor-pointer hover:text-amber-500">
            Congress Information <i class="fa-solid fa-angle-down"></i></div>
        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box gap-2 w-60 p-2 shadow-sm">
            <li>
                <a href="/organizing-committee" wire:navigate
                    class="{{ request()->is('organizing-committee') ? 'text-amber-400' : '' }} justify-between hover:text-amber-400 ">Organizing
                    Committee <i class="fa-solid fa-angle-right"></i></a>
            </li>
            <li>
                <a href="/faculties" wire:navigate
                    class="{{ request()->is('faculties') ? 'text-amber-400' : '' }} justify-between hover:text-amber-400 ">Faculties
                    <i class="fa-solid fa-angle-right"></i></a>
            </li>
        </ul>
    </div>

    <div class="dropdown dropdown-hover">
        <div tabindex="0"
            class="{{ request()->is('program-at-glance') || request()->is('scientific-schedule') ? 'text-amber-400' : 'text-white' }} hover:cursor-pointer hover:text-amber-500">
            Scientific Program <i class="fa-solid fa-angle-down"></i></div>
        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box gap-2 w-60 p-2 shadow-sm">
            <li>
                <a href="/program-at-glance" wire:navigate
                    class="{{ request()->is('program-at-glance') ? 'text-amber-400' : '' }} justify-between hover:text-amber-400">Program
                    at Glance <i class="fa-solid fa-angle-right"></i></a>
            </li>
            <li>
                <a href="/scientific-schedule" wire:navigate
                    class="{{ request()->is('scientific-schedule') ? 'text-amber-400' : '' }} justify-between hover:text-amber-400">Scientific
                    Schedule <i class="fa-solid fa-angle-right"></i></a>
            </li>
    </div>


    <li>
        <a href="{{route('registration')}}" wire:navigate
            class="{{ request()->is('registration') ? 'text-amber-400' : 'text-white' }} hover:text-amber-500 hover:underline">Registration
        </a>
    </li>
    <li>
        <a href="{{route('accommodation')}}" wire:navigate
            class="{{ request()->is('accommodation') || request()->is('accommodation/*') ? 'text-amber-400' : 'text-white' }} hover:text-amber-500 hover:underline">Accommodation
        </a>
    </li>
    <li>
        <a href="/submission" wire:navigate
            class="{{ request()->is('submission') ? 'text-amber-400' : 'text-white' }} hover:text-amber-500 hover:underline">Submission
        </a>
    </li>
    <li>
        <a href="#" wire:navigate
            class="{{ request()->is('visiting') ? 'text-amber-400' : 'text-white' }} hover:text-amber-500 hover:underline">Venue
        </a>
    </li>
</ul>