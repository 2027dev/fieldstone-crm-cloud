@php $features = \App\Http\Controllers\LeadFeatureController::FEATURES; @endphp
<nav class="space-y-0.5">
    <a href="{{ route('leads.index') }}" @class(['subnav-link', 'subnav-link-active' => request()->routeIs('leads.index', 'leads.show', 'leads.edit')])><x-icon name="inbox" class="size-5" /> Leads Inbox</a>
</nav>
<p class="mt-5 mb-1 px-3 text-xs font-semibold tracking-wide text-slate-500 uppercase">LeadBooster</p>
<nav class="space-y-0.5">
    <a href="{{ route('leads.feature', 'live-chat') }}" @class(['subnav-link', 'subnav-link-active' => request()->is('leads/features/live-chat')])><x-icon name="chat" class="size-5" /> Live Chat</a>
    <a href="{{ route('leads.feature', 'chatbot') }}" @class(['subnav-link', 'subnav-link-active' => request()->is('leads/features/chatbot')])><x-icon name="bot" class="size-5" /> Chatbot</a>
    <a href="{{ route('web-forms.index') }}" @class(['subnav-link', 'subnav-link-active' => request()->routeIs('web-forms.*')])><x-icon name="forms" class="size-5" /> Web Forms</a>
    <a href="{{ route('leads.feature', 'prospector') }}" @class(['subnav-link', 'subnav-link-active' => request()->is('leads/features/prospector')])><x-icon name="binoculars" class="size-5" /> Prospector</a>
</nav>
<p class="mt-5 mb-1 px-3 text-xs font-semibold tracking-wide text-slate-500 uppercase">Add-ons</p>
<nav class="space-y-0.5">
    <a href="{{ route('leads.feature', 'web-visitors') }}" @class(['subnav-link', 'subnav-link-active' => request()->is('leads/features/web-visitors')])><x-icon name="radar" class="size-5" /> Web Visitors</a>
</nav>
<p class="mt-5 mb-1 px-3 text-xs font-semibold tracking-wide text-slate-500 uppercase">Integrations</p>
<nav class="space-y-0.5">
    <a href="{{ route('leads.feature', 'linkedin') }}" @class(['subnav-link', 'subnav-link-active' => request()->is('leads/features/linkedin')])>
        <x-icon name="linkedin" class="size-5" /> <span class="flex-1">LinkedIn</span>
        <span class="rounded-full bg-link px-1.5 py-0.5 text-[10px] font-bold text-white">NEW</span>
    </a>
</nav>
