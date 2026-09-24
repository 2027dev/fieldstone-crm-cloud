<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $webForm->headline ?: $webForm->name }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-full items-center justify-center bg-slate-100 px-4 py-12 font-sans text-slate-900 antialiased">
    <div class="w-full max-w-lg rounded-xl bg-white p-8 shadow-lg">
        @if (session('submitted'))
            <div class="py-8 text-center">
                <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-green-100 text-green-700"><x-icon name="check" class="size-7" /></div>
                <p class="text-lg font-semibold">{{ $webForm->success_message }}</p>
            </div>
        @else
            <h1 class="text-2xl font-semibold">{{ $webForm->headline ?: $webForm->name }}</h1>
            <p class="mt-1 text-sm text-slate-600">Fill in your details and we'll get back to you.</p>
            <form method="post" action="{{ route('forms.public.store', $webForm->slug) }}" class="mt-6 space-y-4">
                @csrf
                <x-field label="Full name" name="name" required autocomplete="name" />
                <x-field label="Email" name="email" type="email" required autocomplete="email" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-field label="Phone" name="phone" autocomplete="tel" />
                    <x-field label="Company" name="company" autocomplete="organization" />
                </div>
                <x-field label="How can we help?" name="message" type="textarea" />
                <div class="hidden" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
                <button type="submit" class="btn-primary w-full py-2.5">{{ $webForm->button_label }}</button>
            </form>
        @endif
        <p class="mt-6 text-center text-xs text-slate-400">Powered by Fieldstone CRM</p>
    </div>
</body>
</html>
