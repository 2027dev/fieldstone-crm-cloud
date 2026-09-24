@props(['label', 'name', 'type' => 'text', 'value' => null, 'required' => false, 'placeholder' => null])
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="text-red-600"> *</span>@endif</label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="4" class="input" placeholder="{{ $placeholder }}" @required($required)>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" class="input" placeholder="{{ $placeholder }}" @required($required) {{ $attributes->except('class') }} />
    @endif
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
