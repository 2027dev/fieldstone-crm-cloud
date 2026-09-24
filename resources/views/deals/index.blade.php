@php use App\Support\Money; @endphp
<x-app-layout title="Deals" info="Your sales pipeline">
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-3">
        <div class="inline-flex rounded-md border border-slate-300 shadow-sm">
            <a href="{{ route('deals.index') }}" @class(['rounded-l-md px-3 py-1.5', 'bg-brand-50 text-brand-700' => $view === 'pipeline', 'text-slate-600 hover:bg-slate-50' => $view !== 'pipeline']) title="Pipeline"><x-icon name="kanban" class="size-5" /></a>
            <a href="{{ route('deals.index', ['view' => 'list', 'status' => $status->value]) }}" @class(['rounded-r-md border-l border-slate-300 px-3 py-1.5', 'bg-brand-50 text-brand-700' => $view === 'list', 'text-slate-600 hover:bg-slate-50' => $view !== 'list']) title="List"><x-icon name="list" class="size-5" /></a>
        </div>
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'deal')" class="btn-primary"><x-icon name="plus" /> Deal</button>
        <div class="ml-auto flex items-center gap-3">
            <span class="text-sm text-slate-600"><span class="font-semibold text-slate-900">{{ Money::format($deals->sum('value')) }}</span> · {{ $deals->count() }} {{ Str::plural('deal', $deals->count()) }}</span>
            <div class="inline-flex rounded-md border border-slate-300 text-sm shadow-sm">
                @foreach (\App\Enums\DealStatus::cases() as $option)
                    <a href="{{ route('deals.index', ['status' => $option->value, 'view' => $option === \App\Enums\DealStatus::Open ? null : 'list']) }}" @class(['border-l border-slate-300 px-3 py-1.5 font-medium first:rounded-l-md first:border-l-0 last:rounded-r-md', 'bg-brand-50 text-brand-700' => $status === $option, 'text-slate-600 hover:bg-slate-50' => $status !== $option])>{{ $option->label() }}</a>
                @endforeach
            </div>
        </div>
    </div>

    @if ($view === 'pipeline')
        <div class="flex min-h-0 flex-1 gap-3 overflow-x-auto bg-slate-50 p-3" x-data="pipeline()">
            @foreach ($columns as $column)
                <section
                    class="flex min-w-48 flex-1 flex-col rounded-lg"
                    data-stage-column
                    x-on:dragover.prevent="over = '{{ $column['stage']->value }}'"
                    x-on:dragleave.self="over = null"
                    x-on:drop.prevent="drop($event, '{{ $column['stage']->value }}')"
                    x-bind:class="over === '{{ $column['stage']->value }}' && 'bg-brand-50 ring-2 ring-brand-200'"
                >
                    <header class="rounded-t-lg border-b-2 border-go-600 bg-white px-3 py-2 shadow-xs">
                        <h2 class="truncate text-sm font-semibold text-slate-900">{{ $column['stage']->label() }}</h2>
                        <p class="text-xs text-slate-500"><span data-stage-total>{{ Money::format($column['deals']->sum('value')) }}</span> · <span data-stage-count>{{ $column['deals']->count() }} {{ Str::plural('deal', $column['deals']->count()) }}</span></p>
                    </header>
                    <div class="flex flex-1 flex-col gap-2 p-1.5 pt-2" data-column>
                        @foreach ($column['deals'] as $deal)
                            <article
                                draggable="true"
                                data-deal="{{ $deal->id }}"
                                data-value="{{ (float) $deal->value }}"
                                data-move-url="{{ route('deals.move', $deal) }}"
                                x-on:dragstart="start($event, {{ $deal->id }})"
                                x-on:dragend="end()"
                                x-bind:class="dragging === {{ $deal->id }} && 'opacity-40'"
                                class="group cursor-grab rounded-md border border-slate-200 bg-white p-3 shadow-xs hover:border-slate-300 hover:shadow active:cursor-grabbing"
                            >
                                <a href="{{ route('deals.show', $deal) }}" class="block text-sm font-medium text-slate-900 hover:text-link" draggable="false">{{ $deal->title }}</a>
                                <p class="mt-0.5 truncate text-xs text-slate-500">{{ $deal->organization?->name ?? $deal->person?->name ?? 'No contact' }}</p>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5 text-xs text-slate-600">
                                        @if ($deal->person)<x-avatar :initials="$deal->person->initials()" size="size-5" class="text-[9px]" />@endif
                                        {{ Money::format($deal->value, $deal->currency) }}
                                    </span>
                                    @if ($deal->pending_activities_count)
                                        <span class="flex items-center gap-0.5 text-xs text-slate-500" title="Open activities"><x-icon name="calendar" class="size-3.5" /> {{ $deal->pending_activities_count }}</span>
                                    @else
                                        <span class="text-amber-500" title="No activity scheduled"><x-icon name="info" class="size-4" /></span>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                        <p data-empty @class(['rounded-md border-2 border-dashed border-slate-200 px-3 py-6 text-center text-xs text-slate-400', 'hidden' => $column['deals']->isNotEmpty()])>Drop deals here</p>
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead><tr>
                    <th class="table-head">Title</th><th class="table-head">Value</th><th class="table-head">Organization</th><th class="table-head">Contact person</th><th class="table-head">Stage</th><th class="table-head">Status</th><th class="table-head">{{ $status === \App\Enums\DealStatus::Open ? 'Expected close' : 'Closed' }}</th>
                </tr></thead>
                <tbody>
                    @forelse ($deals as $deal)
                        <tr class="hover:bg-slate-50">
                            <td class="table-cell"><a href="{{ route('deals.show', $deal) }}" class="font-medium hover:text-link hover:underline">{{ $deal->title }}</a></td>
                            <td class="table-cell">{{ Money::format($deal->value, $deal->currency) }}</td>
                            <td class="table-cell">{{ $deal->organization?->name }}</td>
                            <td class="table-cell">{{ $deal->person?->name }}</td>
                            <td class="table-cell">{{ $deal->stage->label() }}</td>
                            <td class="table-cell">@include('deals._status-badge')</td>
                            <td class="table-cell">{{ ($status === \App\Enums\DealStatus::Open ? $deal->expected_close_date : $deal->closed_at)?->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty-state icon="dollar" title="No {{ strtolower($status->label()) }} deals" description="Deals you {{ $status === \App\Enums\DealStatus::Open ? 'add' : 'mark as '.strtolower($status->label()) }} will appear here." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @include('deals._create-modal')
</x-app-layout>
