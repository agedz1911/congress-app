<?php

namespace App\Livewire\Dashboard\Participant;

use App\Models\Registration\Participant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Create Participant')]
class CreateParticipant extends Component
{
    public $countries;

    #[Validate('required|string|max:255')]
    public $first_name = '';

    #[Validate('required|string|max:255')]
    public $last_name = '';

    #[Validate('required|string|max:16')]
    public $nik = '';

    #[Validate('required|string')]
    public $title = '';

    #[Validate('required|string|max:255')]
    public $title_specialist = '';

    #[Validate('required|string')]
    public $speciality = '';

    #[Validate('required|string|max:255')]
    public $name_on_certificate = '';

    #[Validate('required|string|max:255')]
    public $institution = '';

    #[Validate('required|email|max:255')]
    public $email = '';

    #[Validate('required|string|max:20')]
    public $phone_number = '';

    #[Validate('required|string')]
    public $country = '';

    #[Validate('required|string|max:255')]
    public $province = '';

    #[Validate('required|string|max:255')]
    public $city = '';

    #[Validate('required|string|max:10')]
    public $postal_code = '';

    #[Validate('required|string')]
    public $address = '';

    // #[Validate('nullable|string')]
    // public $participant_type = '';

    public $id_participant = '';
    public $fromCart = false;
    public $hasUserData = false;

    public function mount()
    {
        $this->countries = countries();

        if (!Auth::check()) {
            session()->flash('error', 'Please login to create participant.');
            return redirect()->route('login');
        }
        $this->fromCart = session()->has('return_to_cart_step');

        $this->generateIdParticipant();
        $this->checkUserData();
    }

    public function checkUserData()
    {
        $user = Auth::user();

        // Check apakah user memiliki data yang bisa di-fill
        if ($user->name || $user->last_name || $user->email || $user->phone || $user->country) {
            $this->hasUserData = true;
        }
    }

    public function fillFromUserData()
    {
        $user = Auth::user();

        try {
            // Pisahkan first name dan last name dari user name
            $this->first_name = $user->name ?? '';
            $this->last_name = $user->last_name ?? '';

            // Fill email
            $this->email = $user->email ?? '';

            // Fill phone number
            $this->phone_number = $user->phone ?? '';

            // Fill country
            if ($user->country) {
                $this->country = $user->country;
            }

            // Fill province
            if ($user->province) {
                $this->province = $user->province;
            }

            // Fill city
            if ($user->city) {
                $this->city = $user->city;
            }

            // Fill postal code
            if ($user->postal_code) {
                $this->postal_code = $user->postal_code;
            }

            // Fill address
            if ($user->address) {
                $this->address = $user->address;
            }

            // Set name on certificate dengan format yang umum
            $this->name_on_certificate = trim($user->name) ?? '';

            session()->flash('success', 'Data filled from your profile! Please complete remaining fields.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to fill data from profile: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $this->validate();

        try {
            $participant = Participant::create([
                'id_participant' => $this->id_participant,
                'user_id' => Auth::id(),
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'nik' => $this->nik,
                'title' => $this->title,
                'title_specialist' => $this->title_specialist,
                'speciality' => $this->speciality,
                'name_on_certificate' => $this->name_on_certificate,
                'institution' => $this->institution,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'country' => $this->country,
                'province' => $this->province,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
                'address' => $this->address,
                'participant_type' => ['Participant'],
            ]);

            if (session()->has('cart') && session()->has('return_to_cart_step')) {
                session()->put('selected_participant_id', $participant->id);
                session()->put('cart_step', 2);
                session()->forget('return_to_cart_step');
                session()->flash('success', 'Participant created successfully! Continue with your order.');
                return $this->redirect(route('reg-cart'), navigate: true);
            }

            session()->flash('success', 'Participant created successfully!');
            return $this->redirect(route('myparticipants'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create participant: ' . $e->getMessage());
        }
    }

    public function createAnother()
    {
        $this->validate();

        try {
            Participant::create([
                'id_participant' => $this->id_participant,
                'user_id' => Auth::id(),
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'nik' => $this->nik,
                'title' => $this->title,
                'title_specialist' => $this->title_specialist,
                'speciality' => $this->speciality,
                'name_on_certificate' => $this->name_on_certificate,
                'institution' => $this->institution,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'country' => $this->country,
                'province' => $this->province,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
                'address' => $this->address,
                'participant_type' => ['Participant'],
            ]);

            session()->flash('success', 'Participant created successfully! You can create another one.');
            // Reset form
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create participant: ' . $e->getMessage());
        }
    }

    public function generateIdParticipant()
    {
        $this->id_participant = Participant::generateIdParticipant();
    }

    public function resetForm()
    {
        $this->reset([
            'first_name',
            'last_name',
            'nik',
            'title',
            'title_specialist',
            'speciality',
            'name_on_certificate',
            'institution',
            'email',
            'phone_number',
            'country',
            'province',
            'city',
            'postal_code',
            'address',
        ]);

        $this->resetValidation();
        $this->generateIdParticipant();
    }

    public function render()
    {
        return view('livewire.dashboard.participant.create-participant');
    }
}
