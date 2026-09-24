<x-modal name="deal" title="Add deal" width="max-w-2xl">
    <form method="post" action="{{ route('deals.store') }}">
        @csrf
        <input type="hidden" name="_modal" value="deal">
        <div class="px-5 py-4">@include('deals._form', ['deal' => null])</div>
        <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
</x-modal>
