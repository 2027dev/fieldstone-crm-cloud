<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Email;
use App\Models\Person;
use App\Support\Owned;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailController extends Controller
{
    public function index(Request $request): View
    {
        $folder = array_key_exists($request->query('folder'), Email::FOLDERS) ? $request->query('folder') : 'inbox';
        $search = trim((string) $request->query('q'));

        return view('inbox.index', $this->sharedData() + [
            'folder' => $folder,
            'search' => $search,
            'emails' => Email::query()
                ->with('person')
                ->where('folder', $folder)
                ->when($search !== '', fn ($query) => $query->where(fn ($inner) => $inner
                    ->whereLike('subject', "%{$search}%")
                    ->orWhereLike('from_email', "%{$search}%")
                    ->orWhereLike('to_email', "%{$search}%")
                    ->orWhereLike('from_name', "%{$search}%")))
                ->latest()
                ->paginate(30)
                ->withQueryString(),
            'selected' => null,
        ]);
    }

    public function show(Email $email): View
    {
        if ($email->read_at === null) {
            $email->update(['read_at' => now()]);
        }

        $email->load(['person.organization', 'deal']);

        return view('inbox.index', $this->sharedData() + [
            'folder' => $email->folder,
            'search' => '',
            'emails' => Email::with('person')->where('folder', $email->folder)->latest()->paginate(30),
            'selected' => $email,
        ]);
    }

    /**
     * Compose a message. Messages are stored in the Sent folder (or Drafts) and linked to the matching contact.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'to_email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
            'deal_id' => ['nullable', Owned::exists('deals')],
            'draft' => ['sometimes', 'boolean'],
        ]);

        $user = $request->user();
        $person = Person::where('email', $validated['to_email'])->first();

        $email = Email::create([
            'folder' => $request->boolean('draft') ? 'drafts' : 'sent',
            'from_name' => $user->name,
            'from_email' => $user->email,
            'to_email' => $validated['to_email'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'person_id' => $person?->id,
            'deal_id' => $validated['deal_id'] ?? null,
            'read_at' => now(),
        ]);

        return redirect()->route('emails.show', $email)->with('status', $email->folder === 'drafts' ? 'Draft saved.' : 'Email sent.');
    }

    public function update(Request $request, Email $email): RedirectResponse
    {
        $validated = $request->validate([
            'folder' => ['sometimes', 'in:'.implode(',', array_keys(Email::FOLDERS))],
            'is_starred' => ['sometimes', 'boolean'],
            'unread' => ['sometimes', 'boolean'],
            'person_id' => ['sometimes', 'nullable', Owned::exists('people')],
            'deal_id' => ['sometimes', 'nullable', Owned::exists('deals')],
        ]);

        if (array_key_exists('unread', $validated)) {
            $validated['read_at'] = $validated['unread'] ? null : now();
            unset($validated['unread']);
        }

        $email->update($validated);

        if (isset($validated['folder'])) {
            return redirect()->route('inbox', ['folder' => 'inbox'])->with('status', 'Email moved to '.Email::FOLDERS[$validated['folder']].'.');
        }

        return back()->with('status', 'Email updated.');
    }

    public function destroy(Email $email): RedirectResponse
    {
        $folder = $email->folder;
        $email->delete();

        return redirect()->route('inbox', ['folder' => $folder])->with('status', 'Email deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function sharedData(): array
    {
        return [
            'folders' => Email::FOLDERS,
            'unreadCount' => Email::where('folder', 'inbox')->whereNull('read_at')->count(),
            'people' => Person::whereNotNull('email')->orderBy('name')->get(['id', 'name', 'email']),
            'deals' => Deal::open()->orderBy('title')->get(['id', 'title']),
        ];
    }
}
