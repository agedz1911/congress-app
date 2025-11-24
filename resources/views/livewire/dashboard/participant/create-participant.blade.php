<div>
    <div
        class="bg-base-100 border-base-100 rounded-box rounded-lg border p-4 grid grid-cols-1 lg:grid-cols-2">
        <fieldset class="fieldset">
            <legend class="fieldset-legend">First Name</legend>
            <input type="text" class="input" placeholder="John" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Last Name</legend>
            <input type="text" class="input" placeholder="Doe" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">NIK</legend>
            <input type="number" class="input" placeholder="2353 2543 5435 6445" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Title</legend>
            <select class="select">
                <option disabled selected>Choose Option</option>
                <option>Prof.</option>
                <option>MD</option>
                <option>Mr</option>
                <option>Ms</option>
                <option>Miss</option>
            </select>
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Title specialist</legend>
            <input type="text" class="input" placeholder="SpU, SpBP, SpBS" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Speciality</legend>
            <select class="select">
                <option disabled selected>Select Option</option>
                <option>Specialist</option>
                <option>Resident</option>
                <option>General Practitioner</option>
                <option>Medical Student</option>

            </select>
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Name on certificate</legend>
            <input type="text" class="input" placeholder="John Doe, SpU" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Institution</legend>
            <input type="text" class="input" placeholder="" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Email</legend>
            <input type="email" class="input" placeholder="" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Phone Number</legend>
            <input type="tel" class="input" placeholder="" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Country</legend>
            <select class="select">
                <option disabled selected>Choose Country</option>
                @foreach ($countries as $country)
                <option>{{$country['name']}}</option>
                @endforeach
            </select>
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Province</legend>
            <input type="text" class="input" placeholder="" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">City</legend>
            <input type="text" class="input" placeholder="" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Postal Code</legend>
            <input type="number" class="input" placeholder="" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">Address</legend>
            <textarea class="textarea" placeholder="Bio"></textarea>
            <p class="label text-error text-xs">error</p>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend">ID Participant</legend>
            <input type="text" class="input " disabled placeholder="" />
            <p class="label text-error text-xs">error</p>
        </fieldset>
    </div>
    <div class="mt-4 flex justify-end gap-2">
        <button class="btn btn-primary">Save</button>
        <a href="{{route('myparticipants')}}" wire:navigate class="btn btn-error">Cancel</a>
    </div>
</div>