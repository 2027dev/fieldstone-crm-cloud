<x-app-layout title="Edit activity" :breadcrumbs="['Activities' => route('activities.index')]">
    <div class="mx-auto w-full max-w-2xl p-6">
        <form method="post" action="{{ route('activities.update', $activity) }}" class="card">
            @csrf @method('put')
            <div class="p-5">@include('activities._form', ['activity' => $activity])</div>
            <div class="flex items-center justify-between gap-2 rounded-b-lg border-t border-slate-200 bg-slate-50 px-5 py-3">
                <button type="submit" form="delete-activity" class="btn-ghost text-red-600"><x-icon name="trash" /> Delete</button>
                <div class="flex gap-2">
                    <a href="{{ url()->previous() === url()->current() ? route('activities.index') : url()->previous() }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Save changes</button>
                </div>
            </div>
        </form>
        <form id="delete-activity" method="post" action="{{ route('activities.destroy', $activity) }}" onsubmit="return confirm('Delete this activity?')">
            @csrf @method('delete')
        </form>
    </div>
</x-app-layout>
