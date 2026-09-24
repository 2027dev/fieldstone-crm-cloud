<x-app-layout title="Search">
    <div class="mx-auto w-full max-w-3xl p-6">
        <form method="get" action="{{ route('search') }}" class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-slate-500" />
            <input type="search" name="q" value="{{ $term }}" autofocus placeholder="Search people, organizations, deals and leads" class="input py-3 pl-10 text-base">
        </form>

        @if ($term === '')
            <p class="mt-8 text-center text-sm text-slate-500">Start typing to search your workspace.</p>
        @elseif ($results->isEmpty())
            <x-empty-state icon="search" title="No results for “{{ $term }}”" description="Try a different name, email or deal title." />
        @else
            @foreach ($results as $group => $items)
                <h2 class="mt-8 mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">{{ $group }}</h2>
                <ul class="card divide-y divide-slate-100">
                    @foreach ($items as $item)
                        <li>
                            <a href="{{ $item['url'] }}" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50">
                                <x-icon :name="$item['icon']" class="size-5 text-slate-500" />
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-medium">{{ $item['title'] }}</span>
                                    <span class="block truncate text-xs text-slate-500">{{ $item['subtitle'] }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        @endif
    </div>
</x-app-layout>
