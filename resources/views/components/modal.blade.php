@props(['name', 'title', 'width' => 'max-w-lg'])
@php
    $shouldOpen = (old('_modal') === $name && $errors->any()) || request('create') === $name;
@endphp
<div
    x-data="{ open: @js($shouldOpen) }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-900/40 px-4 py-12"
    role="dialog"
    aria-modal="true"
>
    <div x-show="open" x-transition x-on:click.outside="open = false" class="w-full {{ $width }} rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3.5">
            <h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>
            <button type="button" x-on:click="open = false" class="rounded p-1 text-slate-500 hover:bg-slate-100" aria-label="Close">
                <x-icon name="x" class="size-5" />
            </button>
        </div>
        {{ $slot }}
    </div>
</div>
