<x-modal name="person" title="Add person">
    <form method="post" action="{{ route('people.store') }}">
        @csrf
        <input type="hidden" name="_modal" value="person">
        <div class="px-5 py-4">
            @include('people._form', ['person' => null])
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3 rounded-b-xl">
            <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
</x-modal>
