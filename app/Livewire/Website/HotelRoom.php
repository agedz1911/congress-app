<?php

namespace App\Livewire\Website;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.website')]
#[Title('Accommodation')]
class HotelRoom extends Component
{
    public $hotel;

    public $cartCount = 0;
    public $cartTotal = 0;
    public $cartCurrency = 'IDR';

    public function mount($id)
    {
        $this->hotel = \App\Models\Accommodation\Hotel::with('rooms')->findOrFail($id);
        $this->updateCartInfo();
    }

    public function addToCart($roomId)
    {
        $room = $this->hotel->rooms->find($roomId);

        if (!$room) {
            session()->flash('error', 'Room not found');
            return;
        }

        // Get user's country - dapat dari auth user atau deteksi IP
        $userCountry = $this->getUserCountry();
        $isIndonesian = $userCountry === 'ID' || $userCountry === 'Indonesia';

        // Tentukan harga berdasarkan negara
        $price = $isIndonesian ? $room->price_idr : $room->price_usd;
        $currency = $isIndonesian ? 'IDR' : 'USD';

        // Generate booking code
        $bookingCode = 'ACC-' . random_int(10000, 99999);

        // Persiapkan data cart item
        $cartItem = [
            'booking_code' => $bookingCode,
            'hotel_id' => $this->hotel->id,
            'hotel_name' => $this->hotel->name,
            'hotel_image' => $this->hotel->feature_image,
            'room_id' => $room->id,
            'room_type' => $room->room_type,
            'room_image' => $room->image,
            'price' => $price,
            'currency' => $currency,
            'check_in_date' => null, // Akan diisi saat checkout
            'check_out_date' => null, // Akan diisi saat checkout
            'total_night' => 1,
            'subtotal' => $price,
            'status' => 'pending',
            'added_at' => now()->toDateTimeString(),
        ];

        // Ambil cart dari session atau buat array baru
        $cart = session()->get('hotel_cart', []);

        // Cek apakah room ini sudah ada di cart
        $existingItemKey = null;
        foreach ($cart as $key => $item) {
            if ($item['room_id'] == $roomId && $item['hotel_id'] == $this->hotel->id) {
                $existingItemKey = $key;
                break;
            }
        }

        if ($existingItemKey !== null) {
            // Update jika sudah ada
            session()->flash('info', 'Room already in cart');
        } else {
            // Tambah item baru
            $cart[] = $cartItem;
            session()->put('hotel_cart', $cart);
            session()->flash('success', 'Room added to cart successfully');
        }

        // Update cart info
        $this->updateCartInfo();

        // Refresh component untuk update UI
        $this->dispatch('cartUpdated');
    }

    public function removeFromCart($cartKey)
    {
        $cart = session()->get('hotel_cart', []);
        
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('hotel_cart', array_values($cart)); // Re-index array
            session()->flash('success', 'Room removed from cart');
            $this->updateCartInfo();
        }
    }

    public function clearCart()
    {
        session()->forget('hotel_cart');
        session()->flash('success', 'Cart cleared');
        $this->updateCartInfo();
    }

    public function updateCartInfo()
    {
        $cart = session()->get('hotel_cart', []);
        $this->cartCount = count($cart);
        
        $this->cartTotal = collect($cart)->sum('subtotal');
        
        // Ambil currency dari item pertama jika ada
        if (!empty($cart)) {
            $this->cartCurrency = $cart[0]['currency'];
        }
    }

    private function getUserCountry()
    {
        // Metode 1: Jika user sudah login dan ada field country di user
        if (auth()->check()) {
            // Asumsi user model memiliki field country
            return auth()->user()->country ?? 'ID';
        }

        // Metode 2: Deteksi dari IP address
        // Anda bisa menggunakan package seperti stevebauman/location
        // atau ipinfo.io API

        // Untuk demo, gunakan session atau default
        // return session('user_country', 'ID');
        
        // Alternatif: Deteksi IP (perlu library tambahan)
        // $ip = request()->ip();
        // $location = \Stevebauman\Location\Location::get($ip);
        // return $location->countryCode ?? 'ID';
    }

    public function render()
    {
        return view('livewire.website.hotel-room');
    }
}
