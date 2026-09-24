<x-modal name="activity" title="Schedule an activity" width="max-w-2xl">
    <form method="post" action="{{ route('activities.store') }}">
        @csrf
        <input type="hidden" name="_modal" value="activity">
        <div class="px-5 py-4">
            @include('activities._form', ['activity' => null, 'defaults' => $defaults ?? []])
        </div>
        <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
</x-modal>
