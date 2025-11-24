<?php

use App\Livewire\Actions\Cart;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Website\Homepage;
use App\Livewire\Website\Registration;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::get('/', Homepage::class)->name('home');
Route::get('/registration', Registration::class)->name('registration');
Route::get('/cart', Cart::class)->name('cart');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

Route::middleware(['auth'])->group(function () {
    // Route::redirect('dashboard', 'dashboard/participants');

    Route::get('dashboard/participants', App\Livewire\Dashboard\Participant\MyParticipant::class)->name('myparticipants');
    Route::get('dashboard/bookings', App\Livewire\Dashboard\Booking\MyBooking::class)->name('mybookings');
    Route::get('dashboard/registrations', App\Livewire\Dashboard\Registration\MyRegistration::class)->name('myregistrations');
});

require __DIR__ . '/auth.php';
