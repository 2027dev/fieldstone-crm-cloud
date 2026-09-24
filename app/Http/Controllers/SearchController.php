<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View|JsonResponse
    {
        $term = trim((string) $request->query('q'));
        $limit = $request->wantsJson() ? 5 : 25;

        $results = $term === '' ? collect() : collect([
            'People' => Person::with('organization')
                ->where(fn ($query) => $query->whereLike('name', "%{$term}%")->orWhereLike('email', "%{$term}%")->orWhereLike('phone', "%{$term}%"))
                ->limit($limit)->get()
                ->map(fn (Person $person): array => ['title' => $person->name, 'subtitle' => $person->organization?->name ?? $person->email, 'url' => route('people.show', $person), 'icon' => 'user']),
            'Organizations' => Organization::whereLike('name', "%{$term}%")->limit($limit)->get()
                ->map(fn (Organization $organization): array => ['title' => $organization->name, 'subtitle' => $organization->address, 'url' => route('organizations.show', $organization), 'icon' => 'building']),
            'Deals' => Deal::with('organization')->whereLike('title', "%{$term}%")->limit($limit)->get()
                ->map(fn (Deal $deal): array => ['title' => $deal->title, 'subtitle' => $deal->stage->label().' · '.$deal->status->label(), 'url' => route('deals.show', $deal), 'icon' => 'dollar']),
            'Leads' => Lead::whereLike('title', "%{$term}%")->limit($limit)->get()
                ->map(fn (Lead $lead): array => ['title' => $lead->title, 'subtitle' => $lead->source->label(), 'url' => route('leads.show', $lead), 'icon' => 'target']),
        ])->filter(fn ($group) => $group->isNotEmpty());

        if ($request->wantsJson()) {
            return response()->json($results);
        }

        return view('search.index', ['term' => $term, 'results' => $results]);
    }
}
