{{-- Expects $notable (model) and $notableType (person|organization|deal|lead) --}}
<div class="card">
    <div class="border-b border-slate-200 px-4 py-3"><h3 class="font-semibold">Notes</h3></div>
    <form method="post" action="{{ route('notes.store') }}" class="border-b border-slate-100 p-4">
        @csrf
        <input type="hidden" name="notable_type" value="{{ $notableType }}">
        <input type="hidden" name="notable_id" value="{{ $notable->id }}">
        <textarea name="body" rows="3" required class="input bg-amber-50/60" placeholder="Take a note…"></textarea>
        @error('body')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        <div class="mt-2 flex justify-end"><button type="submit" class="btn-primary btn-sm">Save note</button></div>
    </form>
    <ul class="divide-y divide-slate-100">
        @forelse ($notable->notes as $note)
            <li class="group flex gap-3 px-4 py-3">
                <x-icon name="note" class="mt-0.5 size-4 shrink-0 text-amber-500" />
                <div class="min-w-0 flex-1">
                    <p class="text-sm whitespace-pre-line text-slate-800">{{ $note->body }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $note->created_at->diffForHumans() }}</p>
                </div>
                <form method="post" action="{{ route('notes.destroy', $note) }}" onsubmit="return confirm('Delete this note?')" class="opacity-0 group-hover:opacity-100">
                    @csrf @method('delete')
                    <button type="submit" class="text-slate-400 hover:text-red-600" aria-label="Delete note"><x-icon name="trash" /></button>
                </form>
            </li>
        @empty
            <li class="px-4 py-6 text-center text-sm text-slate-500">No notes yet.</li>
        @endforelse
    </ul>
</div>
