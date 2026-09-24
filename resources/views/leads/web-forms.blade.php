<x-app-layout title="Web Forms" :breadcrumbs="['Leads' => route('leads.index')]" info="Public forms that drop submissions straight into your Leads Inbox">
    <x-slot:subnav>@include('leads._subnav')</x-slot:subnav>

    <div class="flex items-center gap-2 px-4 py-3">
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'web-form')" class="btn-primary"><x-icon name="plus" /> Web form</button>
    </div>

    <div class="grid gap-4 p-4 pt-0 lg:grid-cols-2">
        @forelse ($webForms as $webForm)
            <div class="card p-5" x-data="{ copied: false, editing: false }">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate font-semibold">{{ $webForm->name }}</h3>
                        <p class="text-sm text-slate-600">{{ $webForm->headline }}</p>
                    </div>
                    <span @class(['rounded-full px-2 py-0.5 text-xs font-semibold', 'bg-green-100 text-green-800' => $webForm->is_active, 'bg-slate-100 text-slate-600' => ! $webForm->is_active])>{{ $webForm->is_active ? 'Active' : 'Paused' }}</span>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <input type="text" readonly value="{{ route('forms.public.show', $webForm->slug) }}" class="input bg-slate-50 font-mono text-xs" x-ref="link" aria-label="Public link">
                    <button type="button" class="btn-secondary" x-on:click="navigator.clipboard.writeText($refs.link.value); copied = true; setTimeout(() => copied = false, 1500)">
                        <x-icon name="copy" /> <span x-text="copied ? 'Copied' : 'Copy'"></span>
                    </button>
                    <a href="{{ route('forms.public.show', $webForm->slug) }}" target="_blank" class="btn-secondary" aria-label="Open form"><x-icon name="external" /></a>
                </div>
                <div class="mt-4 flex items-center justify-between text-sm text-slate-600">
                    <span>{{ $webForm->submissions_count }} {{ Str::plural('submission', $webForm->submissions_count) }} · {{ $webForm->leads_count }} {{ Str::plural('lead', $webForm->leads_count) }}</span>
                    <div class="flex gap-1">
                        <button type="button" x-on:click="editing = ! editing" class="btn-ghost btn-sm"><x-icon name="pencil" class="size-3.5" /> Edit</button>
                        <form method="post" action="{{ route('web-forms.destroy', $webForm) }}" onsubmit="return confirm('Delete this web form?')">
                            @csrf @method('delete')
                            <button type="submit" class="btn-ghost btn-sm text-red-600"><x-icon name="trash" class="size-3.5" /> Delete</button>
                        </form>
                    </div>
                </div>
                <form x-show="editing" x-cloak method="post" action="{{ route('web-forms.update', $webForm) }}" class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                    @csrf @method('put')
                    <x-field label="Name" name="name" :value="$webForm->name" required />
                    <x-field label="Headline" name="headline" :value="$webForm->headline" />
                    <div class="grid gap-3 sm:grid-cols-2">
                        <x-field label="Button label" name="button_label" :value="$webForm->button_label" required />
                        <x-field label="Success message" name="success_message" :value="$webForm->success_message" required />
                    </div>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($webForm->is_active) class="rounded border-slate-300"> Accept submissions</label>
                    <div class="flex justify-end"><button type="submit" class="btn-primary btn-sm">Save</button></div>
                </form>
            </div>
        @empty
            <div class="lg:col-span-2">
                <x-empty-state icon="forms" title="Collect leads with web forms" description="Create a form, share the link or embed it on your website, and every submission lands in your Leads Inbox.">
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'web-form')" class="btn-primary"><x-icon name="plus" /> Web form</button>
                </x-empty-state>
            </div>
        @endforelse
    </div>

    <x-modal name="web-form" title="New web form">
        <form method="post" action="{{ route('web-forms.store') }}">
            @csrf
            <input type="hidden" name="_modal" value="web-form">
            <div class="space-y-4 px-5 py-4">
                <x-field label="Name" name="name" required placeholder="Website contact form" />
                <x-field label="Headline" name="headline" placeholder="Talk to our sales team" />
                <x-field label="Button label" name="button_label" value="Submit" required />
                <x-field label="Success message" name="success_message" value="Thanks! We will be in touch shortly." required />
            </div>
            <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-200 bg-slate-50 px-5 py-3">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Create form</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
