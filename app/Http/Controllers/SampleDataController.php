<?php

namespace App\Http\Controllers;

use App\Support\SampleData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SampleDataController extends Controller
{
    public function store(Request $request, SampleData $sampleData): RedirectResponse
    {
        if (! $sampleData->existsFor($request->user())) {
            $sampleData->seedFor($request->user());
        }

        return back()->with('status', 'Sample data added.');
    }

    public function destroy(Request $request, SampleData $sampleData): RedirectResponse
    {
        $sampleData->removeFor($request->user());

        return back()->with('status', 'Sample data removed.');
    }
}
