<?php

namespace App\Http\Controllers;

use App\Enums\DealStage;
use App\Enums\DealStatus;
use App\Http\Requests\DealRequest;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DealController extends Controller
{
    public function index(Request $request): View
    {
        $status = DealStatus::tryFrom((string) $request->query('status')) ?? DealStatus::Open;
        $view = $request->query('view') === 'list' || $status !== DealStatus::Open ? 'list' : 'pipeline';

        $deals = Deal::query()
            ->with(['person', 'organization'])
            ->withCount(['activities as pending_activities_count' => fn ($query) => $query->where('done', false)])
            ->where('status', $status)
            ->orderBy('position')
            ->latest('updated_at')
            ->get();

        return view('deals.index', [
            'view' => $view,
            'status' => $status,
            'deals' => $deals,
            'columns' => collect(DealStage::cases())->map(fn (DealStage $stage): array => [
                'stage' => $stage,
                'deals' => $deals->where('stage', $stage)->values(),
            ]),
            'people' => Person::orderBy('name')->get(['id', 'name', 'organization_id']),
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(DealRequest $request): RedirectResponse
    {
        $attributes = $request->validated();
        $attributes['position'] = Deal::where('stage', $attributes['stage'])->max('position') + 1;
        $attributes['value'] ??= 0;

        if (empty($attributes['organization_id']) && ! empty($attributes['person_id'])) {
            $attributes['organization_id'] = Person::find($attributes['person_id'])?->organization_id;
        }

        Deal::create($attributes);

        return redirect()->route('deals.index')->with('status', 'Deal added.');
    }

    public function show(Deal $deal): View
    {
        $deal->load([
            'person',
            'organization',
            'activities' => fn ($query) => $query->orderBy('done')->orderBy('due_date'),
            'emails' => fn ($query) => $query->latest(),
            'notes',
        ]);

        return view('deals.show', [
            'deal' => $deal,
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'deals' => Deal::open()->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function edit(Deal $deal): View
    {
        return view('deals.edit', [
            'deal' => $deal,
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(DealRequest $request, Deal $deal): RedirectResponse
    {
        $attributes = $request->validated();
        $attributes['value'] ??= 0;

        $deal->update($attributes);

        return redirect()->route('deals.show', $deal)->with('status', 'Deal updated.');
    }

    /**
     * Move a deal to another stage from the pipeline board, re-sequencing the target column.
     */
    public function move(Request $request, Deal $deal): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'stage' => ['required', Rule::enum(DealStage::class)],
            'order' => ['array'],
            'order.*' => ['integer'],
        ]);

        DB::transaction(function () use ($deal, $validated): void {
            $deal->update(['stage' => $validated['stage']]);

            foreach (array_values($validated['order'] ?? []) as $position => $id) {
                Deal::whereKey($id)->update(['position' => $position]);
            }
        });

        if (! $request->wantsJson()) {
            return back()->with('status', 'Deal moved to '.DealStage::from($validated['stage'])->label().'.');
        }

        return response()->json(['ok' => true]);
    }

    public function status(Request $request, Deal $deal): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(DealStatus::class)],
            'lost_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $status = DealStatus::from($validated['status']);

        $deal->update([
            'status' => $status,
            'closed_at' => $status === DealStatus::Open ? null : now(),
            'lost_reason' => $status === DealStatus::Lost ? ($validated['lost_reason'] ?? null) : null,
        ]);

        return back()->with('status', match ($status) {
            DealStatus::Won => 'Deal marked as won. 🎉',
            DealStatus::Lost => 'Deal marked as lost.',
            DealStatus::Open => 'Deal reopened.',
        });
    }

    public function destroy(Deal $deal): RedirectResponse
    {
        $deal->delete();

        return redirect()->route('deals.index')->with('status', 'Deal deleted.');
    }
}
