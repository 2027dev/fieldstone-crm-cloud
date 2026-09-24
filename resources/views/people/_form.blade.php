@php $person ??= null; @endphp
<div class="grid gap-4 sm:grid-cols-2">
    <x-field label="Name" name="name" :value="$person?->name" required class="sm:col-span-2" />
    <div class="sm:col-span-2" x-data="{ mode: '{{ old('organization_name') ? 'new' : 'existing' }}' }">
        <div class="mb-1 flex items-center justify-between">
            <label class="label mb-0" for="organization_id">Organization</label>
            <button type="button" class="text-xs font-medium text-link hover:underline" x-on:click="mode = mode === 'new' ? 'existing' : 'new'" x-text="mode === 'new' ? 'Choose existing' : '+ New organization'"></button>
        </div>
        <select x-show="mode === 'existing'" id="organization_id" name="organization_id" class="input" x-bind:disabled="mode === 'new'">
            <option value="">No organization</option>
            @foreach ($organizations as $organization)
                <option value="{{ $organization->id }}" @selected((string) old('organization_id', $person?->organization_id) === (string) $organization->id)>{{ $organization->name }}</option>
            @endforeach
        </select>
        <input x-show="mode === 'new'" x-cloak type="text" name="organization_name" value="{{ old('organization_name') }}" class="input" placeholder="New organization name" x-bind:disabled="mode === 'existing'">
        @error('organization_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <x-field label="Email" name="email" type="email" :value="$person?->email" />
    <x-select label="Email label" name="email_label" :options="['Work' => 'Work', 'Home' => 'Home', 'Other' => 'Other']" :value="$person?->email_label ?? 'Work'" />
    <x-field label="Phone" name="phone" :value="$person?->phone" />
    <x-select label="Phone label" name="phone_label" :options="['Work' => 'Work', 'Mobile' => 'Mobile', 'Home' => 'Home', 'Other' => 'Other']" :value="$person?->phone_label ?? 'Work'" />
    <x-field label="Job title" name="job_title" :value="$person?->job_title" class="sm:col-span-2" />
</div>
