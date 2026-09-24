@props(['title', 'breadcrumbs' => [], 'info' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-white font-sans text-slate-900 antialiased" x-data="{ nav: false }">
@php
    $navigation = [
        ['label' => 'Setup guide', 'icon' => 'guide', 'route' => 'setup', 'active' => ['setup'], 'badge' => $setupRemaining ?: null],
        ['label' => 'Contacts', 'icon' => 'contacts', 'route' => 'people.index', 'active' => ['people.*', 'organizations.*', 'contacts.*']],
        ['label' => 'Activities', 'icon' => 'calendar', 'route' => 'activities.index', 'active' => ['activities.*']],
        ['label' => 'Deals', 'icon' => 'dollar', 'route' => 'deals.index', 'active' => ['deals.*']],
        ['label' => 'Leads', 'icon' => 'target', 'route' => 'leads.index', 'active' => ['leads.*', 'web-forms.*']],
        ['label' => 'Insights', 'icon' => 'chart', 'route' => 'insights', 'active' => ['insights', 'dashboards.*', 'reports.*']],
        ['label' => 'Sales Inbox', 'icon' => 'mail', 'route' => 'inbox', 'active' => ['inbox', 'emails.*']],
    ];
@endphp
<div class="flex h-full">
    {{-- Primary navigation --}}
    <div x-show="nav" x-cloak x-on:click="nav = false" class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden"></div>
    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-navy-900 text-white transition-transform lg:static lg:translate-x-0"
        x-bind:class="nav && 'translate-x-0'"
    >
        <a href="{{ route('setup') }}" class="flex h-16 items-center gap-2 px-6">
            <span class="flex size-7 items-center justify-center rounded-md bg-white text-sm font-black text-navy-900">f</span>
            <span class="text-[22px] font-bold tracking-tight">fieldstone</span>
        </a>
        <nav class="mt-3 flex-1 space-y-1 px-2.5">
            @foreach ($navigation as $item)
                @php $isActive = request()->routeIs(...$item['active']); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'flex items-center gap-3 rounded-md px-3 py-2.5 text-[15px] font-medium transition',
                        'bg-brand-600 text-white shadow-sm' => $isActive,
                        'text-white/90 hover:bg-white/10' => ! $isActive,
                    ])
                    @if ($isActive) aria-current="page" @endif
                >
                    <x-icon :name="$item['icon']" class="size-[18px]" />
                    <span class="flex-1">{{ $item['label'] }}</span>
                    @if (! empty($item['badge']))
                        <span class="flex size-5 items-center justify-center rounded-full bg-[#3b82f6] text-[11px] font-semibold">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach

            <div x-data="{ open: false }" class="relative">
                <button type="button" x-on:click="open = ! open" class="flex w-full items-center gap-3 rounded-md px-3 py-2.5 text-[15px] font-medium text-white/90 hover:bg-white/10">
                    <x-icon name="dots" class="size-[18px]" />
                    <span class="flex-1 text-left">More</span>
                    <x-icon name="chevron-down" class="size-4 transition" x-bind:class="open && 'rotate-180'" />
                </button>
                <div x-show="open" x-cloak class="mt-1 space-y-1 pl-4">
                    <a href="{{ route('organizations.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-white/80 hover:bg-white/10"><x-icon name="building" /> Organizations</a>
                    <a href="{{ route('web-forms.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-white/80 hover:bg-white/10"><x-icon name="forms" /> Web forms</a>
                    <a href="{{ route('contacts.duplicates') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-white/80 hover:bg-white/10"><x-icon name="merge" /> Merge duplicates</a>
                    <a href="{{ route('settings') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-white/80 hover:bg-white/10"><x-icon name="settings" /> Settings</a>
                </div>
            </div>
        </nav>
        <div class="border-t border-white/10 px-5 py-4 text-xs text-white/60">
            {{ auth()->user()->workspaceName() }}
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        {{-- Top bar --}}
        <header class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-200 bg-white px-4 lg:px-5">
            <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-100 lg:hidden" x-on:click="nav = true" aria-label="Open navigation">
                <x-icon name="menu" class="size-5" />
            </button>
            <div class="flex min-w-0 items-center gap-2 text-lg">
                @forelse ($breadcrumbs as $label => $url)
                    <a href="{{ $url }}" class="truncate font-medium text-slate-600 hover:text-slate-900">{{ $label }}</a>
                    <span class="text-slate-400">/</span>
                @empty
                @endforelse
                <h1 class="truncate font-semibold text-slate-900">{{ $title }}</h1>
                @if ($info)
                    <span class="text-slate-500" title="{{ $info }}"><x-icon name="info" class="size-4" /></span>
                @endif
            </div>

            <div class="ml-auto flex flex-1 items-center justify-end gap-2 md:justify-center" x-data="globalSearch('{{ route('search') }}')" x-on:click.outside="open = false">
                <form action="{{ route('search') }}" method="get" class="relative hidden w-full max-w-md md:block">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 text-slate-500" />
                    <input
                        type="search"
                        name="q"
                        x-model="term"
                        x-on:input="search()"
                        x-on:focus="open = ! isEmpty"
                        placeholder="Search Fieldstone"
                        autocomplete="off"
                        class="w-full rounded-full border border-slate-300 bg-white py-2 pr-4 pl-10 text-sm placeholder:text-slate-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none"
                    >
                    <div x-show="open" x-cloak class="absolute inset-x-0 top-full z-40 mt-2 max-h-96 overflow-y-auto rounded-lg border border-slate-200 bg-white py-2 shadow-xl">
                        <template x-if="isEmpty && ! loading">
                            <p class="px-4 py-3 text-sm text-slate-500">No results for “<span x-text="term"></span>”</p>
                        </template>
                        <template x-for="(items, group) in groups" :key="group">
                            <div class="py-1">
                                <p class="px-4 py-1 text-xs font-semibold tracking-wide text-slate-500 uppercase" x-text="group"></p>
                                <template x-for="item in items" :key="item.url">
                                    <a :href="item.url" class="block px-4 py-2 hover:bg-slate-50">
                                        <span class="block text-sm font-medium text-slate-900" x-text="item.title"></span>
                                        <span class="block text-xs text-slate-500" x-text="item.subtitle"></span>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </form>
                <a href="{{ route('search') }}" class="rounded-full p-2 text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Search"><x-icon name="search" class="size-5" /></a>
                <x-dropdown align="right" width="w-52">
                    <x-slot:trigger>
                        <button type="button" class="flex size-10 items-center justify-center rounded-full border border-slate-300 text-slate-700 hover:bg-slate-50" aria-label="Quick add">
                            <x-icon name="plus" class="size-5" />
                        </button>
                    </x-slot:trigger>
                    <p class="px-3 pt-1 pb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">Add new</p>
                    <x-dropdown-link :href="route('leads.index', ['create' => 'lead'])" icon="target">Lead</x-dropdown-link>
                    <x-dropdown-link :href="route('deals.index', ['create' => 'deal'])" icon="dollar">Deal</x-dropdown-link>
                    <x-dropdown-link :href="route('activities.index', ['create' => 'activity'])" icon="calendar">Activity</x-dropdown-link>
                    <x-dropdown-link :href="route('people.index', ['create' => 'person'])" icon="user">Person</x-dropdown-link>
                    <x-dropdown-link :href="route('organizations.index', ['create' => 'organization'])" icon="building">Organization</x-dropdown-link>
                    <x-dropdown-link :href="route('inbox', ['create' => 'email'])" icon="mail">Email</x-dropdown-link>
                </x-dropdown>
            </div>

            <div class="flex items-center gap-1">
                <a href="{{ route('setup') }}" class="hidden rounded-full p-2 text-slate-600 hover:bg-slate-100 sm:block" aria-label="Help"><x-icon name="help" class="size-5" /></a>
                <x-dropdown align="right" width="w-60">
                    <x-slot:trigger>
                        <button type="button" class="ml-1 flex items-center rounded-full" aria-label="Account menu">
                            <x-avatar :initials="auth()->user()->initials()" size="size-9" class="bg-slate-200 text-slate-700" />
                        </button>
                    </x-slot:trigger>
                    <div class="border-b border-slate-100 px-3 pt-1 pb-2">
                        <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                    <x-dropdown-link :href="route('settings')" icon="settings">Settings</x-dropdown-link>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link icon="logout">Log out</x-dropdown-link>
                    </form>
                </x-dropdown>
            </div>
        </header>

        <div class="flex min-h-0 flex-1">
            @isset($subnav)
                <aside class="hidden w-64 shrink-0 flex-col overflow-y-auto border-r border-slate-200 bg-slate-50 p-2.5 lg:flex">
                    {{ $subnav }}
                </aside>
            @endisset
            <main class="flex min-w-0 flex-1 flex-col overflow-y-auto bg-white *:shrink-0">
                @if (session('status'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="fixed right-6 bottom-6 z-50 flex items-center gap-3 rounded-lg bg-navy-900 px-4 py-3 text-sm text-white shadow-xl" role="status">
                        <x-icon name="check" class="size-4 text-green-300" />
                        {{ session('status') }}
                        <button type="button" x-on:click="show = false" class="text-white/60 hover:text-white" aria-label="Dismiss"><x-icon name="x" class="size-4" /></button>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</div>
</body>
</html>
