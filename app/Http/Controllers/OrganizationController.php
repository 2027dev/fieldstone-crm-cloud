<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $organizations = Organization::query()
            ->withCount(['people', 'deals as open_deals_count' => fn ($query) => $query->where('status', 'open')])
            ->when($search !== '', fn ($query) => $query->whereLike('name', "%{$search}%"))
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        return view('organizations.index', ['organizations' => $organizations, 'search' => $search]);
    }

    public function store(OrganizationRequest $request): RedirectResponse
    {
        $organization = Organization::create($request->validated());

        return redirect()->route('organizations.show', $organization)->with('status', 'Organization added.');
    }

    public function show(Organization $organization): View
    {
        $organization->load([
            'people' => fn ($query) => $query->orderBy('name'),
            'deals' => fn ($query) => $query->with('person')->latest(),
            'activities' => fn ($query) => $query->with('person')->orderBy('done')->orderBy('due_date'),
            'notes',
        ]);

        return view('organizations.show', [
            'organization' => $organization,
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'deals' => Deal::open()->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function edit(Organization $organization): View
    {
        return view('organizations.edit', ['organization' => $organization]);
    }

    public function update(OrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $organization->update($request->validated());

        return redirect()->route('organizations.show', $organization)->with('status', 'Organization updated.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return redirect()->route('organizations.index')->with('status', 'Organization deleted.');
    }
}
