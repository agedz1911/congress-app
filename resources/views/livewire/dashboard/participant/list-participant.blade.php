<div>
    <x-toast type="success" :message="session('success')" :duration="5000" />
    <x-toast type="error" :message="session('error')" />
    <x-toast type="info" :message="session('info')" />
    <div class="mb-3 gap-3 flex justify-end">
        <label class="input">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input wire:model.live.debounce.300ms='search' type="search" class="grow" placeholder="Search" />
        </label>
        <a href="{{route('createparticipants')}}" wire:navigate class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
    </div>
    <div class="overflow-x-auto rounded-box border dark:border-zinc-50/5 boder-zinc-200">
        <table class="table ">
            <!-- head -->
            <thead class="text-zinc-700 dark:text-zinc-50 ">
                <tr>
                    <th>ID Participant</th>
                    <th>Full Name</th>
                    <th>NIK</th>
                    <th>Title Specialist</th>
                    <th>Speciality</th>
                    <th>Title</th>
                    <th>Name on Certificate</th>
                    <th>Institution</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Country</th>
                    <th>Province</th>
                    <th>City</th>
                    <th>Postal Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-zinc-700 dark:text-zinc-50">
                @foreach($participants as $participant)
                <tr>
                    <td>{{$participant->id_participant}}</td>
                    <td>{{$participant->first_name}} {{$participant->last_name}}</td>
                    <td>{{$participant->nik}}</td>
                    <td>{{$participant->title_specialist}}</td>
                    <td>{{$participant->speciality}}</td>
                    <td>{{$participant->title}}</td>
                    <td>{{$participant->name_on_certificate}}</td>
                    <td>{{$participant->institution}}</td>
                    <td>{{$participant->email}}</td>
                    <td>{{$participant->phone_number}}</td>
                    <td>{{$participant->country}}</td>
                    <td>{{$participant->province}}</td>
                    <td>{{$participant->city}}</td>
                    <td>{{$participant->postal_code}}</td>
                    <td>
                        <div class="flex gap-1">
                            <a href="{{route('editparticipants', ['id' => $participant->id])}}" class="btn btn-xs btn-primary">Edit</a>
                            <button wire:click="destroy('{{$participant->id}}')"
                                wire:confirm="Apakah kamu yakin ingin menghapus participant ini?"
                                class="btn btn-xs btn-error">Delete</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $participants->links() }}
</div>