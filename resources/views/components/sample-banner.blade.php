@props(['title', 'description', 'icon' => 'contacts'])
<div x-data="{ show: true }" x-show="show" class="relative mt-auto overflow-hidden border-t border-slate-100 bg-gradient-to-b from-white to-brand-50/60">
    <div class="mx-auto flex max-w-4xl flex-col items-center gap-6 px-6 py-10 sm:flex-row sm:justify-center">
        <div class="flex h-28 w-48 shrink-0 flex-col justify-between rounded-lg bg-navy-800 p-3 text-white shadow-md">
            <span class="text-[10px] font-semibold tracking-wide text-brand-200">fieldstone</span>
            <div class="flex items-end justify-between">
                <span class="text-lg leading-tight font-semibold">{{ $title }}</span>
                <x-icon :name="$icon" class="size-8 text-go-50/80" />
            </div>
        </div>
        <div class="max-w-sm text-sm text-slate-600">
            <p>{{ $description }}</p>
            <div class="mt-3 flex flex-wrap gap-2">{{ $slot }}</div>
        </div>
    </div>
    <button type="button" x-on:click="show = false" class="absolute top-4 right-4 rounded p-1 text-slate-500 hover:bg-slate-100" aria-label="Dismiss">
        <x-icon name="x" class="size-5" />
    </button>
</div>
