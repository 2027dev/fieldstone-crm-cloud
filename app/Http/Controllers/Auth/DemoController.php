<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\SampleData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DemoController extends Controller
{
    /**
     * Spin up a private demo workspace pre-filled with sample data and sign the visitor in.
     */
    public function __invoke(Request $request, SampleData $sampleData): RedirectResponse
    {
        $user = User::create([
            'name' => 'Demo User',
            'company_name' => 'Fieldstone Demo Co.',
            'email' => 'demo+'.Str::lower(Str::random(12)).'@fieldstone.test',
            'password' => Str::random(40),
        ]);

        $sampleData->seedFor($user);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('setup');
    }
}
