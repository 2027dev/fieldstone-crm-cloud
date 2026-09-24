<x-app-layout :title="$dashboard->name" :breadcrumbs="['Insights' => route('insights')]">
    <x-slot:subnav>@include('insights._subnav')</x-slot:subnav>

    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-5 py-3" x-data="{ renaming: false }">
        <form x-show="renaming" x-cloak method="post" action="{{ route('dashboards.update', $dashboard) }}" class="flex items-center gap-2">
            @csrf @method('put')
            <input type="text" name="name" value="{{ $dashboard->name }}" class="input py-1.5" required aria-label="Dashboard name">
            <button type="submit" class="btn-primary">Save</button>
            <button type="button" class="btn-ghost" x-on:click="renaming = false">Cancel</button>
        </form>
        <p x-show="! renaming" class="text-sm text-slate-600">{{ $dashboard->reports->count() }} {{ Str::plural('report', $dashboard->reports->count()) }} · Live data from your workspace</p>
        <div class="ml-auto flex gap-2">
            <button type="button" x-on:click="$dispatch('open-modal', 'report')" class="btn-primary"><x-icon name="plus" /> Add report</button>
            <x-dropdown align="right">
                <x-slot:trigger><button type="button" class="btn-secondary px-2" aria-label="Dashboard options"><x-icon name="dots" /></button></x-slot:trigger>
                <x-dropdown-link href="#" icon="pencil" x-on:click.prevent="renaming = true">Rename</x-dropdown-link>
                <form method="post" action="{{ route('dashboards.destroy', $dashboard) }}" onsubmit="return confirm('Delete this dashboard and its reports?')">
                    @csrf @method('delete')
                    <x-dropdown-link icon="trash" class="text-red-600">Delete dashboard</x-dropdown-link>
                </form>
            </x-dropdown>
        </div>
    </div>

    <div class="flex-1 bg-slate-50 p-5">
        @if ($dashboard->reports->isEmpty())
            <x-empty-state icon="chart" title="This dashboard is empty" description="Add reports to track deals, activities and leads at a glance.">
                <button type="button" x-data x-on:click="$dispatch('open-modal', 'report')" class="btn-primary"><x-icon name="plus" /> Add report</button>
            </x-empty-state>
        @else
            <div class="grid gap-5 xl:grid-cols-2">
                @foreach ($dashboard->reports as $item)
                    <div class="card flex flex-col p-5">
                        <div class="mb-4 flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-slate-900">{{ $item->name }}</h3>
                                <p class="text-xs text-slate-500">{{ $item->type->description() }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-semibold">{{ $charts[$item->id]['total'] }}</span>
                                <form method="post" action="{{ route('reports.destroy', $item) }}" onsubmit="return confirm('Remove this report?')">
                                    @csrf @method('delete')
                                    <button type="submit" class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-red-600" aria-label="Remove report"><x-icon name="x" /></button>
                                </form>
                            </div>
                        </div>
                        @include('insights._chart', ['type' => $item->type, 'chart' => $charts[$item->id]])
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
