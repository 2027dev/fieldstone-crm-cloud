<x-app-layout title="Sales Inbox" :breadcrumbs="$selected ? ['Sales Inbox' => route('inbox', ['folder' => $folder])] : []">
    <x-slot:subnav>
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'email')" class="btn-primary mb-4 w-full"><x-icon name="pencil" /> Compose</button>
        <nav class="space-y-0.5">
            @foreach ($folders as $key => $label)
                <a href="{{ route('inbox', ['folder' => $key]) }}" @class(['subnav-link', 'subnav-link-active' => $folder === $key])>
                    <x-icon :name="['inbox' => 'inbox', 'drafts' => 'note', 'sent' => 'send', 'archive' => 'archive'][$key]" class="size-5" />
                    <span class="flex-1">{{ $label }}</span>
                    @if ($key === 'inbox' && $unreadCount)<span class="rounded-full bg-brand-600 px-1.5 text-xs font-semibold text-white">{{ $unreadCount }}</span>@endif
                </a>
            @endforeach
        </nav>
        <div class="mt-auto rounded-lg border border-slate-200 bg-white p-3 text-xs text-slate-600">
            Emails are matched to contacts by address so every conversation shows up on the person's timeline.
        </div>
    </x-slot:subnav>

    <div class="flex min-h-0 flex-1">
        <div @class(['flex w-full flex-col border-r border-slate-200 md:w-96 md:shrink-0', 'hidden md:flex' => $selected])>
            <form method="get" action="{{ route('inbox') }}" class="border-b border-slate-200 p-3">
                <input type="hidden" name="folder" value="{{ $folder }}">
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" name="q" value="{{ $search }}" placeholder="Search {{ strtolower($folders[$folder]) }}" class="input py-1.5 pl-8">
                </div>
            </form>
            <ul class="flex-1 divide-y divide-slate-100 overflow-y-auto">
                @forelse ($emails as $email)
                    <li>
                        <a href="{{ route('emails.show', $email) }}" @class(['block px-4 py-3 hover:bg-slate-50', 'bg-brand-50/70' => $selected?->is($email)])>
                            <div class="flex items-center gap-2">
                                @if (! $email->read_at)<span class="size-2 shrink-0 rounded-full bg-brand-600" title="Unread"></span>@endif
                                <span @class(['flex-1 truncate text-sm', 'font-semibold text-slate-900' => ! $email->read_at, 'text-slate-700' => $email->read_at])>{{ $email->counterpart() }}</span>
                                @if ($email->is_starred)<x-icon name="star" class="size-3.5 fill-amber-400 text-amber-400" />@endif
                                <span class="shrink-0 text-xs text-slate-500">{{ $email->created_at->isToday() ? $email->created_at->format('g:i A') : $email->created_at->format('M j') }}</span>
                            </div>
                            <p @class(['mt-0.5 truncate text-sm', 'font-semibold text-slate-900' => ! $email->read_at, 'text-slate-800' => $email->read_at])>{{ $email->subject }}</p>
                            <p class="truncate text-xs text-slate-500">{{ Str::limit(str_replace("\n", ' ', $email->body), 90) }}</p>
                        </a>
                    </li>
                @empty
                    <li><x-empty-state icon="mail" title="{{ $search ? 'No emails match' : 'Nothing in '.strtolower($folders[$folder]) }}" /></li>
                @endforelse
            </ul>
            @if ($emails->hasPages())<div class="border-t border-slate-200 p-3">{{ $emails->links() }}</div>@endif
        </div>

        <div @class(['min-w-0 flex-1 overflow-y-auto', 'hidden md:block' => ! $selected])>
            @if ($selected)
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-5 py-3">
                    <a href="{{ route('inbox', ['folder' => $folder]) }}" class="btn-ghost px-2 md:hidden" aria-label="Back"><x-icon name="chevron-left" /></a>
                    <form method="post" action="{{ route('emails.update', $selected) }}">@csrf @method('patch')<input type="hidden" name="is_starred" value="{{ $selected->is_starred ? 0 : 1 }}"><button type="submit" class="btn-secondary"><x-icon name="star" @class(['fill-amber-400 text-amber-400' => $selected->is_starred]) /> {{ $selected->is_starred ? 'Starred' : 'Star' }}</button></form>
                    <form method="post" action="{{ route('emails.update', $selected) }}">@csrf @method('patch')<input type="hidden" name="unread" value="1"><button type="submit" class="btn-secondary">Mark unread</button></form>
                    @if ($selected->folder !== 'archive')
                        <form method="post" action="{{ route('emails.update', $selected) }}">@csrf @method('patch')<input type="hidden" name="folder" value="archive"><button type="submit" class="btn-secondary"><x-icon name="archive" /> Archive</button></form>
                    @else
                        <form method="post" action="{{ route('emails.update', $selected) }}">@csrf @method('patch')<input type="hidden" name="folder" value="inbox"><button type="submit" class="btn-secondary"><x-icon name="inbox" /> Move to inbox</button></form>
                    @endif
                    <form method="post" action="{{ route('emails.destroy', $selected) }}" onsubmit="return confirm('Delete this email?')">@csrf @method('delete')<button type="submit" class="btn-secondary text-red-600" aria-label="Delete"><x-icon name="trash" /></button></form>
                    @if (in_array($selected->folder, ['inbox', 'archive']))
                        <button type="button" x-data x-on:click="$dispatch('open-modal', 'email')" class="btn-primary ml-auto"><x-icon name="send" /> Reply</button>
                    @endif
                </div>
                <article class="px-6 py-5">
                    <h2 class="text-xl font-semibold">{{ $selected->subject }}</h2>
                    <div class="mt-4 flex items-center gap-3">
                        <x-avatar :initials="Str::upper(Str::substr(Str::replace('[Sample] ', '', $selected->from_name ?: $selected->from_email), 0, 1))" size="size-10" />
                        <div class="min-w-0 flex-1 text-sm">
                            <p><span class="font-semibold">{{ $selected->from_name }}</span> <span class="text-slate-500">&lt;{{ $selected->from_email }}&gt;</span></p>
                            <p class="text-slate-500">to {{ $selected->to_email }} · {{ $selected->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                    <div class="mt-6 text-[15px] leading-relaxed whitespace-pre-line text-slate-800">{{ $selected->body }}</div>
                </article>
                <div class="mx-6 mb-6 grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2">
                    <form method="post" action="{{ route('emails.update', $selected) }}" class="text-sm">
                        @csrf @method('patch')
                        <label class="label" for="link_person">Linked contact</label>
                        <div class="flex gap-2">
                            <select id="link_person" name="person_id" class="input py-1.5">
                                <option value="">None</option>
                                @foreach ($people as $person)
                                    <option value="{{ $person->id }}" @selected($selected->person_id === $person->id)>{{ $person->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-secondary">Link</button>
                        </div>
                        @if ($selected->person)<a href="{{ route('people.show', $selected->person) }}" class="mt-1 inline-block text-xs text-link hover:underline">Open {{ $selected->person->name }}</a>@endif
                    </form>
                    <form method="post" action="{{ route('emails.update', $selected) }}" class="text-sm">
                        @csrf @method('patch')
                        <label class="label" for="link_deal">Linked deal</label>
                        <div class="flex gap-2">
                            <select id="link_deal" name="deal_id" class="input py-1.5">
                                <option value="">None</option>
                                @foreach ($deals as $deal)
                                    <option value="{{ $deal->id }}" @selected($selected->deal_id === $deal->id)>{{ $deal->title }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-secondary">Link</button>
                        </div>
                        @if ($selected->deal)<a href="{{ route('deals.show', $selected->deal) }}" class="mt-1 inline-block text-xs text-link hover:underline">Open {{ $selected->deal->title }}</a>@endif
                    </form>
                </div>
            @else
                <x-empty-state icon="mail" title="Select an email to read" description="Keep every sales conversation in one place, linked to the right contact and deal.">
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'email')" class="btn-primary"><x-icon name="pencil" /> Compose</button>
                </x-empty-state>
            @endif
        </div>
    </div>

    @php
        $replying = $selected && in_array($selected->folder, ['inbox', 'archive']);
        $defaultTo = old('to_email', $replying ? $selected->from_email : request('to'));
        $defaultSubject = old('subject', $replying ? (Str::startsWith($selected->subject, 'Re:') ? $selected->subject : 'Re: '.$selected->subject) : null);
    @endphp
    <x-modal name="email" title="{{ $replying ? 'Reply' : 'New email' }}" width="max-w-2xl">
        <form method="post" action="{{ route('emails.store') }}">
            @csrf
            <input type="hidden" name="_modal" value="email">
            <div class="space-y-4 px-5 py-4">
                <div>
                    <label for="to_email" class="label">To <span class="text-red-600">*</span></label>
                    <input id="to_email" name="to_email" type="email" list="contact-emails" value="{{ $defaultTo }}" required class="input" placeholder="name@company.com">
                    <datalist id="contact-emails">
                        @foreach ($people as $person)<option value="{{ $person->email }}">{{ $person->name }}</option>@endforeach
                    </datalist>
                    @error('to_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <x-field label="Subject" name="subject" :value="$defaultSubject" required />
                <x-select label="Link to deal" name="deal_id" placeholder="None" :options="$deals->pluck('title', 'id')" :value="request('deal', $replying ? $selected->deal_id : null)" />
                <x-field label="Message" name="body" type="textarea" required />
            </div>
            <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-200 bg-slate-50 px-5 py-3">
                <button type="submit" name="draft" value="1" class="btn-secondary">Save draft</button>
                <button type="submit" class="btn-primary"><x-icon name="send" /> Send</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
