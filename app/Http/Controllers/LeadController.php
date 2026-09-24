<?php

namespace App\Http\Controllers;

use App\Enums\DealStage;
use App\Enums\LeadSource;
use App\Http\Requests\LeadRequest;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'archived' ? 'archived' : 'inbox';
        $search = trim((string) $request->query('q'));

        $leads = Lead::query()
            ->with(['person', 'organization', 'webForm'])
            ->when($tab === 'archived', fn ($query) => $query->archived(), fn ($query) => $query->inbox())
            ->when($search !== '', fn ($query) => $query->whereLike('title', "%{$search}%"))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('leads.index', [
            'leads' => $leads,
            'tab' => $tab,
            'search' => $search,
            'inboxCount' => Lead::inbox()->count(),
            'archivedCount' => Lead::archived()->count(),
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(LeadRequest $request): RedirectResponse
    {
        $attributes = $request->validated();
        $attributes['source'] ??= LeadSource::Manual->value;

        Lead::create($attributes);

        return redirect()->route('leads.index')->with('status', 'Lead added.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['person', 'organization', 'webForm', 'deal', 'activities', 'notes']);

        return view('leads.show', [
            'lead' => $lead,
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'deals' => Deal::open()->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function edit(Lead $lead): View
    {
        return view('leads.edit', [
            'lead' => $lead,
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(LeadRequest $request, Lead $lead): RedirectResponse
    {
        $lead->update($request->validated());

        return redirect()->route('leads.show', $lead)->with('status', 'Lead updated.');
    }

    /**
     * Turn a qualified lead into a deal at the start of the pipeline.
     */
    public function convert(Lead $lead): RedirectResponse
    {
        abort_if($lead->converted_at !== null, 409, 'This lead was already converted.');

        $deal = DB::transaction(function () use ($lead): Deal {
            $deal = Deal::create([
                'title' => $lead->title,
                'value' => $lead->value ?? 0,
                'currency' => $lead->currency,
                'stage' => DealStage::Qualified,
                'person_id' => $lead->person_id,
                'organization_id' => $lead->organization_id,
                'position' => Deal::where('stage', DealStage::Qualified)->max('position') + 1,
            ]);

            $lead->update(['deal_id' => $deal->id, 'converted_at' => now()]);
            $lead->activities()->update(['deal_id' => $deal->id]);

            return $deal;
        });

        return redirect()->route('deals.show', $deal)->with('status', 'Lead converted to a deal.');
    }

    public function archive(Lead $lead): RedirectResponse
    {
        $lead->update(['archived_at' => $lead->archived_at ? null : now()]);

        return back()->with('status', $lead->archived_at ? 'Lead archived.' : 'Lead moved back to the inbox.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
            'action' => ['required', 'in:archive,unarchive,delete'],
        ]);

        $query = Lead::whereIn('id', $validated['ids']);
        $count = match ($validated['action']) {
            'archive' => $query->update(['archived_at' => now()]),
            'unarchive' => $query->update(['archived_at' => null]),
            'delete' => $query->delete(),
        };

        return back()->with('status', trans_choice(':count lead updated.|:count leads updated.', $count));
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('leads.index')->with('status', 'Lead deleted.');
    }
}
