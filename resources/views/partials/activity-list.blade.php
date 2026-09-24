{{-- Expects $activities and optional $context (array of hidden defaults for the quick-add form) --}}
@php $context ??= []; @endphp
<div class="card">
    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
        <h3 class="font-semibold">Activities</h3>
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'activity')" class="btn-secondary btn-sm"><x-icon name="plus" class="size-3.5" /> Activity</button>
    </div>
    <ul class="divide-y divide-slate-100">
        @forelse ($activities as $activity)
            <li class="flex items-center gap-3 px-4 py-2.5" x-data="doneToggle(@js($activity->done), '{{ route('activities.toggle', $activity) }}')">
                <button type="button" x-on:click="toggle()" class="flex size-5 shrink-0 items-center justify-center rounded-full border-2" x-bind:class="done ? 'border-go-600 bg-go-600 text-white' : 'border-slate-300 hover:border-go-600'" aria-label="Toggle done">
                    <x-icon name="check" class="size-3" x-show="done" />
                </button>
                <x-icon :name="$activity->type->icon()" class="size-4 shrink-0 text-slate-500" />
                <div class="min-w-0 flex-1">
                    <a href="{{ route('activities.edit', $activity) }}" class="block truncate text-sm font-medium hover:text-link" x-bind:class="done && 'line-through text-slate-400'">{{ $activity->subject }}</a>
                    <p @class(['text-xs', 'text-red-600' => $activity->isOverdue(), 'text-slate-500' => ! $activity->isOverdue()])>
                        {{ $activity->due_date?->format('M j, Y') ?? 'No due date' }} {{ $activity->formattedDueTime() }}
                        @if ($activity->deal) · {{ $activity->deal->title }} @endif
                    </p>
                </div>
                @if ($activity->priority)
                    <span class="rounded px-1.5 py-0.5 text-xs font-medium {{ $activity->priority->badgeClasses() }}">{{ $activity->priority->label() }}</span>
                @endif
            </li>
        @empty
            <li class="px-4 py-6 text-center text-sm text-slate-500">No activities scheduled.</li>
        @endforelse
    </ul>
</div>
