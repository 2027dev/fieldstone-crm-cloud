<?php

namespace App\Http\Controllers;

use App\Enums\ReportType;
use App\Models\Dashboard;
use App\Models\Report;
use App\Support\Owned;
use App\Support\ReportBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(ReportType::class)],
            'name' => ['nullable', 'string', 'max:255'],
            'dashboard_id' => ['nullable', Owned::exists('dashboards')],
        ]);

        $type = ReportType::from($validated['type']);
        $dashboardId = $validated['dashboard_id'] ?? null;

        $report = Report::create([
            'type' => $type,
            'name' => $validated['name'] ?: $type->label(),
            'dashboard_id' => $dashboardId,
            'position' => $dashboardId ? Report::where('dashboard_id', $dashboardId)->max('position') + 1 : 0,
        ]);

        return $dashboardId
            ? redirect()->route('dashboards.show', $dashboardId)->with('status', 'Report added to dashboard.')
            : redirect()->route('reports.show', $report)->with('status', 'Report created.');
    }

    public function show(Report $report, ReportBuilder $builder): View
    {
        return view('insights.report', [
            'report' => $report,
            'chart' => $builder->build($report->type),
            'dashboards' => Dashboard::oldest()->get(),
            'reports' => Report::whereNull('dashboard_id')->latest()->get(),
            'reportTypes' => ReportType::cases(),
        ]);
    }

    public function destroy(Report $report): RedirectResponse
    {
        $dashboardId = $report->dashboard_id;
        $report->delete();

        return $dashboardId
            ? redirect()->route('dashboards.show', $dashboardId)->with('status', 'Report removed.')
            : redirect()->route('insights')->with('status', 'Report deleted.');
    }
}
