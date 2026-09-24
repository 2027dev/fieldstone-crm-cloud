<x-app-layout title="Edit lead" :breadcrumbs="['Leads' => route('leads.index'), $lead->title => route('leads.show', $lead)]">
    <x-slot:subnav>@include('leads._subnav')</x-slot:subnav>
    <div class="mx-auto w-full max-w-2xl p-6">
        <form method="post" action="{{ route('leads.update', $lead) }}" class="card">
            @csrf @method('put')
            <div class="p-5">@include('leads._form', ['lead' => $lead])</div>
            <div class="flex justify-end gap-2 rounded-b-lg border-t border-slate-200 bg-slate-50 px-5 py-3">
                <a href="{{ route('leads.show', $lead) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
