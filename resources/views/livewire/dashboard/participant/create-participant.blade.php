<div>
    <x-toast type="success" :message="session('success')" :duration="5000" />
    <x-toast type="error" :message="session('error')" />
    <x-toast type="info" :message="session('info')" />

    @if($fromCart)
    <div class="alert alert-info mb-4">
        <i class="fa fa-info-circle"></i>
        <span>You're adding participant details for your order. After creating, you'll be redirected back to continue your checkout.</span>
    </div>
    @endif

    <div>
        <div class="breadcrumbs text-sm text-zinc-700 dark:text-zinc-50">
            <ul>
                <li><a href="{{route('dashboard')}}" wire:navigate>Dashboard</a></li>
                @if($fromCart)
                <li><a href="{{route('reg-cart')}}" wire:navigate>Cart</a></li>
                @else
                <li><a href="{{route('myparticipants')}}" wire:navigate>Participants</a></li>
                @endif
                <li>Create</li>
            </ul>
        </div>
        <h4 class="text-xl text-zinc-700 dark:text-zinc-50">Create Participant</h4>
    </div>

    {{-- Auto Fill from User Data --}}
    @if($hasUserData)
    <div class="alert alert-success mb-4 flex justify-between items-center">
        <div>
            <i class="fa fa-lightbulb"></i>
            <span>We detected your profile information. You can auto-fill the form to save time!</span>
        </div>
        <button type="button" wire:click="fillFromUserData" class="btn btn-sm btn-primary gap-2">
            <i class="fa fa-magic"></i>
            Auto Fill from Profile
        </button>
    </div>
    @endif

    <form wire:submit.prevent="create">
        <div class="p-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- First Name --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">First Name <span class="text-error">*</span></legend>
                <input type="text" class="input @error('first_name') border-error @enderror" wire:model="first_name"
                    placeholder="John" />
                @error('first_name')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Last Name --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Last Name <span class="text-error">*</span></legend>
                <input type="text" class="input @error('last_name') border-error @enderror" wire:model="last_name"
                    placeholder="Doe" />
                @error('last_name')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- NIK --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">NIK <span class="text-error">*</span></legend>
                <input type="text" class="input @error('nik') border-error @enderror" wire:model="nik"
                    placeholder="2353 2543 5435 6445" />
                @error('nik')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Title --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Title <span class="text-error">*</span></legend>
                <select class="select @error('title') border-error @enderror" wire:model="title">
                    <option disabled value="">Choose Option</option>
                    <option value="Prof.">Prof.</option>
                    <option value="MD">MD</option>
                    <option value="Mr">Mr</option>
                    <option value="Ms">Ms</option>
                    <option value="Miss">Miss</option>
                </select>
                @error('title')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Title Specialist --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Title specialist <span class="text-error">*</span></legend>
                <input type="text" class="input @error('title_specialist') border-error @enderror"
                    wire:model="title_specialist" placeholder="SpU, SpBP, SpBS" />
                @error('title_specialist')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Speciality --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Speciality <span class="text-error">*</span></legend>
                <select class="select @error('speciality') border-error @enderror" wire:model="speciality">
                    <option disabled value="">Select Option</option>
                    <option value="Specialist">Specialist</option>
                    <option value="Resident">Resident</option>
                    <option value="General Practitioner">General Practitioner</option>
                    <option value="Medical Student">Medical Student</option>
                </select>
                @error('speciality')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Name on Certificate --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Name on certificate <span class="text-error">*</span></legend>
                <input type="text" class="input @error('name_on_certificate') border-error @enderror"
                    wire:model="name_on_certificate" placeholder="John Doe, SpU" />
                @error('name_on_certificate')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Institution --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Institution <span class="text-error">*</span></legend>
                <input type="text" class="input @error('institution') border-error @enderror" wire:model="institution"
                    placeholder="" />
                @error('institution')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Email --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Email <span class="text-error">*</span></legend>
                <input type="email" class="input @error('email') border-error @enderror" wire:model="email"
                    placeholder="" />
                @error('email')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Phone Number --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Phone Number <span class="text-error">*</span></legend>
                <input type="tel" class="input @error('phone_number') border-error @enderror" wire:model="phone_number"
                    placeholder="" />
                @error('phone_number')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Country --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Country <span class="text-error">*</span></legend>
                <select class="select @error('country') border-error @enderror" wire:model="country">
                    <option disabled value="">Choose Country</option>
                    @foreach ($countries as $country)
                    <option value="{{$country['name']}}">{{$country['name']}}</option>
                    @endforeach
                </select>
                @error('country')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Province --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Province <span class="text-error">*</span></legend>
                <input type="text" class="input @error('province') border-error @enderror" wire:model="province"
                    placeholder="" />
                @error('province')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- City --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">City <span class="text-error">*</span></legend>
                <input type="text" class="input @error('city') border-error @enderror" wire:model="city"
                    placeholder="" />
                @error('city')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Postal Code --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Postal Code <span class="text-error">*</span></legend>
                <input type="text" class="input @error('postal_code') border-error @enderror" wire:model="postal_code"
                    placeholder="" />
                @error('postal_code')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Address --}}
            <fieldset class="fieldset lg:col-span-2">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">Address <span class="text-error">*</span></legend>
                <textarea class="textarea @error('address') border-error @enderror" wire:model="address"
                    placeholder="Street address"></textarea>
                @error('address')
                <p class="label text-error text-xs">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- ID Participant (Read Only) --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-zinc-700 dark:text-zinc-50">ID Participant</legend>
                <input type="text" class="input" disabled wire:model="id_participant" />
            </fieldset>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-6 flex justify-start gap-2">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="create">
                    <i class="fa fa-check"></i>
                    @if($fromCart)
                    Create & Continue Order
                    @else
                    Create Participant
                    @endif
                </span>
                <span wire:loading wire:target="create">
                    <span class="loading loading-spinner loading-xs"></span>
                    Creating...
                </span>
            </button>

            @if(!$fromCart)
            <button type="button" class="btn btn-soft btn-primary" wire:click="createAnother"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="createAnother">
                    <i class="fa fa-plus"></i>
                    Create & Create Another
                </span>
                <span wire:loading wire:target="createAnother">
                    <span class="loading loading-spinner loading-xs"></span>
                    Creating...
                </span>
            </button>
            @endif

            <a href="{{$fromCart ? route('reg-cart') : route('myparticipants')}}" wire:navigate class="btn btn-ghost">
                <i class="fa fa-times"></i>
                Cancel
            </a>
        </div>
    </form>
</div>