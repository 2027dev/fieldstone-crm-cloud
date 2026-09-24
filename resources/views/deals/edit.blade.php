<x-app-layout title="Edit deal" :breadcrumbs="['Deals' => route('deals.index'), $deal->title => route('deals.show', $deal)]">
    <div class="mx-auto w-full max-w-2xl p-6">
        <form method="post" action="{{ route('deals.update', $deal) }}" class="card">
            @csrf @method('put')
            <div class="p-5">@include('deals._form', ['deal' => $deal])</div>
            <div class="flex justify-end gap-2 rounded-b-lg border-t border-slate-200 bg-slate-50 px-5 py-3">
                <a href="{{ route('deals.show', $deal) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
