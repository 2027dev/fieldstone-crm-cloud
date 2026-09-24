<x-app-layout title="Setup guide">
    <section class="relative overflow-hidden border-b border-slate-100 bg-brand-50/70">
        <div class="mx-auto flex max-w-6xl items-center gap-10 px-8 py-12">
            <div class="max-w-xl flex-1">
                <h2 class="text-3xl font-medium text-slate-900">Hi, <span class="text-go-600">{{ auth()->user()->name }}</span>! Let's get you set up</h2>
                <p class="mt-4 text-lg leading-relaxed text-slate-800">
                    Connect <span class="text-go-600">{{ auth()->user()->company_name ?: 'your team' }}</span> with Fieldstone. Follow these milestones to customize your workspace.
                </p>
                <div class="mt-8">
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-brand-600 transition-all" style="width: {{ round($completed / $total * 100) }}%"></div>
                    </div>
                    <p class="mt-2 text-right text-xs font-semibold tracking-wide text-slate-600 uppercase">{{ $completed }}/{{ $total }} suggested tasks completed</p>
                </div>
            </div>
            <div class="relative hidden h-56 w-80 shrink-0 lg:block" aria-hidden="true">
                <div class="absolute inset-x-0 top-0 h-44 rounded-lg bg-brand-200 shadow-inner"></div>
                <div class="absolute bottom-0 left-1/2 h-12 w-16 -translate-x-1/2 bg-brand-500"></div>
                <div class="absolute top-0 inset-x-0 h-44 rounded-lg border-b-[14px] border-brand-200/80"></div>
                <x-icon name="star" class="absolute top-8 left-6 size-16 fill-green-500 text-green-500" />
                <x-icon name="star" class="absolute top-14 left-16 size-12 text-white/80" />
                <div class="absolute top-16 left-36 size-14 rounded-full bg-white"></div>
                <div class="absolute top-20 right-6 h-14 w-14 bg-white"></div>
                <div class="absolute -top-2 right-10 h-16 w-8 rounded-full bg-navy-700"></div>
            </div>
        </div>
    </section>

    <div class="mx-auto w-full max-w-5xl space-y-8 px-6 py-10">
        <div class="card flex items-start gap-4 px-5 py-4">
            <x-icon name="check-circle" class="mt-0.5 size-5 text-brand-600" />
            <div>
                <p class="font-medium text-slate-500 line-through">Set up account</p>
                <p class="text-sm text-slate-500 line-through">You've successfully joined Fieldstone! Continue by customizing your account to your needs.</p>
            </div>
        </div>

        @php $basicsDone = collect($tasks)->where('done', true)->count(); @endphp
        <div>
            <h3 class="mb-3 text-sm font-semibold tracking-wide text-slate-700 uppercase">Your to-do list</h3>
            <div x-data="{ open: true }" class="card divide-y divide-slate-200">
                <button type="button" x-on:click="open = ! open" class="flex w-full items-center gap-4 px-5 py-4 text-left">
                    @if ($basicsDone === count($tasks))
                        <x-icon name="check-circle" class="size-5 text-brand-600" />
                    @else
                        <span class="size-5 rounded-full border-2 border-slate-200"></span>
                    @endif
                    <span class="flex-1 font-semibold text-slate-900">Cover the basics</span>
                    <span class="hidden h-1.5 w-20 overflow-hidden rounded-full bg-slate-200 sm:block">
                        <span class="block h-full bg-brand-600" style="width: {{ round($basicsDone / count($tasks) * 100) }}%"></span>
                    </span>
                    <span class="text-sm text-slate-600">{{ $basicsDone }} of {{ count($tasks) }} tasks</span>
                    <x-icon name="chevron-up" class="size-4 text-slate-600 transition" x-bind:class="! open && 'rotate-180'" />
                </button>
                @foreach ($tasks as $task)
                    <div x-show="open" class="flex items-start gap-4 px-5 py-5">
                        @if ($task['done'])
                            <x-icon name="check-circle" class="mt-0.5 size-5 text-brand-600" />
                        @else
                            <x-icon :name="$task['icon']" class="mt-0.5 size-5 text-slate-500" />
                        @endif
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p @class(['font-semibold', 'text-slate-500 line-through' => $task['done'], 'text-slate-900' => ! $task['done']])>{{ $task['title'] }}</p>
                                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-xs font-medium text-slate-700"><x-icon name="clock" class="size-3" /> {{ $task['duration'] }}</span>
                            </div>
                            <p class="mt-0.5 text-sm text-slate-600">{{ $task['description'] }}</p>
                            @unless ($task['done'])
                                <a href="{{ $task['url'] }}" class="btn-primary btn-sm mt-3">{{ $task['action'] }}</a>
                            @else
                                <p class="mt-2 text-sm font-medium text-go-600">Done — nice work!</p>
                            @endunless
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <a href="{{ route('people.index') }}" class="card flex items-center gap-4 p-5 hover:border-brand-200">
                <span class="flex size-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="contacts" class="size-5" /></span>
                <span><span class="block text-2xl font-semibold">{{ $stats['people'] }}</span><span class="text-sm text-slate-600">People</span></span>
            </a>
            <a href="{{ route('deals.index') }}" class="card flex items-center gap-4 p-5 hover:border-brand-200">
                <span class="flex size-10 items-center justify-center rounded-lg bg-go-50 text-go-600"><x-icon name="dollar" class="size-5" /></span>
                <span><span class="block text-2xl font-semibold">{{ $stats['openDeals'] }}</span><span class="text-sm text-slate-600">Open deals</span></span>
            </a>
            <a href="{{ route('activities.index') }}" class="card flex items-center gap-4 p-5 hover:border-brand-200">
                <span class="flex size-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600"><x-icon name="calendar" class="size-5" /></span>
                <span><span class="block text-2xl font-semibold">{{ $stats['pendingActivities'] }}</span><span class="text-sm text-slate-600">Activities to do</span></span>
            </a>
        </div>
    </div>
</x-app-layout>
