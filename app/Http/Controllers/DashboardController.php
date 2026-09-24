<?php

namespace App\Http\Controllers;

use App\Enums\ReportType;
use App\Models\Dashboard;
use App\Models\Report;
use App\Support\ReportBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Reports added to a freshly created dashboard so it is useful immediately.
     *
     * @var list<ReportType>
     */
    private const STARTER_REPORTS = [
        ReportType::DealsByStage,
        ReportType::DealsByStatus,
        ReportType::RevenueByMonth,
        ReportType::ActivitiesCompletion,
    ];

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'starter' => ['sometimes', 'boolean'],
        ]);

        $dashboard = DB::transaction(function () use ($validated, $request): Dashboard {
            $dashboard = Dashboard::create(['name' => $validated['name']]);

            if ($request->boolean('starter', true)) {
                foreach (self::STARTER_REPORTS as $position => $type) {
                    $dashboard->reports()->create(['name' => $type->label(), 'type' => $type, 'position' => $position]);
                }
            }

            return $dashboard;
        });

        return redirect()->route('dashboards.show', $dashboard)->with('status', 'Dashboard created.');
    }

    public function show(Dashboard $dashboard, ReportBuilder $builder): View
    {
        $dashboard->load('reports');

        return view('insights.dashboard', [
            'dashboard' => $dashboard,
            'dashboards' => Dashboard::oldest()->get(),
            'reports' => Report::whereNull('dashboard_id')->latest()->get(),
            'charts' => $dashboard->reports->mapWithKeys(fn (Report $report): array => [$report->id => $builder->build($report->type)]),
            'reportTypes' => ReportType::cases(),
        ]);
    }

    public function update(Request $request, Dashboard $dashboard): RedirectResponse
    {
        $dashboard->update($request->validate(['name' => ['required', 'string', 'max:255']]));

        return back()->with('status', 'Dashboard renamed.');
    }

    public function destroy(Dashboard $dashboard): RedirectResponse
    {
        $dashboard->delete();

        return redirect()->route('insights')->with('status', 'Dashboard deleted.');
    }
}
