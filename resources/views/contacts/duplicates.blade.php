<x-app-layout title="Merge duplicates" :breadcrumbs="['Contacts' => route('people.index')]" info="People sharing an email address or name">
    <x-slot:subnav>@include('contacts._subnav')</x-slot:subnav>

    <div class="mx-auto w-full max-w-4xl p-6">
        <p class="mb-6 text-sm text-slate-600">We look for people who share an email address or name. Choose which record to keep — deals, activities, emails and notes from the others are moved onto it.</p>

        @forelse ($groups as $group)
            <form method="post" action="{{ route('contacts.duplicates.merge') }}" class="card mb-5" onsubmit="return confirm('Merge these contacts? This cannot be undone.')">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <h3 class="font-semibold">{{ $group->count() }} possible duplicates</h3>
                    <button type="submit" class="btn-primary btn-sm"><x-icon name="merge" class="size-3.5" /> Merge</button>
                </div>
                <table class="w-full">
                    <thead><tr>
                        <th class="table-head w-20">Keep</th><th class="table-head">Name</th><th class="table-head">Email</th><th class="table-head">Organization</th><th class="table-head">Deals</th><th class="table-head">Activities</th>
                    </tr></thead>
                    <tbody>
                        @foreach ($group as $person)
                            <tr>
                                <td class="table-cell">
                                    <input type="hidden" name="ids[]" value="{{ $person->id }}">
                                    <input type="radio" name="primary_id" value="{{ $person->id }}" @checked($loop->first) class="text-brand-600" aria-label="Keep {{ $person->name }}">
                                </td>
                                <td class="table-cell"><a href="{{ route('people.show', $person) }}" class="font-medium hover:text-link">{{ $person->name }}</a></td>
                                <td class="table-cell">{{ $person->email }}</td>
                                <td class="table-cell">{{ $person->organization?->name }}</td>
                                <td class="table-cell">{{ $person->deals_count }}</td>
                                <td class="table-cell">{{ $person->activities_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
        @empty
            <x-empty-state icon="check" title="No duplicates found" description="Your contact list looks clean. We'll surface people who share an email or name here." />
        @endforelse
    </div>
</x-app-layout>
