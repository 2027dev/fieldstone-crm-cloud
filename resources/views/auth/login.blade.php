<x-guest-layout title="Log in">
    <h1 class="text-2xl font-semibold text-slate-900">Welcome back</h1>
    <p class="mt-1 text-sm text-slate-600">Log in to your Fieldstone workspace.</p>

    <form method="post" action="{{ route('login') }}" class="mt-8 space-y-4">
        @csrf
        <x-field label="Email" name="email" type="email" required autofocus autocomplete="email" />
        <x-field label="Password" name="password" type="password" required autocomplete="current-password" />
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600"> Remember me
        </label>
        <button type="submit" class="btn-primary w-full py-2.5">Log in</button>
    </form>

    <div class="my-6 flex items-center gap-3 text-xs text-slate-400">
        <span class="h-px flex-1 bg-slate-200"></span> or <span class="h-px flex-1 bg-slate-200"></span>
    </div>

    <form method="post" action="{{ route('demo') }}">
        @csrf
        <button type="submit" class="btn-secondary w-full py-2.5">
            <x-icon name="sparkles" class="size-4 text-brand-600" /> Explore a demo workspace
        </button>
    </form>
    <p class="mt-2 text-center text-xs text-slate-500">Creates a private workspace pre-filled with sample data.</p>

    <p class="mt-8 text-center text-sm text-slate-600">
        New to Fieldstone? <a href="{{ route('register') }}" class="font-semibold text-link hover:underline">Create an account</a>
    </p>
</x-guest-layout>
