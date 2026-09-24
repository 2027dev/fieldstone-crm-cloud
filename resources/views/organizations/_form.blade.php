@php $organization ??= null; @endphp
<div class="space-y-4">
    <x-field label="Name" name="name" :value="$organization?->name" required />
    <x-field label="Address" name="address" :value="$organization?->address" />
    <x-field label="Website" name="website" type="url" :value="$organization?->website" placeholder="https://" />
</div>
