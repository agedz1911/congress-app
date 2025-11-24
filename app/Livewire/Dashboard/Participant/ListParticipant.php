<?php

namespace App\Livewire\Dashboard\Participant;

use App\Models\Registration\Participant;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ListParticipant extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function destroy($id)
    {
        $participant = Participant::findOrFail($id);
        $participant->delete();
        session()->flash('message', 'Participant deleted successfully.');
        $this->resetPage();
    }

    public function render()
    {
        $participants = Participant::search($this->search)->where('user_id', auth()->id())->paginate($this->perPage);
        return view('livewire.dashboard.participant.list-participant', [
            'participants' => $participants,
        ]);
    }
}
