@php
    $activity ??= null;
    $defaults ??= [];
    $currentType = old('type', $activity?->type?->value ?? ($defaults['type'] ?? 'call'));
@endphp
<div class="space-y-4" x-data="{ type: @js($currentType) }">
    <x-field label="Subject" name="subject" :value="$activity?->subject ?? ($defaults['subject'] ?? null)" required placeholder="Call" />
    <div>
        <span class="label">Type</span>
        <div class="flex flex-wrap gap-1.5">
            @foreach (\App\Enums\ActivityType::cases() as $activityType)
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="{{ $activityType->value }}" x-model="type" class="peer sr-only">
                    <span class="inline-flex items-center gap-1 rounded-md border border-slate-300 px-2.5 py-1.5 text-sm text-slate-700 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-300">
                        <x-icon :name="$activityType->icon()" class="size-4" /> {{ $activityType->label() }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-3">
        <x-field label="Due date" name="due_date" type="date" :value="$activity?->due_date?->toDateString() ?? ($defaults['due_date'] ?? today()->toDateString())" />
        <x-field label="Time" name="due_time" type="time" :value="$activity?->formattedDueTime()" />
        <x-field label="Duration (min)" name="duration_minutes" type="number" min="0" max="1440" :value="$activity?->duration_minutes" />
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-select label="Priority" name="priority" placeholder="None" :options="collect(\App\Enums\ActivityPriority::cases())->mapWithKeys(fn ($p) => [$p->value => $p->label()])" :value="$activity?->priority?->value" />
        <x-field label="Outcome" name="outcome" :value="$activity?->outcome" placeholder="e.g. Left voicemail" />
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-select label="Contact person" name="person_id" placeholder="None" :options="$people->pluck('name', 'id')" :value="$activity?->person_id ?? ($defaults['person_id'] ?? null)" />
        <x-select label="Deal" name="deal_id" placeholder="None" :options="$deals->pluck('title', 'id')" :value="$activity?->deal_id ?? ($defaults['deal_id'] ?? null)" />
    </div>
    @foreach (['organization_id', 'lead_id'] as $hidden)
        @if (! empty($defaults[$hidden]))
            <input type="hidden" name="{{ $hidden }}" value="{{ $defaults[$hidden] }}">
        @endif
    @endforeach
    <x-field label="Note" name="note" type="textarea" :value="$activity?->note" placeholder="Notes are visible to your team" />
    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="hidden" name="done" value="0">
        <input type="checkbox" name="done" value="1" class="rounded border-slate-300 text-go-600" @checked(old('done', $activity?->done))> Mark as done
    </label>
</div>
