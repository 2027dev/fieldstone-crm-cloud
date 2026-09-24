<x-app-layout title="Edit organization" :breadcrumbs="['Contacts' => route('people.index'), $organization->name => route('organizations.show', $organization)]">
    <x-slot:subnav>@include('contacts._subnav')</x-slot:subnav>
    <div class="mx-auto w-full max-w-2xl p-6">
        <form method="post" action="{{ route('organizations.update', $organization) }}" class="card">
            @csrf @method('put')
            <div class="p-5">@include('organizations._form', ['organization' => $organization])</div>
            <div class="flex justify-end gap-2 rounded-b-lg border-t border-slate-200 bg-slate-50 px-5 py-3">
                <a href="{{ route('organizations.show', $organization) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
