<x-app-layout title="People" :breadcrumbs="['Contacts' => route('people.index')]" info="Everyone you do business with">
    <x-slot:subnav>@include('contacts._subnav')</x-slot:subnav>

    <div class="flex flex-1 flex-col" x-data="bulkSelect()">
        <div class="flex flex-wrap items-center gap-2 px-4 py-3">
            <div class="inline-flex rounded-md shadow-sm">
                <button type="button" x-on:click="$dispatch('open-modal', 'person')" class="btn-primary rounded-r-none"><x-icon name="plus" /> Person</button>
                <x-dropdown align="left">
                    <x-slot:trigger>
                        <button type="button" class="btn-primary rounded-l-none border-l-go-700 px-2" aria-label="More add options"><x-icon name="chevron-down" /></button>
                    </x-slot:trigger>
                    <x-dropdown-link href="#" icon="building" x-on:click.prevent="$dispatch('open-modal', 'organization')">Add organization</x-dropdown-link>
                    <x-dropdown-link href="#" icon="upload" x-on:click.prevent="$dispatch('open-modal', 'import-people')">Import from CSV</x-dropdown-link>
                </x-dropdown>
            </div>

            <form x-show="selected.length" x-cloak method="post" action="{{ route('people.bulk-destroy') }}" onsubmit="return confirm('Delete the selected people?')" class="flex items-center gap-2">
                @csrf @method('delete')
                <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                <span class="text-sm text-slate-600"><span x-text="selected.length"></span> selected</span>
                <button type="submit" class="btn-secondary text-red-600"><x-icon name="trash" /> Delete</button>
            </form>

            <div class="ml-auto flex items-center gap-2">
                <form method="get" class="relative">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" name="q" value="{{ $search }}" placeholder="Search people" class="input w-48 py-1.5 pl-8">
                </form>
                <a href="{{ route('people.index') }}" class="flex items-center gap-1.5 text-sm font-semibold text-slate-800" title="Refresh">
                    <x-icon name="refresh" class="size-4" /> {{ $people->total() }} {{ $people->total() === 1 ? 'person' : 'people' }}
                </a>
                <x-dropdown align="right" width="w-56">
                    <x-slot:trigger>
                        <button type="button" @class(['btn-secondary', 'border-brand-500 text-brand-700' => $filter !== 'all'])><x-icon name="filter" /> {{ $filter === 'all' ? 'Filter' : \App\Http\Controllers\PersonController::FILTERS[$filter] }} <x-icon name="chevron-down" class="size-3" /></button>
                    </x-slot:trigger>
                    @foreach (\App\Http\Controllers\PersonController::FILTERS as $key => $label)
                        <x-dropdown-link :href="route('people.index', ['filter' => $key, 'q' => $search ?: null])" :class="$filter === $key ? 'font-semibold text-brand-700' : ''">{{ $label }}</x-dropdown-link>
                    @endforeach
                </x-dropdown>
            </div>
        </div>

        @if ($filter === 'all')
            <div x-data="{ show: true }" x-show="show" class="mx-4 mb-3 rounded-lg border border-brand-200 bg-brand-50/60 px-4 py-3">
                <div class="flex items-start gap-3">
                    <x-icon name="graduation" class="mt-0.5 size-5 text-brand-600" />
                    <div class="flex-1">
                        <p class="font-semibold text-slate-900">Find the right contacts faster</p>
                        <p class="mt-0.5 text-sm text-slate-700">Filters make it easy to manage a growing contact list. Try the suggested filter below or adjust it as needed to find what you need faster.</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-sm">
                            <span class="chip font-semibold"><span class="text-[10px]">123</span> Total activities &gt; 0</span>
                            <a href="{{ route('people.index', ['filter' => 'open_deals']) }}" class="inline-flex items-center gap-1 font-medium text-slate-600 hover:text-slate-900"><x-icon name="filter" class="size-3.5" /> Try "With open deals"</a>
                        </div>
                    </div>
                    <a href="{{ route('people.index', ['filter' => 'has_activities']) }}" class="btn-primary self-center">Apply filter</a>
                    <button type="button" x-on:click="show = false" class="text-slate-600 hover:text-slate-900" aria-label="Dismiss"><x-icon name="x" class="size-5" /></button>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-t border-slate-200">
                <thead>
                    <tr>
                        <th class="table-head w-10"><input type="checkbox" class="rounded border-slate-300" x-on:change="toggleAll($event, @js($people->pluck('id')))" aria-label="Select all"></th>
                        <th class="table-head">Name</th>
                        <th class="table-head">Organization</th>
                        <th class="table-head">Email</th>
                        <th class="table-head">Phone</th>
                        <th class="table-head">Closed deals</th>
                        <th class="table-head">Activities</th>
                        <th class="table-head w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($people as $person)
                        <tr class="group hover:bg-slate-50">
                            <td class="table-cell"><input type="checkbox" value="{{ $person->id }}" x-model="selected" class="rounded border-slate-300" aria-label="Select {{ $person->name }}"></td>
                            <td class="table-cell"><a href="{{ route('people.show', $person) }}" class="font-medium hover:text-link hover:underline">{{ $person->name }}</a></td>
                            <td class="table-cell">
                                @if ($person->organization)
                                    <a href="{{ route('organizations.show', $person->organization) }}" class="hover:text-link hover:underline">{{ $person->organization->name }}</a>
                                @endif
                            </td>
                            <td class="table-cell">
                                @if ($person->email)
                                    <span class="chip"><a href="mailto:{{ $person->email }}" class="text-link">{{ $person->email }}</a> <span class="text-slate-500">({{ $person->email_label }})</span></span>
                                @endif
                            </td>
                            <td class="table-cell">@if ($person->phone)<a href="tel:{{ $person->phone }}" class="text-link">{{ $person->phone }}</a>@endif</td>
                            <td class="table-cell">{{ $person->closed_deals_count ?: '' }}</td>
                            <td class="table-cell">{{ $person->activities_count ?: '' }}</td>
                            <td class="table-cell">
                                <x-dropdown align="right" width="w-40">
                                    <x-slot:trigger><button type="button" class="rounded p-1 text-slate-500 hover:bg-slate-200" aria-label="Actions"><x-icon name="dots" /></button></x-slot:trigger>
                                    <x-dropdown-link :href="route('people.show', $person)" icon="user">Open</x-dropdown-link>
                                    <x-dropdown-link :href="route('people.edit', $person)" icon="pencil">Edit</x-dropdown-link>
                                    <form method="post" action="{{ route('people.destroy', $person) }}" onsubmit="return confirm('Delete this person?')">
                                        @csrf @method('delete')
                                        <x-dropdown-link icon="trash" class="text-red-600">Delete</x-dropdown-link>
                                    </form>
                                </x-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state icon="contacts" title="{{ $search || $filter !== 'all' ? 'No people match' : 'No people yet' }}" description="{{ $search || $filter !== 'all' ? 'Try a different search or filter.' : 'Add the people you work with so their deals, emails and activities can be linked.' }}">
                                    <button type="button" x-on:click="$dispatch('open-modal', 'person')" class="btn-primary"><x-icon name="plus" /> Person</button>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $people->links() }}</div>

        @if ($hasSampleData)
            <x-sample-banner title="Manage your contacts" description="These [Sample] contacts show how Fieldstone links people, organizations, deals and activities. Remove them whenever you're ready." icon="contacts">
                <button type="button" x-on:click="$dispatch('open-modal', 'import-people')" class="btn-secondary"><x-icon name="download" /> Import contacts</button>
                <form method="post" action="{{ route('sample-data.destroy') }}" onsubmit="return confirm('Remove all sample data from your workspace?')">
                    @csrf @method('delete')
                    <button type="submit" class="btn-secondary">Remove sample data</button>
                </form>
            </x-sample-banner>
        @endif
    </div>

    @include('people._create-modal')
    @include('organizations._create-modal')
    @include('partials.import-modal', ['type' => 'people'])
</x-app-layout>
