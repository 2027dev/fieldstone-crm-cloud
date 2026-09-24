<x-app-layout :title="$organization->name" :breadcrumbs="['Contacts' => route('people.index'), 'Organizations' => route('organizations.index')]">
    <x-slot:subnav>@include('contacts._subnav')</x-slot:subnav>

    <div class="flex flex-wrap items-center gap-4 border-b border-slate-200 px-6 py-5">
        <span class="flex size-14 items-center justify-center rounded-lg bg-slate-100 text-slate-600"><x-icon name="building" class="size-7" /></span>
        <div class="min-w-0 flex-1">
            <h2 class="truncate text-2xl font-semibold">{{ $organization->name }}</h2>
            <p class="text-sm text-slate-600">{{ $organization->address }} @if ($organization->website) · <a href="{{ $organization->website }}" target="_blank" rel="noopener" class="text-link hover:underline">{{ parse_url($organization->website, PHP_URL_HOST) ?? $organization->website }}</a>@endif</p>
        </div>
        <a href="{{ route('organizations.edit', $organization) }}" class="btn-secondary"><x-icon name="pencil" /> Edit</a>
        <form method="post" action="{{ route('organizations.destroy', $organization) }}" onsubmit="return confirm('Delete this organization?')">
            @csrf @method('delete')
            <button type="submit" class="btn-secondary text-red-600" aria-label="Delete"><x-icon name="trash" /></button>
        </form>
    </div>

    <div class="grid gap-6 p-6 xl:grid-cols-3">
        <div class="space-y-6">
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-3"><h3 class="font-semibold">People ({{ $organization->people->count() }})</h3></div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($organization->people as $person)
                        <li class="flex items-center gap-3 px-4 py-2.5">
                            <x-avatar :initials="$person->initials()" />
                            <div class="min-w-0">
                                <a href="{{ route('people.show', $person) }}" class="block truncate text-sm font-medium hover:text-link">{{ $person->name }}</a>
                                <p class="truncate text-xs text-slate-500">{{ $person->job_title ?: $person->email }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-slate-500">No people linked.</li>
                    @endforelse
                </ul>
            </div>
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-3"><h3 class="font-semibold">Deals</h3></div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($organization->deals as $deal)
                        <li class="flex items-center justify-between gap-3 px-4 py-2.5">
                            <a href="{{ route('deals.show', $deal) }}" class="min-w-0 truncate text-sm font-medium hover:text-link">{{ $deal->title }}</a>
                            <span class="flex items-center gap-2 text-sm">{{ \App\Support\Money::format($deal->value, $deal->currency) }} @include('deals._status-badge', ['deal' => $deal])</span>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-slate-500">No deals yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="space-y-6 xl:col-span-2">
            @include('partials.activity-list', ['activities' => $organization->activities])
            @include('partials.notes', ['notable' => $organization, 'notableType' => 'organization'])
        </div>
    </div>

    @include('activities._create-modal', ['defaults' => ['organization_id' => $organization->id]])
</x-app-layout>
