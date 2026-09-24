<nav class="space-y-0.5">
    <a href="{{ route('people.index') }}" @class(['subnav-link', 'subnav-link-active' => request()->routeIs('people.*')])><x-icon name="user" class="size-5" /> People</a>
    <a href="{{ route('organizations.index') }}" @class(['subnav-link', 'subnav-link-active' => request()->routeIs('organizations.*')])><x-icon name="building" class="size-5" /> Organizations</a>
    <a href="{{ route('contacts.timeline') }}" @class(['subnav-link', 'subnav-link-active' => request()->routeIs('contacts.timeline')])><x-icon name="timeline" class="size-5" /> Contacts timeline</a>
    <a href="{{ route('contacts.duplicates') }}" @class(['subnav-link', 'subnav-link-active' => request()->routeIs('contacts.duplicates')])><x-icon name="merge" class="size-5" /> Merge duplicates</a>
</nav>
<div x-data="{ show: true }" x-show="show" class="mt-auto rounded-lg border border-slate-200 bg-white p-3">
    <div class="flex items-start justify-between gap-2">
        <p class="text-sm font-semibold text-slate-800">Import your contacts</p>
        <button type="button" x-on:click="show = false" class="text-slate-500 hover:text-slate-800" aria-label="Dismiss"><x-icon name="x" class="size-4" /></button>
    </div>
    <p class="mt-1 text-xs text-slate-600">Bring people in from a spreadsheet with a CSV file.</p>
    <button type="button" x-on:click="$dispatch('open-modal', 'import-people')" class="mt-2 text-sm font-semibold text-link hover:underline">Import CSV</button>
</div>
