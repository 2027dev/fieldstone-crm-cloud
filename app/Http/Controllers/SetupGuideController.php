<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\Person;
use App\Support\SetupProgress;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetupGuideController extends Controller
{
    public function __invoke(Request $request, SetupProgress $progress): View
    {
        return view('setup.index', [
            'tasks' => $progress->tasks(),
            'completed' => $progress->completedCount(),
            'total' => $progress->totalCount(),
            'stats' => [
                'people' => Person::count(),
                'openDeals' => Deal::open()->count(),
                'pendingActivities' => Activity::pending()->count(),
            ],
        ]);
    }
}
