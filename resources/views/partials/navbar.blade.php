<x-navigation.header />

<div id="navbar" class="w-full bg-transparent z-20 shadow-lg sticky lg:shadow-none lg:fixed transition-colors duration-300">
    <div class="drawer">
        <input id="my-drawer-3" type="checkbox" class="drawer-toggle" />
        <div class="drawer-content flex flex-col">
            <div class="navbar">
                <div class="navbar-start">
                    <img src="assets/images/logo/logo.png" class="h-full max-h-12" alt="Logo" />
                </div>
                <div class="navbar-center hidden lg:flex py-2">
                    <x-navigation.menu />
                </div>
                <div class="navbar-end gap-2">
                    <x-navigation.trolley class="hidden lg:block dropdown-end" />
                    <div class="flex-none lg:hidden">
                        <label for="my-drawer-3" aria-label="open sidebar" class="btn btn-square btn-ghost">
                            <i class="fa fa-bars text-warning dark:text-slate-50 text-2xl"></i>
                        </label>
                    </div>
                    <x-navigation.avatar />
                </div>
            </div>
        </div>
        <div class="drawer-side">
            <label for="my-drawer-3" aria-label="close sidebar" class="drawer-overlay"></label>
            <ul class="bg-base-200 min-h-full w-80 p-4">
                <!-- Sidebar content here -->
                <img src="assets/images/logo/logo.png" class="w-full mb-5 max-w-sm" />
                <x-navigation.menu-mobile />
            </ul>
        </div>
    </div>
</div>