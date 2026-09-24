@php use App\Support\Money; @endphp
<x-app-layout title="Leads Inbox" :breadcrumbs="['Leads' => route('leads.index')]" info="Qualify incoming opportunities before they become deals">
    <x-slot:subnav>@include('leads._subnav')</x-slot:subnav>

    <div class="flex flex-1 flex-col" x-data="bulkSelect()">
        <div class="flex flex-wrap items-center gap-2 px-4 py-3">
            <div class="inline-flex rounded-md shadow-sm">
                <button type="button" x-on:click="$dispatch('open-modal', 'lead')" class="btn-primary rounded-r-none"><x-icon name="plus" /> Lead</button>
                <x-dropdown align="left">
                    <x-slot:trigger>
                        <button type="button" class="btn-primary rounded-l-none border-l-go-700 px-2" aria-label="More add options"><x-icon name="chevron-down" /></button>
                    </x-slot:trigger>
                    <x-dropdown-link href="#" icon="upload" x-on:click.prevent="$dispatch('open-modal', 'import-leads')">Import leads</x-dropdown-link>
                    <x-dropdown-link :href="route('web-forms.index')" icon="forms">Create a web form</x-dropdown-link>
                </x-dropdown>
            </div>

            <form x-show="selected.length" x-cloak method="post" action="{{ route('leads.bulk') }}" class="flex items-center gap-2">
                @csrf @method('patch')
                <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                <span class="text-sm text-slate-600"><span x-text="selected.length"></span> selected</span>
                @if ($tab === 'inbox')
                    <button type="submit" name="action" value="archive" class="btn-secondary"><x-icon name="archive" /> Archive</button>
                @else
                    <button type="submit" name="action" value="unarchive" class="btn-secondary"><x-icon name="inbox" /> Move to inbox</button>
                @endif
                <button type="submit" name="action" value="delete" class="btn-secondary text-red-600" onclick="return confirm('Delete the selected leads?')"><x-icon name="trash" /> Delete</button>
            </form>

            <div class="ml-auto flex items-center gap-2">
                <div class="inline-flex rounded-md border border-slate-300 text-sm shadow-sm">
                    <a href="{{ route('leads.index') }}" @class(['rounded-l-md px-3 py-1.5 font-medium', 'bg-brand-50 text-brand-700' => $tab === 'inbox', 'text-slate-600 hover:bg-slate-50' => $tab !== 'inbox'])>Inbox ({{ $inboxCount }})</a>
                    <a href="{{ route('leads.index', ['tab' => 'archived']) }}" @class(['rounded-r-md border-l border-slate-300 px-3 py-1.5 font-medium', 'bg-brand-50 text-brand-700' => $tab === 'archived', 'text-slate-600 hover:bg-slate-50' => $tab !== 'archived'])>Archived ({{ $archivedCount }})</a>
                </div>
                <form method="get" class="relative">
                    @if ($tab === 'archived')<input type="hidden" name="tab" value="archived">@endif
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" name="q" value="{{ $search }}" placeholder="Search leads" class="input w-48 py-1.5 pl-8">
                </form>
            </div>
        </div>

        @if ($leads->isEmpty() && $search === '' && $tab === 'inbox')
            <x-empty-state icon="target" title="Add your first lead" description="Organize and qualify incoming opportunities here – then convert the right ones into deals.">
                <button type="button" x-on:click="$dispatch('open-modal', 'lead')" class="btn-secondary"><x-icon name="plus" /> Lead</button>
                <button type="button" x-on:click="$dispatch('open-modal', 'import-leads')" class="btn-secondary"><x-icon name="download" /> Import leads</button>
            </x-empty-state>
            <div class="mt-auto border-t border-slate-100 bg-gradient-to-b from-white to-brand-50/60 py-10">
                <div class="mx-auto flex max-w-sm flex-col items-center rounded-lg bg-navy-800 p-5 text-center text-white shadow-md">
                    <span class="rounded bg-yellow-300 px-2 py-0.5 text-xl font-bold text-navy-900">Leads inbox</span>
                    <p class="mt-3 text-sm text-white/80">Capture leads from web forms, imports or by hand, label them hot, warm or cold, and convert the best ones into deals with one click.</p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[880px] border-t border-slate-200">
                    <thead><tr>
                        <th class="table-head w-10"><input type="checkbox" class="rounded border-slate-300" x-on:change="toggleAll($event, @js($leads->pluck('id')))" aria-label="Select all"></th>
                        <th class="table-head">Title</th>
                        <th class="table-head">Label</th>
                        <th class="table-head">Source</th>
                        <th class="table-head">Contact person</th>
                        <th class="table-head">Organization</th>
                        <th class="table-head">Value</th>
                        <th class="table-head">Created</th>
                        <th class="table-head w-40"></th>
                    </tr></thead>
                    <tbody>
                        @forelse ($leads as $lead)
                            <tr class="hover:bg-slate-50">
                                <td class="table-cell"><input type="checkbox" value="{{ $lead->id }}" x-model="selected" class="rounded border-slate-300" aria-label="Select lead"></td>
                                <td class="table-cell"><a href="{{ route('leads.show', $lead) }}" class="font-medium hover:text-link hover:underline">{{ $lead->title }}</a></td>
                                <td class="table-cell">@if ($lead->label)<span class="rounded px-1.5 py-0.5 text-xs font-semibold {{ $lead->label->badgeClasses() }}">{{ $lead->label->label() }}</span>@endif</td>
                                <td class="table-cell text-slate-600">{{ $lead->source->label() }}</td>
                                <td class="table-cell">@if ($lead->person)<a href="{{ route('people.show', $lead->person) }}" class="hover:text-link">{{ $lead->person->name }}</a>@endif</td>
                                <td class="table-cell">{{ $lead->organization?->name }}</td>
                                <td class="table-cell">{{ $lead->value !== null ? Money::format($lead->value, $lead->currency) : '' }}</td>
                                <td class="table-cell text-slate-600">{{ $lead->created_at->diffForHumans() }}</td>
                                <td class="table-cell text-right">
                                    <form method="post" action="{{ route('leads.convert', $lead) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="btn-secondary btn-sm">Convert to deal</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9"><x-empty-state icon="archive" title="{{ $search ? 'No leads match your search' : 'No archived leads' }}" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3">{{ $leads->links() }}</div>
        @endif
    </div>

    <x-modal name="lead" title="Add lead" width="max-w-2xl">
        <form method="post" action="{{ route('leads.store') }}">
            @csrf
            <input type="hidden" name="_modal" value="lead">
            <div class="px-5 py-4">@include('leads._form', ['lead' => null])</div>
            <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-200 bg-slate-50 px-5 py-3">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-modal>
    @include('partials.import-modal', ['type' => 'leads'])
</x-app-layout>
