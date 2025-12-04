<flux:navlist.item 
    icon="shopping-cart" 
    :badge="$cartCount > 0 ? (string)$cartCount : 0" 
    :href="route('reg-cart')"
    :current="request()->routeIs('reg-cart')"
    wire:navigate>
    {{ __('My Cart') }}
</flux:navlist.item>