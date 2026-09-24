<x-guest-layout title="Create account">
    <h1 class="text-2xl font-semibold text-slate-900">Create your workspace</h1>
    <p class="mt-1 text-sm text-slate-600">Free to start. We'll add some sample data to help you explore.</p>

    <form method="post" action="{{ route('register') }}" class="mt-8 space-y-4">
        @csrf
        <x-field label="Your name" name="name" required autofocus autocomplete="name" />
        <x-field label="Company" name="company_name" placeholder="Agent Solutions" autocomplete="organization" />
        <x-field label="Work email" name="email" type="email" required autocomplete="email" />
        <x-field label="Password" name="password" type="password" required autocomplete="new-password" />
        <x-field label="Confirm password" name="password_confirmation" type="password" required autocomplete="new-password" />
        <button type="submit" class="btn-primary w-full py-2.5">Create account</button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-600">
        Already have an account? <a href="{{ route('login') }}" class="font-semibold text-link hover:underline">Log in</a>
    </p>
</x-guest-layout>
