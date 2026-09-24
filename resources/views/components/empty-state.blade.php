@props(['title', 'description' => null, 'icon' => 'inbox'])
<div class="flex flex-col items-center justify-center px-6 py-16 text-center">
    <div class="mb-4 flex size-14 items-center justify-center rounded-full bg-brand-50 text-brand-600">
        <x-icon :name="$icon" class="size-7" />
    </div>
    <h3 class="text-xl font-medium text-slate-900">{{ $title }}</h3>
    @if ($description)
        <p class="mt-2 max-w-lg text-sm text-slate-600">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-5 flex flex-wrap items-center justify-center gap-3">{{ $slot }}</div>
    @endif
</div>
