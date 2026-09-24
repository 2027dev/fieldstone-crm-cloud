<x-app-layout title="Contacts timeline" :breadcrumbs="['Contacts' => route('people.index')]" info="Recent activities, deals, emails and notes across your contacts">
    <x-slot:subnav>@include('contacts._subnav')</x-slot:subnav>

    <div class="mx-auto w-full max-w-3xl p-6">
        @forelse ($groups as $day => $events)
            <h3 class="mt-6 mb-3 text-xs font-semibold tracking-wide text-slate-500 uppercase first:mt-0">{{ $day }}</h3>
            <ol class="relative space-y-3 border-l border-slate-200 pl-6">
                @foreach ($events as $event)
                    <li class="relative">
                        <span @class([
                            'absolute top-3 -left-[35px] flex size-6 items-center justify-center rounded-full ring-4 ring-white',
                            'bg-brand-100 text-brand-700' => $event['kind'] === 'activity',
                            'bg-green-100 text-green-700' => $event['kind'] === 'deal',
                            'bg-sky-100 text-sky-700' => $event['kind'] === 'email',
                            'bg-amber-100 text-amber-700' => $event['kind'] === 'note',
                        ])><x-icon :name="$event['icon']" class="size-3.5" /></span>
                        <a href="{{ $event['url'] }}" class="card flex items-center gap-3 px-4 py-3 hover:border-brand-200">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-900">{{ $event['title'] }}</p>
                                <p class="text-xs text-slate-500">{{ $event['meta'] }} · {{ $event['person']->name }}</p>
                            </div>
                            <span class="shrink-0 text-xs text-slate-500">{{ $event['at']->format('g:i A') }}</span>
                        </a>
                    </li>
                @endforeach
            </ol>
        @empty
            <x-empty-state icon="timeline" title="Nothing on the timeline yet" description="As you log activities, send emails and add notes for your contacts, they'll show up here." />
        @endforelse
    </div>
</x-app-layout>
