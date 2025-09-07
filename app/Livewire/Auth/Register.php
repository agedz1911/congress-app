<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $last_name = '';
    public string $country = '';
    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';
    public $countries;
    /**
     * Handle an incoming registration request.
     */
    public function mount()
    {
        $this->countries = countries();
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));
        $user->assignRole('user');
        Notification::make()
            ->title('User Created successfully')
            ->success()
            ->send();


        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
