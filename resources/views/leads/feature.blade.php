<x-app-layout :title="$feature['title']" :breadcrumbs="['Leads' => route('leads.index')]">
    <x-slot:subnav>@include('leads._subnav')</x-slot:subnav>
    <x-empty-state :icon="$feature['icon']" :title="$feature['title']" :description="$feature['description']">
        <span class="rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700">Coming soon</span>
        <a href="{{ route('web-forms.index') }}" class="btn-secondary"><x-icon name="forms" /> Try Web Forms instead</a>
    </x-empty-state>
</x-app-layout>
