<?php

namespace App\Livewire\Dashboard\Participant;

use App\Models\Registration\Participant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;


#[Title('Edit Participant')]
class EditParticipant extends Component
{
    public $countries;
    public $participant;
    public $participantId;

    #[Validate('required|string|max:255')]
    public $first_name = '';

    #[Validate('required|string|max:255')]
    public $last_name = '';

    #[Validate('required|string|max:255')]
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

    public $email = '';

    #[Validate('required|numeric|digits_between:10,14')]
    public $phone_number = '';

    #[Validate('required|string')]
    public $country = '';

    #[Validate('required|string|max:255')]
    public $province = '';

    #[Validate('required|string|max:255')]
    public $city = '';

    #[Validate('required|numeric|digits_between:6,10')]
    public $postal_code = '';

    #[Validate('required|string')]
    public $address = '';

    // #[Validate('nullable|string')]
    // public $participant_type = '';

    public $id_participant = '';

    public function mount($id)
    {
        $this->countries = countries();

        if (!Auth::check()) {
            session()->flash('error', 'Please login to edit participant.');
            return redirect()->route('login');
        }
        $this->participant = Participant::findOrFail($id);

        if ($this->participant->user_id !== Auth::id()) {
            session()->flash('error', 'You are not authorized to edit this participant.');
            return redirect()->route('myparticipants');
        }

        $this->fill($this->participant->only([
            'id_participant',
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
            'participant_type',
        ]));
        $this->participantId = $this->participant->id;
    }



    public function update()
    {
        $this->validate();

        try {
            $participant = Participant::findOrFail($this->participantId);
            $participant->update([
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
                // 'participant_type' => $this->participant_type,
            ]);

            session()->flash('success', 'Participant updated successfully.');
            // return redirect()->route('myparticipants');
            $this->participant->refresh();
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the participant: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dashboard.participant.edit-participant');
    }
}
