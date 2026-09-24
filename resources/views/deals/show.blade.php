@php use App\Enums\DealStatus; use App\Support\Money; @endphp
<x-app-layout :title="$deal->title" :breadcrumbs="['Deals' => route('deals.index')]">
    <div class="border-b border-slate-200 px-6 py-5">
        <div class="flex flex-wrap items-start gap-4">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-3">
                    <h2 class="truncate text-2xl font-semibold">{{ $deal->title }}</h2>
                    @include('deals._status-badge')
                </div>
                <p class="mt-1 text-lg text-slate-700">{{ Money::format($deal->value, $deal->currency) }}
                    @if ($deal->person) · <a href="{{ route('people.show', $deal->person) }}" class="text-base text-link hover:underline">{{ $deal->person->name }}</a>@endif
                    @if ($deal->organization) · <a href="{{ route('organizations.show', $deal->organization) }}" class="text-base text-link hover:underline">{{ $deal->organization->name }}</a>@endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($deal->status === DealStatus::Open)
                    <form method="post" action="{{ route('deals.status', $deal) }}">
                        @csrf @method('patch')
                        <input type="hidden" name="status" value="won">
                        <button type="submit" class="btn-primary"><x-icon name="trophy" /> Won</button>
                    </form>
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'lost')" class="btn-danger">Lost</button>
                @else
                    <form method="post" action="{{ route('deals.status', $deal) }}">
                        @csrf @method('patch')
                        <input type="hidden" name="status" value="open">
                        <button type="submit" class="btn-secondary"><x-icon name="refresh" /> Reopen</button>
                    </form>
                @endif
                <a href="{{ route('deals.edit', $deal) }}" class="btn-secondary"><x-icon name="pencil" /> Edit</a>
                <form method="post" action="{{ route('deals.destroy', $deal) }}" onsubmit="return confirm('Delete this deal?')">
                    @csrf @method('delete')
                    <button type="submit" class="btn-secondary text-red-600" aria-label="Delete deal"><x-icon name="trash" /></button>
                </form>
            </div>
        </div>

        <div class="mt-5 flex overflow-hidden rounded-md">
            @php $reached = true; @endphp
            @foreach (\App\Enums\DealStage::cases() as $stage)
                <form method="post" action="{{ route('deals.move', $deal) }}" class="flex-1">
                    @csrf @method('patch')
                    <input type="hidden" name="stage" value="{{ $stage->value }}">
                    <button type="submit" @disabled($deal->status !== DealStatus::Open) title="Move to {{ $stage->label() }}"
                        @class([
                            'relative w-full truncate border-r-2 border-white px-3 py-2 text-xs font-semibold transition',
                            'bg-go-600 text-white hover:bg-go-700' => $reached,
                            'bg-slate-200 text-slate-600 hover:bg-slate-300' => ! $reached,
                        ])>{{ $stage->label() }}</button>
                </form>
                @php if ($stage === $deal->stage) { $reached = false; } @endphp
            @endforeach
        </div>
        @if ($deal->status === DealStatus::Lost && $deal->lost_reason)
            <p class="mt-3 text-sm text-red-700">Lost reason: {{ $deal->lost_reason }}</p>
        @endif
    </div>

    <div class="grid gap-6 p-6 xl:grid-cols-3">
        <div class="space-y-6">
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-3"><h3 class="font-semibold">Summary</h3></div>
                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-32 shrink-0 text-slate-500">Value</dt><dd class="font-medium">{{ Money::format($deal->value, $deal->currency) }}</dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-32 shrink-0 text-slate-500">Stage</dt><dd>{{ $deal->stage->label() }} <span class="text-slate-500">({{ $deal->stage->probability() }}%)</span></dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-32 shrink-0 text-slate-500">Expected close</dt><dd>{{ $deal->expected_close_date?->format('M j, Y') ?? '—' }}</dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-32 shrink-0 text-slate-500">Contact person</dt><dd>@if ($deal->person)<a href="{{ route('people.show', $deal->person) }}" class="text-link">{{ $deal->person->name }}</a>@else — @endif</dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-32 shrink-0 text-slate-500">Organization</dt><dd>@if ($deal->organization)<a href="{{ route('organizations.show', $deal->organization) }}" class="text-link">{{ $deal->organization->name }}</a>@else — @endif</dd></div>
                    <div class="flex gap-4 px-4 py-2.5"><dt class="w-32 shrink-0 text-slate-500">Created</dt><dd>{{ $deal->created_at->format('M j, Y') }}</dd></div>
                    @if ($deal->closed_at)
                        <div class="flex gap-4 px-4 py-2.5"><dt class="w-32 shrink-0 text-slate-500">Closed</dt><dd>{{ $deal->closed_at->format('M j, Y') }}</dd></div>
                    @endif
                </dl>
            </div>
            <div class="card">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <h3 class="font-semibold">Emails</h3>
                    @if ($deal->person?->email)
                        <a href="{{ route('inbox', ['create' => 'email', 'to' => $deal->person->email, 'deal' => $deal->id]) }}" class="btn-secondary btn-sm"><x-icon name="mail" class="size-3.5" /> Compose</a>
                    @endif
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($deal->emails as $email)
                        <li><a href="{{ route('emails.show', $email) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50"><x-icon :name="$email->folder === 'sent' ? 'send' : 'inbox'" class="size-4 text-slate-500" /><span class="truncate">{{ $email->subject }}</span></a></li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-slate-500">No emails linked.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="space-y-6 xl:col-span-2">
            @include('partials.activity-list', ['activities' => $deal->activities])
            @include('partials.notes', ['notable' => $deal, 'notableType' => 'deal'])
        </div>
    </div>

    @include('activities._create-modal', ['defaults' => ['deal_id' => $deal->id, 'person_id' => $deal->person_id, 'organization_id' => $deal->organization_id]])

    <x-modal name="lost" title="Mark as lost">
        <form method="post" action="{{ route('deals.status', $deal) }}">
            @csrf @method('patch')
            <input type="hidden" name="status" value="lost">
            <div class="px-5 py-4">
                <x-field label="Lost reason" name="lost_reason" placeholder="e.g. Went with a competitor" />
            </div>
            <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-200 bg-slate-50 px-5 py-3">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-danger">Mark as lost</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
