@php $lead ??= null; @endphp
<div class="grid gap-4 sm:grid-cols-2">
    <x-field label="Title" name="title" :value="$lead?->title" required class="sm:col-span-2" placeholder="e.g. Greenfield Farms lead" />
    <x-select label="Contact person" name="person_id" placeholder="None" :options="$people->pluck('name', 'id')" :value="$lead?->person_id" />
    <x-select label="Organization" name="organization_id" placeholder="None" :options="$organizations->pluck('name', 'id')" :value="$lead?->organization_id" />
    <div class="grid grid-cols-3 gap-2">
        <x-field label="Value" name="value" type="number" step="0.01" min="0" :value="$lead?->value ? (float) $lead->value : null" class="col-span-2" />
        <x-select label="Currency" name="currency" :options="['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP']" :value="$lead?->currency ?? 'USD'" />
    </div>
    <x-select label="Label" name="label" placeholder="None" :options="collect(\App\Enums\LeadLabel::cases())->mapWithKeys(fn ($l) => [$l->value => $l->label()])" :value="$lead?->label?->value" />
    <x-select label="Source" name="source" :options="collect(\App\Enums\LeadSource::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])" :value="$lead?->source?->value ?? 'manual'" class="sm:col-span-2" />
    <x-field label="Details" name="message" type="textarea" :value="$lead?->message" class="sm:col-span-2" />
</div>
