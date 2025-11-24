<div>
    <div class="flex justify-between mb-2">
        <h4 class="text-xl">List Participant</h4>
        <button onclick="addnew.showModal()" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</button>
    </div>
    <livewire:dashboard.participant.list-participant />

    <dialog id="addnew" class="modal">
        <div class="modal-box w-11/12 max-w-5xl">
            <h3 class="text-lg font-bold">Hello!</h3>
            <livewire:dashboard.participant.create-participant />
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            
        </div>
    </dialog>
</div>