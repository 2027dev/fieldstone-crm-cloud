@props(['align' => 'right', 'width' => 'w-48'])
<div x-data="{ open: false }" class="relative" x-on:keydown.escape="open = false">
    <div x-on:click="open = ! open">{{ $trigger }}</div>
    <div
        x-show="open"
        x-cloak
        x-transition.origin.top
        x-on:click.outside="open = false"
        class="absolute z-40 mt-1 {{ $width }} {{ $align === 'right' ? 'right-0' : 'left-0' }} rounded-lg border border-slate-200 bg-white py-1 shadow-lg"
    >
        {{ $slot }}
    </div>
</div>
