<x-app-layout title="Insights">
    <x-slot:subnav>@include('insights._subnav')</x-slot:subnav>

    <div class="flex flex-1 flex-col items-center justify-center bg-slate-50 px-6 py-12">
        <div class="grid w-full max-w-3xl grid-cols-5 gap-5" aria-hidden="true">
            <div class="relative col-span-3 rounded-lg bg-white p-6 shadow-sm">
                <x-icon name="sparkles" class="absolute top-4 left-4 size-6 text-brand-500" />
                <span class="absolute -top-3 -right-3 rounded-md bg-brand-600 px-2 py-1 text-sm font-bold text-white">AI</span>
                <div class="mt-6 space-y-3 border-l border-slate-200 pl-2">
                    <div class="h-10 w-3/4 bg-brand-200"></div>
                    <div class="h-10 w-11/12 bg-brand-500"></div>
                </div>
            </div>
            <div class="col-span-2 flex items-center justify-center rounded-lg bg-white p-6 shadow-sm">
                <div class="size-32 rounded-full" style="background: conic-gradient(#2e7d32 0 12%, #66bb6a 12% 70%, #a5d6a7 70% 100%)"></div>
            </div>
            <div class="col-span-2 space-y-2 rounded-lg bg-white p-6 shadow-sm">
                <div class="h-3 w-full bg-go-700"></div><div class="h-3 w-3/5 bg-green-500"></div><div class="h-3 w-1/3 bg-green-400"></div><div class="h-3 w-1/6 bg-green-300"></div>
            </div>
            <div class="col-span-3 flex items-end gap-4 rounded-lg bg-white p-6 shadow-sm">
                @foreach ([90, 75, 60, 88] as $height)
                    <div class="flex flex-1 flex-col" style="height: {{ $height }}px"><div class="h-1/4 bg-green-600"></div><div class="h-1/4 bg-green-400"></div><div class="h-1/2 bg-green-200"></div></div>
                @endforeach
            </div>
        </div>

        <h2 class="mt-12 text-3xl font-medium text-slate-900">Identify growth opportunities. Take action.</h2>
        <p class="mt-4 max-w-3xl text-center text-slate-700">Set up your personalized, customizable reporting dashboard. Track Fieldstone data related to your sales activities. Make informed decisions at the right time.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'dashboard')" class="btn-primary px-4 py-2">Create dashboard</button>
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'report')" class="btn-secondary px-4 py-2"><x-icon name="chart" class="text-brand-600" /> Create report</button>
        </div>
    </div>
</x-app-layout>
