<?php

namespace App\Http\Controllers;

use App\Enums\ReportType;
use App\Models\Dashboard;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InsightsController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $dashboard = Dashboard::oldest()->first();

        if ($dashboard) {
            return redirect()->route('dashboards.show', $dashboard);
        }

        return view('insights.index', [
            'dashboards' => collect(),
            'reports' => Report::whereNull('dashboard_id')->latest()->get(),
            'reportTypes' => ReportType::cases(),
        ]);
    }
}
