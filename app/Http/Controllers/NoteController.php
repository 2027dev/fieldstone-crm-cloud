<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Record types that notes can be attached to.
     *
     * @var array<string, class-string<Model>>
     */
    private const NOTABLE = [
        'person' => Person::class,
        'organization' => Organization::class,
        'deal' => Deal::class,
        'lead' => Lead::class,
    ];

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'notable_type' => ['required', 'in:'.implode(',', array_keys(self::NOTABLE))],
            'notable_id' => ['required', 'integer'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $notable = self::NOTABLE[$validated['notable_type']]::findOrFail($validated['notable_id']);
        $notable->notes()->create(['body' => $validated['body']]);

        return back()->with('status', 'Note added.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        $note->delete();

        return back()->with('status', 'Note deleted.');
    }
}
