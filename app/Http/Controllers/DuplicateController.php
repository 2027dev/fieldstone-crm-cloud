<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\Email;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Person;
use App\Support\Owned;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DuplicateController extends Controller
{
    public function index(): View
    {
        return view('contacts.duplicates', ['groups' => $this->duplicateGroups()]);
    }

    /**
     * Merge the selected duplicates into the chosen primary person, moving all related records.
     */
    public function merge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'primary_id' => ['required', Owned::exists('people')],
            'ids' => ['required', 'array', 'min:2'],
            'ids.*' => [Owned::exists('people')],
        ]);

        $primary = Person::findOrFail($validated['primary_id']);
        $duplicateIds = collect($validated['ids'])->map(fn ($id): int => (int) $id)->reject(fn (int $id): bool => $id === $primary->id)->values();

        DB::transaction(function () use ($primary, $duplicateIds): void {
            $duplicates = Person::whereIn('id', $duplicateIds)->get();

            $primary->fill([
                'email' => $primary->email ?: $duplicates->pluck('email')->filter()->first(),
                'phone' => $primary->phone ?: $duplicates->pluck('phone')->filter()->first(),
                'job_title' => $primary->job_title ?: $duplicates->pluck('job_title')->filter()->first(),
                'organization_id' => $primary->organization_id ?: $duplicates->pluck('organization_id')->filter()->first(),
            ])->save();

            foreach ([Activity::class, Deal::class, Email::class, Lead::class] as $model) {
                $model::whereIn('person_id', $duplicateIds)->update(['person_id' => $primary->id]);
            }

            Note::where('notable_type', Person::class)->whereIn('notable_id', $duplicateIds)->update(['notable_id' => $primary->id]);
            Person::whereIn('id', $duplicateIds)->delete();
        });

        return redirect()->route('contacts.duplicates')->with('status', 'Contacts merged into '.$primary->name.'.');
    }

    /**
     * Group people who share an email address or a normalized name.
     *
     * @return Collection<int, Collection<int, Person>>
     */
    private function duplicateGroups(): Collection
    {
        $people = Person::with('organization')->withCount(['deals', 'activities'])->orderBy('created_at')->get();
        $normalize = fn (string $value): string => Str::of($value)->replace('[Sample]', '')->lower()->squish()->toString();

        $byEmail = $people->filter(fn (Person $person): bool => filled($person->email))->groupBy(fn (Person $person): string => 'email:'.Str::lower($person->email));
        $byName = $people->groupBy(fn (Person $person): string => 'name:'.$normalize($person->name));

        $seen = [];

        return $byEmail->toBase()->merge($byName->toBase())
            ->filter(fn (Collection $group): bool => $group->count() > 1)
            ->map(fn (Collection $group): Collection => $group->values())
            ->filter(function (Collection $group) use (&$seen): bool {
                $signature = $group->pluck('id')->sort()->implode('-');

                if (isset($seen[$signature])) {
                    return false;
                }

                return $seen[$signature] = true;
            })
            ->values();
    }
}
