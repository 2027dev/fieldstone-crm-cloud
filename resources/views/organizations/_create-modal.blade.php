<x-modal name="organization" title="Add organization">
    <form method="post" action="{{ route('organizations.store') }}">
        @csrf
        <input type="hidden" name="_modal" value="organization">
        <div class="px-5 py-4">@include('organizations._form', ['organization' => null])</div>
        <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
</x-modal>
