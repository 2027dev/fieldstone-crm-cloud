@php
    $classes = match ($deal->status) {
        \App\Enums\DealStatus::Won => 'bg-green-100 text-green-800',
        \App\Enums\DealStatus::Lost => 'bg-red-100 text-red-700',
        default => 'bg-brand-50 text-brand-700',
    };
@endphp
<span class="inline-block rounded px-1.5 py-0.5 text-xs font-semibold {{ $classes }}">{{ $deal->status->label() }}</span>
