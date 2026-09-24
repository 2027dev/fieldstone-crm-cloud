<x-app-layout title="Edit person" :breadcrumbs="['Contacts' => route('people.index'), $person->name => route('people.show', $person)]">
    <x-slot:subnav>@include('contacts._subnav')</x-slot:subnav>
    <div class="mx-auto w-full max-w-2xl p-6">
        <form method="post" action="{{ route('people.update', $person) }}" class="card">
            @csrf @method('put')
            <div class="p-5">@include('people._form', ['person' => $person])</div>
            <div class="flex justify-end gap-2 rounded-b-lg border-t border-slate-200 bg-slate-50 px-5 py-3">
                <a href="{{ route('people.show', $person) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
