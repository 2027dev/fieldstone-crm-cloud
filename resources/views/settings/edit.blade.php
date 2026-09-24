<x-app-layout title="Settings">
    <div class="mx-auto w-full max-w-2xl space-y-6 p-6">
        <form method="post" action="{{ route('settings.update') }}" class="card">
            @csrf @method('put')
            <div class="border-b border-slate-200 px-5 py-3"><h2 class="font-semibold">Profile &amp; workspace</h2></div>
            <div class="space-y-4 p-5">
                <x-field label="Name" name="name" :value="auth()->user()->name" required />
                <x-field label="Company" name="company_name" :value="auth()->user()->company_name" />
                <x-field label="Email" name="email" type="email" :value="auth()->user()->email" required />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-field label="New password" name="password" type="password" autocomplete="new-password" placeholder="Leave blank to keep" />
                    <x-field label="Confirm new password" name="password_confirmation" type="password" autocomplete="new-password" />
                </div>
            </div>
            <div class="flex justify-end rounded-b-lg border-t border-slate-200 bg-slate-50 px-5 py-3"><button type="submit" class="btn-primary">Save settings</button></div>
        </form>

        <div class="card">
            <div class="border-b border-slate-200 px-5 py-3"><h2 class="font-semibold">Sample data</h2></div>
            <div class="flex flex-wrap items-center justify-between gap-4 p-5">
                <p class="max-w-md text-sm text-slate-600">Sample records are prefixed with “[Sample]”. Remove them once you've added your own data, or add them back to explore features.</p>
                <div class="flex gap-2">
                    <form method="post" action="{{ route('sample-data.store') }}">@csrf<button type="submit" class="btn-secondary">Add sample data</button></form>
                    <form method="post" action="{{ route('sample-data.destroy') }}" onsubmit="return confirm('Remove all sample data?')">@csrf @method('delete')<button type="submit" class="btn-secondary text-red-600">Remove sample data</button></form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
