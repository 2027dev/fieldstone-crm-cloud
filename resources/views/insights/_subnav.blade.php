<div x-data="{ q: '' }" class="flex flex-1 flex-col">
    <div class="flex gap-2">
        <x-dropdown align="left" width="w-56">
            <x-slot:trigger>
                <button type="button" class="btn-secondary w-full min-w-44"><x-icon name="plus" /> Create <x-icon name="chevron-down" class="size-3" /></button>
            </x-slot:trigger>
            <x-dropdown-link href="#" icon="layout" x-on:click.prevent="$dispatch('open-modal', 'dashboard')">Dashboard</x-dropdown-link>
            <x-dropdown-link href="#" icon="chart" x-on:click.prevent="$dispatch('open-modal', 'report')">Report</x-dropdown-link>
        </x-dropdown>
        <a href="{{ route('setup') }}" class="btn-secondary px-2.5" title="Learn"><x-icon name="graduation" /></a>
    </div>
    <div class="relative mt-2">
        <x-icon name="search" class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-slate-500" />
        <input type="search" x-model="q" placeholder="Search from Insights" class="input py-1.5 pl-8">
    </div>

    <div class="mt-5 flex items-center justify-between px-1">
        <p class="flex items-center gap-2 text-sm font-semibold text-slate-800 uppercase"><x-icon name="layout" /> Dashboards</p>
        <button type="button" x-on:click="$dispatch('open-modal', 'dashboard')" class="rounded p-1 text-slate-500 hover:bg-slate-200" aria-label="New dashboard"><x-icon name="plus" /></button>
    </div>
    <p class="mt-2 flex items-center gap-1 px-1 text-sm text-slate-700"><x-icon name="chevron-down" class="size-3.5" /> My dashboards</p>
    <nav class="mt-1 space-y-0.5 pl-3">
        @forelse ($dashboards as $item)
            <a href="{{ route('dashboards.show', $item) }}" x-show="! q || @js(Str::lower($item->name)).includes(q.toLowerCase())" @class(['subnav-link py-1.5 text-sm', 'subnav-link-active' => isset($dashboard) && $dashboard->is($item)])>{{ $item->name }}</a>
        @empty
            <p class="px-3 py-1.5 text-sm text-slate-500">No dashboards</p>
        @endforelse
    </nav>

    <p class="mt-5 flex items-center gap-1 px-1 text-sm text-slate-700"><x-icon name="chevron-down" class="size-3.5" /> My reports</p>
    <nav class="mt-1 space-y-0.5 pl-3">
        @forelse ($reports as $item)
            <a href="{{ route('reports.show', $item) }}" x-show="! q || @js(Str::lower($item->name)).includes(q.toLowerCase())" @class(['subnav-link py-1.5 text-sm', 'subnav-link-active' => isset($report) && $report->is($item)])>{{ $item->name }}</a>
        @empty
            <p class="px-3 py-1.5 text-sm text-slate-500">No reports</p>
        @endforelse
    </nav>
</div>

<x-modal name="dashboard" title="Create dashboard">
    <form method="post" action="{{ route('dashboards.store') }}">
        @csrf
        <input type="hidden" name="_modal" value="dashboard">
        <div class="space-y-4 px-5 py-4">
            <x-field label="Dashboard name" name="name" required placeholder="Sales overview" />
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="hidden" name="starter" value="0">
                <input type="checkbox" name="starter" value="1" checked class="rounded border-slate-300"> Start with recommended reports
            </label>
        </div>
        <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
            <button type="submit" class="btn-primary">Create dashboard</button>
        </div>
    </form>
</x-modal>

<x-modal name="report" title="Create report" width="max-w-2xl">
    <form method="post" action="{{ route('reports.store') }}" x-data="{ type: @js(old('type', 'deals_by_stage')) }">
        @csrf
        <input type="hidden" name="_modal" value="report">
        <div class="space-y-4 px-5 py-4">
            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($reportTypes as $reportType)
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="{{ $reportType->value }}" x-model="type" class="peer sr-only">
                        <span class="block h-full rounded-lg border border-slate-200 p-3 peer-checked:border-brand-500 peer-checked:bg-brand-50 hover:border-slate-300">
                            <span class="flex items-center gap-2 text-sm font-semibold text-slate-900"><x-icon :name="$reportType->chart() === 'pie' ? 'target' : 'chart'" class="size-4 text-brand-600" /> {{ $reportType->label() }}</span>
                            <span class="mt-1 block text-xs text-slate-600">{{ $reportType->description() }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            <x-field label="Report name (optional)" name="name" placeholder="Uses the report type if left empty" />
            <x-select label="Add to dashboard" name="dashboard_id" placeholder="Don't add — save to My reports" :options="$dashboards->pluck('name', 'id')" :value="isset($dashboard) ? $dashboard->id : null" />
        </div>
        <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
            <button type="submit" class="btn-primary">Create report</button>
        </div>
    </form>
</x-modal>
