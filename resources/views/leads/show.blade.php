@php use App\Support\Money; @endphp
<x-app-layout :title="$lead->title" :breadcrumbs="['Leads' => route('leads.index'), 'Leads Inbox' => route('leads.index')]">
    <x-slot:subnav>@include('leads._subnav')</x-slot:subnav>

    <div class="flex flex-wrap items-center gap-4 border-b border-slate-200 px-6 py-5">
        <span class="flex size-12 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="target" class="size-6" /></span>
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
                <h2 class="truncate text-2xl font-semibold">{{ $lead->title }}</h2>
                @if ($lead->label)<span class="rounded px-1.5 py-0.5 text-xs font-semibold {{ $lead->label->badgeClasses() }}">{{ $lead->label->label() }}</span>@endif
                @if ($lead->archived_at)<span class="rounded bg-slate-100 px-1.5 py-0.5 text-xs font-semibold text-slate-600">Archived</span>@endif
            </div>
            <p class="text-sm text-slate-600">{{ $lead->source->label() }}@if ($lead->webForm) · {{ $lead->webForm->name }}@endif · added {{ $lead->created_at->diffForHumans() }}</p>
        </div>
        @if ($lead->converted_at)
            <a href="{{ $lead->deal ? route('deals.show', $lead->deal) : route('deals.index') }}" class="btn-secondary"><x-icon name="dollar" /> View deal</a>
        @else
            <form method="post" action="{{ route('leads.convert', $lead) }}">@csrf<button type="submit" class="btn-primary"><x-icon name="dollar" /> Convert to deal</button></form>
            <form method="post" action="{{ route('leads.archive', $lead) }}">@csrf @method('patch')<button type="submit" class="btn-secondary"><x-icon name="archive" /> {{ $lead->archived_at ? 'Unarchive' : 'Archive' }}</button></form>
        @endif
        <a href="{{ route('leads.edit', $lead) }}" class="btn-secondary"><x-icon name="pencil" /> Edit</a>
        <form method="post" action="{{ route('leads.destroy', $lead) }}" onsubmit="return confirm('Delete this lead?')">@csrf @method('delete')<button type="submit" class="btn-secondary text-red-600" aria-label="Delete lead"><x-icon name="trash" /></button></form>
    </div>

    <div class="grid gap-6 p-6 xl:grid-cols-3">
        <div class="space-y-6">
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-3"><h3 class="font-semibold">Details</h3></div>
                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-28 shrink-0 text-slate-500">Value</dt><dd>{{ $lead->value !== null ? Money::format($lead->value, $lead->currency) : '—' }}</dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-28 shrink-0 text-slate-500">Contact</dt><dd>@if ($lead->person)<a href="{{ route('people.show', $lead->person) }}" class="text-link">{{ $lead->person->name }}</a><br><span class="text-slate-500">{{ $lead->person->email }}</span>@else — @endif</dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-28 shrink-0 text-slate-500">Organization</dt><dd>@if ($lead->organization)<a href="{{ route('organizations.show', $lead->organization) }}" class="text-link">{{ $lead->organization->name }}</a>@else — @endif</dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-28 shrink-0 text-slate-500">Source</dt><dd>{{ $lead->source->label() }}</dd></div>
                </dl>
            </div>
            @if ($lead->message)
                <div class="card p-4">
                    <h3 class="mb-2 font-semibold">Message</h3>
                    <p class="text-sm whitespace-pre-line text-slate-700">{{ $lead->message }}</p>
                </div>
            @endif
        </div>
        <div class="space-y-6 xl:col-span-2">
            @include('partials.activity-list', ['activities' => $lead->activities])
            @include('partials.notes', ['notable' => $lead, 'notableType' => 'lead'])
        </div>
    </div>

    @include('activities._create-modal', ['defaults' => ['lead_id' => $lead->id, 'person_id' => $lead->person_id, 'organization_id' => $lead->organization_id]])
</x-app-layout>
