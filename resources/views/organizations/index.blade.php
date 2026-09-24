<x-app-layout title="Organizations" :breadcrumbs="['Contacts' => route('people.index')]">
    <x-slot:subnav>@include('contacts._subnav')</x-slot:subnav>

    <div class="flex flex-wrap items-center gap-2 px-4 py-3">
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'organization')" class="btn-primary"><x-icon name="plus" /> Organization</button>
        <div class="ml-auto flex items-center gap-3">
            <form method="get" class="relative">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Search organizations" class="input w-56 py-1.5 pl-8">
            </form>
            <span class="text-sm font-semibold">{{ $organizations->total() }} {{ Str::plural('organization', $organizations->total()) }}</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] border-t border-slate-200">
            <thead>
                <tr>
                    <th class="table-head">Name</th>
                    <th class="table-head">Address</th>
                    <th class="table-head">People</th>
                    <th class="table-head">Open deals</th>
                    <th class="table-head w-10"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($organizations as $organization)
                    <tr class="hover:bg-slate-50">
                        <td class="table-cell"><a href="{{ route('organizations.show', $organization) }}" class="font-medium hover:text-link hover:underline">{{ $organization->name }}</a></td>
                        <td class="table-cell text-slate-600">{{ $organization->address }}</td>
                        <td class="table-cell">{{ $organization->people_count }}</td>
                        <td class="table-cell">{{ $organization->open_deals_count }}</td>
                        <td class="table-cell">
                            <x-dropdown align="right" width="w-40">
                                <x-slot:trigger><button type="button" class="rounded p-1 text-slate-500 hover:bg-slate-200" aria-label="Actions"><x-icon name="dots" /></button></x-slot:trigger>
                                <x-dropdown-link :href="route('organizations.edit', $organization)" icon="pencil">Edit</x-dropdown-link>
                                <form method="post" action="{{ route('organizations.destroy', $organization) }}" onsubmit="return confirm('Delete this organization?')">
                                    @csrf @method('delete')
                                    <x-dropdown-link icon="trash" class="text-red-600">Delete</x-dropdown-link>
                                </form>
                            </x-dropdown>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">
                        <x-empty-state icon="building" title="No organizations yet" description="Organizations group the people you work with at the same company.">
                            <button type="button" x-data x-on:click="$dispatch('open-modal', 'organization')" class="btn-primary"><x-icon name="plus" /> Organization</button>
                        </x-empty-state>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3">{{ $organizations->links() }}</div>

    @include('organizations._create-modal')
</x-app-layout>
