@props(['title'])
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
<body class="h-full bg-slate-50 font-sans text-slate-900 antialiased">
<div class="grid min-h-full lg:grid-cols-2">
    <div class="relative hidden flex-col justify-between overflow-hidden bg-navy-900 p-12 text-white lg:flex">
        <div class="flex items-center gap-2">
            <span class="flex size-8 items-center justify-center rounded-md bg-white text-base font-black text-navy-900">f</span>
            <span class="text-2xl font-bold tracking-tight">fieldstone</span>
        </div>
        <div class="relative z-10 max-w-md">
            <h2 class="text-4xl leading-tight font-semibold">The CRM that keeps every deal moving.</h2>
            <p class="mt-4 text-lg text-white/70">Track contacts, schedule activities, qualify leads and close deals — all in one calm, focused workspace.</p>
            <ul class="mt-8 space-y-3 text-white/85">
                <li class="flex items-center gap-3"><x-icon name="check" class="size-5 text-green-300" /> Visual deal pipeline with drag &amp; drop</li>
                <li class="flex items-center gap-3"><x-icon name="check" class="size-5 text-green-300" /> Leads inbox with shareable web forms</li>
                <li class="flex items-center gap-3"><x-icon name="check" class="size-5 text-green-300" /> Activities, calendar and insights dashboards</li>
            </ul>
        </div>
        <p class="text-sm text-white/50">&copy; {{ date('Y') }} Fieldstone CRM</p>
        <div class="absolute -right-24 -bottom-24 size-96 rounded-full bg-brand-600/30 blur-3xl"></div>
    </div>
    <div class="flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 flex items-center gap-2 lg:hidden">
                <span class="flex size-8 items-center justify-center rounded-md bg-navy-900 text-base font-black text-white">f</span>
                <span class="text-2xl font-bold tracking-tight text-navy-900">fieldstone</span>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
</body>
</html>
