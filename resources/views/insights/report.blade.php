<x-app-layout :title="$report->name" :breadcrumbs="['Insights' => route('insights'), 'My reports' => route('insights')]">
    <x-slot:subnav>@include('insights._subnav')</x-slot:subnav>
    <div class="flex-1 bg-slate-50 p-5">
        <div class="card mx-auto max-w-3xl p-6">
            <div class="mb-6 flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold">{{ $report->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $report->type->description() }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-2xl font-semibold">{{ $chart['total'] }}</span>
                    <form method="post" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Delete this report?')">
                        @csrf @method('delete')
                        <button type="submit" class="btn-secondary text-red-600" aria-label="Delete report"><x-icon name="trash" /></button>
                    </form>
                </div>
            </div>
            @include('insights._chart', ['type' => $report->type, 'chart' => $chart])
        </div>
    </div>
</x-app-layout>
