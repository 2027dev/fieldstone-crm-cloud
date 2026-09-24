@php
    $query = fn (array $overrides = []) => array_filter(array_merge(['view' => $view === 'calendar' ? 'calendar' : null, 'type' => $type?->value, 'period' => $period !== 'todo' ? $period : null], $overrides), fn ($value) => $value !== null);
@endphp
<x-app-layout title="Activities" info="Calls, meetings, tasks and deadlines">
    <div class="flex flex-1 flex-col" x-data="bulkSelect()">
        <div x-data="{ show: true }" x-show="show" class="relative mx-4 mt-4 flex items-center gap-5 overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-r from-white via-white to-brand-50 px-5 py-4">
            <div class="relative hidden size-14 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-brand-600 sm:flex">
                <x-icon name="calendar" class="size-8" />
                <span class="absolute -right-1 -bottom-1 flex size-5 items-center justify-center rounded-full bg-link text-white ring-2 ring-white"><x-icon name="refresh" class="size-3" /></span>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-slate-900">Plan your week with the calendar view.</p>
                <p class="text-sm text-slate-700">See every call, meeting and deadline laid out by day so nothing slips through the cracks.</p>
            </div>
            <a href="{{ route('activities.index', ['view' => 'calendar']) }}" class="btn-secondary">Open calendar</a>
            <button type="button" x-on:click="show = false" class="text-slate-700 hover:text-slate-900" aria-label="Dismiss"><x-icon name="x" class="size-5" /></button>
        </div>

        <div class="flex flex-wrap items-center gap-2 px-4 pt-4 pb-2">
            <div class="inline-flex rounded-md border border-slate-300 shadow-sm">
                <a href="{{ route('activities.index', $query(['view' => null])) }}" @class(['rounded-l-md px-3 py-1.5', 'bg-brand-50 text-brand-700' => $view === 'list', 'text-slate-600 hover:bg-slate-50' => $view !== 'list']) title="List view"><x-icon name="list" class="size-5" /></a>
                <a href="{{ route('activities.index', $query(['view' => 'calendar'])) }}" @class(['rounded-r-md border-l border-slate-300 px-3 py-1.5', 'bg-brand-50 text-brand-700' => $view === 'calendar', 'text-slate-600 hover:bg-slate-50' => $view !== 'calendar']) title="Calendar view"><x-icon name="calendar" class="size-5" /></a>
            </div>
            <button type="button" x-on:click="$dispatch('open-modal', 'activity')" class="btn-primary"><x-icon name="plus" /> Activity</button>

            <form x-show="selected.length" x-cloak method="post" action="{{ route('activities.bulk') }}" class="flex items-center gap-2">
                @csrf @method('patch')
                <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                <span class="text-sm text-slate-600"><span x-text="selected.length"></span> selected</span>
                <button type="submit" name="action" value="done" class="btn-secondary"><x-icon name="check" /> Mark done</button>
                <button type="submit" name="action" value="delete" class="btn-secondary text-red-600" onclick="return confirm('Delete the selected activities?')"><x-icon name="trash" /> Delete</button>
            </form>

            @if ($view === 'list')
                <span class="ml-auto flex items-center gap-1.5 text-sm font-medium text-slate-800">{{ $activities->total() }} {{ Str::plural('activity', $activities->total()) }}</span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-1 border-b border-slate-200 px-4 pb-2 text-sm">
            <a href="{{ route('activities.index', $query(['type' => null])) }}" @class(['filter-pill', 'filter-pill-active' => ! $type])>All</a>
            <span class="mx-1 h-5 w-px bg-slate-200"></span>
            @foreach (\App\Enums\ActivityType::cases() as $activityType)
                <a href="{{ route('activities.index', $query(['type' => $activityType->value])) }}" @class(['filter-pill text-link', 'filter-pill-active' => $type === $activityType])>
                    <x-icon :name="$activityType->icon()" class="size-4" /> {{ $activityType->label() }}
                </a>
            @endforeach
            @if ($view === 'list')
                <div class="ml-auto flex flex-wrap items-center gap-0.5">
                    @foreach (\App\Http\Controllers\ActivityController::PERIODS as $key => $label)
                        <a href="{{ route('activities.index', $query(['period' => $key === 'todo' ? null : $key])) }}" @class(['filter-pill', 'filter-pill-active' => $period === $key])>{{ $label }}</a>
                    @endforeach
                </div>
            @endif
        </div>

        @if ($view === 'list')
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px]">
                    <thead>
                        <tr>
                            <th class="table-head w-10"><input type="checkbox" class="rounded border-slate-300" x-on:change="toggleAll($event, @js($activities->pluck('id')))" aria-label="Select all"></th>
                            <th class="table-head w-16">Done</th>
                            <th class="table-head">Subject</th>
                            <th class="table-head">Deal</th>
                            <th class="table-head">Priority</th>
                            <th class="table-head">Outcome</th>
                            <th class="table-head">Contact person</th>
                            <th class="table-head"><span class="inline-flex items-center gap-1"><x-icon name="user" class="size-3.5" /> Email</span></th>
                            <th class="table-head"><span class="inline-flex items-center gap-1"><x-icon name="user" class="size-3.5" /> Phone</span></th>
                            <th class="table-head">Due date</th>
                            <th class="table-head w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr class="hover:bg-slate-50" x-data="doneToggle(@js($activity->done), '{{ route('activities.toggle', $activity) }}')">
                                <td class="table-cell"><input type="checkbox" value="{{ $activity->id }}" x-model="selected" class="rounded border-slate-300" aria-label="Select activity"></td>
                                <td class="table-cell">
                                    <button type="button" x-on:click="toggle()" class="flex size-5 items-center justify-center rounded-full border" x-bind:class="done ? 'border-go-600 bg-go-600 text-white' : 'border-slate-400 hover:border-go-600'" aria-label="Toggle done">
                                        <x-icon name="check" class="size-3" x-show="done" />
                                    </button>
                                </td>
                                <td class="table-cell">
                                    <a href="{{ route('activities.edit', $activity) }}" class="inline-flex items-center gap-1.5 hover:text-link" x-bind:class="done && 'line-through text-slate-400'">
                                        <x-icon :name="$activity->type->icon()" class="size-4" /> {{ $activity->subject }}
                                    </a>
                                </td>
                                <td class="table-cell">@if ($activity->deal)<a href="{{ route('deals.show', $activity->deal) }}" class="hover:text-link">{{ $activity->deal->title }}</a>@endif</td>
                                <td class="table-cell">@if ($activity->priority)<span class="rounded px-1.5 py-0.5 text-xs font-medium {{ $activity->priority->badgeClasses() }}">{{ $activity->priority->label() }}</span>@endif</td>
                                <td class="table-cell text-slate-600">{{ $activity->outcome }}</td>
                                <td class="table-cell">@if ($activity->person)<a href="{{ route('people.show', $activity->person) }}" class="chip hover:border-slate-300">{{ $activity->person->name }}</a>@endif</td>
                                <td class="table-cell">@if ($activity->person?->email)<span class="chip">{{ $activity->person->email }} <span class="text-slate-500">({{ $activity->person->email_label }})</span></span>@endif</td>
                                <td class="table-cell">{{ $activity->person?->phone }}</td>
                                <td @class(['table-cell', 'font-medium text-red-600' => $activity->isOverdue(), 'text-go-600 font-medium' => ! $activity->done && $activity->due_date?->isToday()])>
                                    @if ($activity->due_date)
                                        {{ $activity->due_date->isToday() ? 'Today' : ($activity->due_date->isTomorrow() ? 'Tomorrow' : $activity->due_date->format('M j, Y')) }}
                                        <span class="text-slate-500">{{ $activity->formattedDueTime() }}</span>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    <x-dropdown align="right" width="w-40">
                                        <x-slot:trigger><button type="button" class="rounded p-1 text-slate-500 hover:bg-slate-200" aria-label="Actions"><x-icon name="dots" /></button></x-slot:trigger>
                                        <x-dropdown-link :href="route('activities.edit', $activity)" icon="pencil">Edit</x-dropdown-link>
                                        <form method="post" action="{{ route('activities.destroy', $activity) }}" onsubmit="return confirm('Delete this activity?')">
                                            @csrf @method('delete')
                                            <x-dropdown-link icon="trash" class="text-red-600">Delete</x-dropdown-link>
                                        </form>
                                    </x-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11">
                                <x-empty-state icon="calendar" title="No activities here" description="Schedule calls, meetings and tasks to keep your deals moving forward.">
                                    <button type="button" x-on:click="$dispatch('open-modal', 'activity')" class="btn-primary"><x-icon name="plus" /> Activity</button>
                                </x-empty-state>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3">{{ $activities->links() }}</div>
        @else
            <div class="flex items-center gap-3 px-4 py-3">
                <a href="{{ route('activities.index', $query(['month' => $month->subMonth()->format('Y-m')])) }}" class="btn-secondary px-2" aria-label="Previous month"><x-icon name="chevron-left" /></a>
                <a href="{{ route('activities.index', $query(['month' => $month->addMonth()->format('Y-m')])) }}" class="btn-secondary px-2" aria-label="Next month"><x-icon name="chevron-right" /></a>
                <a href="{{ route('activities.index', $query()) }}" class="btn-secondary">Today</a>
                <h2 class="text-lg font-semibold">{{ $month->format('F Y') }}</h2>
            </div>
            <div class="mx-4 mb-6 overflow-hidden rounded-lg border border-slate-200">
                <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50 text-center text-xs font-semibold tracking-wide text-slate-600 uppercase">
                    @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
                        <div class="py-2">{{ $weekday }}</div>
                    @endforeach
                </div>
                @foreach ($weeks as $week)
                    <div class="grid grid-cols-7 border-b border-slate-100 last:border-b-0">
                        @foreach ($week as $day)
                            @php $dayActivities = $byDay->get($day->toDateString(), collect()); @endphp
                            <div @class(['min-h-28 border-r border-slate-100 p-1.5 last:border-r-0', 'bg-slate-50/70 text-slate-400' => ! $day->isSameMonth($month)])>
                                <div class="mb-1 flex justify-end">
                                    <span @class(['flex size-6 items-center justify-center rounded-full text-xs font-medium', 'bg-brand-600 text-white' => $day->isToday()])>{{ $day->day }}</span>
                                </div>
                                <div class="space-y-1">
                                    @foreach ($dayActivities->take(4) as $activity)
                                        <a href="{{ route('activities.edit', $activity) }}" @class([
                                            'flex items-center gap-1 truncate rounded px-1.5 py-0.5 text-xs',
                                            'bg-slate-100 text-slate-400 line-through' => $activity->done,
                                            'bg-red-50 text-red-700' => $activity->isOverdue(),
                                            'bg-brand-50 text-brand-700 hover:bg-brand-100' => ! $activity->done && ! $activity->isOverdue(),
                                        ]) title="{{ $activity->subject }}">
                                            <x-icon :name="$activity->type->icon()" class="size-3 shrink-0" />
                                            <span class="truncate">{{ $activity->formattedDueTime() }} {{ $activity->subject }}</span>
                                        </a>
                                    @endforeach
                                    @if ($dayActivities->count() > 4)
                                        <p class="px-1.5 text-xs text-slate-500">+{{ $dayActivities->count() - 4 }} more</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @include('activities._create-modal')
</x-app-layout>
