@props(['initials', 'size' => 'size-8'])
<span {{ $attributes->merge(['class' => "$size inline-flex shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700"]) }}>{{ $initials ?: '?' }}</span>
