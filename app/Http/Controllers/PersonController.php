<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\Person;
use App\Support\SampleData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class PersonController extends Controller
{
    /**
     * Filters that can be applied to the people list.
     *
     * @var array<string, string>
     */
    public const FILTERS = [
        'all' => 'All people',
        'has_activities' => 'Total activities > 0',
        'open_deals' => 'With open deals',
        'no_organization' => 'Without organization',
    ];

    public function index(Request $request, SampleData $sampleData): View
    {
        $filter = array_key_exists($request->query('filter'), self::FILTERS) ? $request->query('filter') : 'all';
        $search = trim((string) $request->query('q'));

        $people = Person::query()
            ->with('organization')
            ->withCount(['activities', 'deals as closed_deals_count' => fn ($query) => $query->where('status', '!=', 'open')])
            ->when($search !== '', fn ($query) => $query->where(fn ($inner) => $inner
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('email', "%{$search}%")
                ->orWhereLike('phone', "%{$search}%")))
            ->when($filter === 'has_activities', fn ($query) => $query->has('activities'))
            ->when($filter === 'open_deals', fn ($query) => $query->whereHas('deals', fn ($deals) => $deals->where('status', 'open')))
            ->when($filter === 'no_organization', fn ($query) => $query->whereNull('organization_id'))
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        return view('people.index', [
            'people' => $people,
            'filter' => $filter,
            'search' => $search,
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
            'hasSampleData' => $sampleData->existsFor($request->user()),
        ]);
    }

    public function store(PersonRequest $request): RedirectResponse
    {
        $person = Person::create($this->attributes($request));

        return redirect()->route('people.show', $person)->with('status', 'Person added.');
    }

    public function show(Person $person): View
    {
        $person->load([
            'organization',
            'deals' => fn ($query) => $query->latest(),
            'activities' => fn ($query) => $query->with('deal')->orderBy('done')->orderBy('due_date'),
            'emails' => fn ($query) => $query->latest(),
            'notes',
        ]);

        return view('people.show', [
            'person' => $person,
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'deals' => Deal::open()->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function edit(Person $person): View
    {
        return view('people.edit', [
            'person' => $person,
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(PersonRequest $request, Person $person): RedirectResponse
    {
        $person->update($this->attributes($request));

        return redirect()->route('people.show', $person)->with('status', 'Person updated.');
    }

    public function destroy(Person $person): RedirectResponse
    {
        $person->delete();

        return redirect()->route('people.index')->with('status', 'Person deleted.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];

        $deleted = Person::whereIn('id', $ids)->delete();

        return back()->with('status', trans_choice(':count person deleted.|:count people deleted.', $deleted));
    }

    /**
     * Resolve the validated attributes, creating the organization on the fly when a new name is typed.
     *
     * @return array<string, mixed>
     */
    private function attributes(PersonRequest $request): array
    {
        $attributes = Arr::except($request->validated(), ['organization_name']);
        $organizationName = trim((string) $request->validated('organization_name'));

        if (empty($attributes['organization_id']) && $organizationName !== '') {
            $attributes['organization_id'] = Organization::firstOrCreate(['name' => $organizationName, 'user_id' => $request->user()->id])->id;
        }

        $attributes['email_label'] ??= 'Work';
        $attributes['phone_label'] ??= 'Work';

        return $attributes;
    }
}
