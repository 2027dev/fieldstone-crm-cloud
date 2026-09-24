<x-app-layout :title="$person->name" :breadcrumbs="['Contacts' => route('people.index'), 'People' => route('people.index')]">
    <x-slot:subnav>@include('contacts._subnav')</x-slot:subnav>

    <div class="border-b border-slate-200 px-6 py-5">
        <div class="flex flex-wrap items-center gap-4">
            <x-avatar :initials="$person->initials()" size="size-14" class="text-lg" />
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-2xl font-semibold">{{ $person->name }}</h2>
                <p class="text-sm text-slate-600">
                    {{ $person->job_title }}@if ($person->job_title && $person->organization) at @endif
                    @if ($person->organization)<a href="{{ route('organizations.show', $person->organization) }}" class="text-link hover:underline">{{ $person->organization->name }}</a>@endif
                </p>
            </div>
            <div class="flex gap-2">
                @if ($person->email)
                    <a href="{{ route('inbox', ['create' => 'email', 'to' => $person->email]) }}" class="btn-secondary"><x-icon name="mail" /> Email</a>
                @endif
                <a href="{{ route('people.edit', $person) }}" class="btn-secondary"><x-icon name="pencil" /> Edit</a>
                <form method="post" action="{{ route('people.destroy', $person) }}" onsubmit="return confirm('Delete this person? Their deals and activities will be kept.')">
                    @csrf @method('delete')
                    <button type="submit" class="btn-secondary text-red-600" aria-label="Delete"><x-icon name="trash" /></button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid gap-6 p-6 xl:grid-cols-3">
        <div class="space-y-6">
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-3"><h3 class="font-semibold">Details</h3></div>
                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-24 shrink-0 text-slate-500">Email</dt><dd>@if ($person->email)<a href="mailto:{{ $person->email }}" class="text-link">{{ $person->email }}</a> <span class="text-slate-500">({{ $person->email_label }})</span>@else — @endif</dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-24 shrink-0 text-slate-500">Phone</dt><dd>@if ($person->phone)<a href="tel:{{ $person->phone }}" class="text-link">{{ $person->phone }}</a> <span class="text-slate-500">({{ $person->phone_label }})</span>@else — @endif</dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-24 shrink-0 text-slate-500">Job title</dt><dd>{{ $person->job_title ?: '—' }}</dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-24 shrink-0 text-slate-500">Added</dt><dd>{{ $person->created_at->format('M j, Y') }}</dd></div>
                </dl>
            </div>

            <div class="card">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <h3 class="font-semibold">Deals</h3>
                    <a href="{{ route('deals.index', ['create' => 'deal']) }}" class="btn-secondary btn-sm"><x-icon name="plus" class="size-3.5" /> Deal</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($person->deals as $deal)
                        <li class="flex items-center justify-between gap-3 px-4 py-2.5">
                            <div class="min-w-0">
                                <a href="{{ route('deals.show', $deal) }}" class="block truncate text-sm font-medium hover:text-link">{{ $deal->title }}</a>
                                <p class="text-xs text-slate-500">{{ $deal->stage->label() }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium">{{ \App\Support\Money::format($deal->value, $deal->currency) }}</p>
                                @include('deals._status-badge', ['deal' => $deal])
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-slate-500">No deals yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="space-y-6 xl:col-span-2">
            @include('partials.activity-list', ['activities' => $person->activities])

            @include('partials.notes', ['notable' => $person, 'notableType' => 'person'])

            <div class="card">
                <div class="border-b border-slate-200 px-4 py-3"><h3 class="font-semibold">Emails</h3></div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($person->emails as $email)
                        <li>
                            <a href="{{ route('emails.show', $email) }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50">
                                <x-icon :name="$email->folder === 'sent' ? 'send' : 'inbox'" class="size-4 text-slate-500" />
                                <span class="min-w-0 flex-1 truncate text-sm">{{ $email->subject }}</span>
                                <span class="text-xs text-slate-500">{{ $email->created_at->diffForHumans() }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-slate-500">No emails linked to this person.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    @include('activities._create-modal', ['defaults' => ['person_id' => $person->id, 'organization_id' => $person->organization_id]])
</x-app-layout>
