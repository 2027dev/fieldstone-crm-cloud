@php $deal ??= null; @endphp
<div class="grid gap-4 sm:grid-cols-2">
    <x-field label="Title" name="title" :value="$deal?->title" required class="sm:col-span-2" />
    <x-select label="Contact person" name="person_id" placeholder="None" :options="$people->pluck('name', 'id')" :value="$deal?->person_id" />
    <x-select label="Organization" name="organization_id" placeholder="None" :options="$organizations->pluck('name', 'id')" :value="$deal?->organization_id" />
    <div class="grid grid-cols-3 gap-2">
        <x-field label="Value" name="value" type="number" step="0.01" min="0" :value="$deal?->value ? (float) $deal->value : null" class="col-span-2" />
        <x-select label="Currency" name="currency" :options="['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP']" :value="$deal?->currency ?? 'USD'" />
    </div>
    <x-field label="Expected close date" name="expected_close_date" type="date" :value="$deal?->expected_close_date?->toDateString()" />
    <div class="sm:col-span-2">
        <span class="label">Pipeline stage</span>
        <div class="flex overflow-hidden rounded-md border border-slate-300">
            @foreach (\App\Enums\DealStage::cases() as $stage)
                <label class="flex-1 cursor-pointer border-r border-slate-300 last:border-r-0" title="{{ $stage->label() }}">
                    <input type="radio" name="stage" value="{{ $stage->value }}" class="peer sr-only" @checked(old('stage', $deal?->stage?->value ?? 'qualified') === $stage->value)>
                    <span class="block truncate px-2 py-2 text-center text-xs font-medium text-slate-600 peer-checked:bg-go-600 peer-checked:text-white hover:bg-slate-50 peer-checked:hover:bg-go-600">{{ $stage->label() }}</span>
                </label>
            @endforeach
        </div>
        @error('stage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
