<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Http\Requests\ActivityRequest;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Person;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Period filters shown above the activities list.
     *
     * @var array<string, string>
     */
    public const PERIODS = [
        'todo' => 'To-do',
        'overdue' => 'Overdue',
        'today' => 'Today',
        'tomorrow' => 'Tomorrow',
        'this_week' => 'This week',
        'next_week' => 'Next week',
        'done' => 'Done',
        'all' => 'All',
    ];

    public function index(Request $request): View
    {
        $view = $request->query('view') === 'calendar' ? 'calendar' : 'list';
        $type = ActivityType::tryFrom((string) $request->query('type'));
        $period = array_key_exists($request->query('period'), self::PERIODS) ? $request->query('period') : 'todo';

        $data = [
            'view' => $view,
            'type' => $type,
            'period' => $period,
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'deals' => Deal::open()->orderBy('title')->get(['id', 'title']),
        ];

        if ($view === 'calendar') {
            return view('activities.index', $data + $this->calendar($request, $type));
        }

        $activities = Activity::query()
            ->with(['person', 'deal'])
            ->when($type, fn (Builder $query) => $query->where('type', $type))
            ->tap(fn (Builder $query) => $this->applyPeriod($query, $period))
            ->orderByRaw('case when due_date is null then 1 else 0 end')
            ->orderBy('due_date')
            ->orderBy('due_time')
            ->paginate(50)
            ->withQueryString();

        return view('activities.index', $data + ['activities' => $activities]);
    }

    public function store(ActivityRequest $request): RedirectResponse
    {
        $activity = Activity::create($this->withDealContext($request->validated()));

        if ($activity->done) {
            $activity->update(['done_at' => now()]);
        }

        return back()->with('status', 'Activity scheduled.');
    }

    public function edit(Activity $activity): View
    {
        return view('activities.edit', [
            'activity' => $activity,
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'deals' => Deal::orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function update(ActivityRequest $request, Activity $activity): RedirectResponse
    {
        $attributes = $this->withDealContext($request->validated());
        $attributes['done'] = $request->boolean('done');
        $attributes['done_at'] = $attributes['done'] ? ($activity->done_at ?? now()) : null;

        $activity->update($attributes);

        return redirect()->route('activities.index')->with('status', 'Activity updated.');
    }

    public function toggle(Request $request, Activity $activity): JsonResponse|RedirectResponse
    {
        $activity->update([
            'done' => ! $activity->done,
            'done_at' => $activity->done ? null : now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['done' => $activity->done]);
        }

        return back()->with('status', $activity->done ? 'Activity marked as done.' : 'Activity reopened.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
            'action' => ['required', 'in:done,delete'],
        ]);

        $query = Activity::whereIn('id', $validated['ids']);
        $count = $validated['action'] === 'delete'
            ? $query->delete()
            : $query->update(['done' => true, 'done_at' => now()]);

        return back()->with('status', trans_choice(':count activity updated.|:count activities updated.', $count));
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $cameFromEditPage = url()->previous() === route('activities.edit', $activity);
        $activity->delete();

        return ($cameFromEditPage ? redirect()->route('activities.index') : back())->with('status', 'Activity deleted.');
    }

    private function applyPeriod(Builder $query, string $period): void
    {
        $today = CarbonImmutable::today();

        match ($period) {
            'todo' => $query->where('done', false),
            'overdue' => $query->where('done', false)->whereDate('due_date', '<', $today),
            'today' => $query->whereDate('due_date', $today),
            'tomorrow' => $query->whereDate('due_date', $today->addDay()),
            'this_week' => $query->whereBetween('due_date', [$today->startOfWeek()->toDateString(), $today->endOfWeek()->toDateString()]),
            'next_week' => $query->whereBetween('due_date', [$today->addWeek()->startOfWeek()->toDateString(), $today->addWeek()->endOfWeek()->toDateString()]),
            'done' => $query->where('done', true),
            default => null,
        };
    }

    /**
     * @return array{month: CarbonImmutable, weeks: list<list<CarbonImmutable>>, byDay: Collection<string, Collection<int, Activity>>}
     */
    private function calendar(Request $request, ?ActivityType $type): array
    {
        $month = rescue(fn () => CarbonImmutable::createFromFormat('Y-m', (string) $request->query('month'))->startOfMonth(), null, false)
            ?? CarbonImmutable::today()->startOfMonth();

        $start = $month->startOfWeek();
        $end = $month->endOfMonth()->endOfWeek();

        $byDay = Activity::query()
            ->with('person')
            ->when($type, fn (Builder $query) => $query->where('type', $type))
            ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('due_time')
            ->get()
            ->groupBy(fn (Activity $activity): string => $activity->due_date->toDateString());

        $weeks = [];
        for ($day = $start; $day->lte($end); $day = $day->addWeek()) {
            $weeks[] = collect(range(0, 6))->map(fn (int $offset): CarbonImmutable => $day->addDays($offset))->all();
        }

        return ['month' => $month, 'weeks' => $weeks, 'byDay' => $byDay];
    }

    /**
     * Fill in the contact and organization from the linked deal when they were left blank.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function withDealContext(array $attributes): array
    {
        if (! empty($attributes['deal_id']) && ($deal = Deal::find($attributes['deal_id']))) {
            $attributes['person_id'] = $attributes['person_id'] ?? $deal->person_id;
            $attributes['organization_id'] = $attributes['organization_id'] ?? $deal->organization_id;
        }

        return $attributes;
    }
}
