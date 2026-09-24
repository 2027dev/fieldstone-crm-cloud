<?php

namespace App\Http\Controllers;

use App\Enums\LeadSource;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use App\Models\WebForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicWebFormController extends Controller
{
    public function show(string $slug): View
    {
        $webForm = $this->resolve($slug);

        return view('forms.public', ['webForm' => $webForm]);
    }

    /**
     * Capture a public submission as a lead in the form owner's inbox.
     */
    public function store(Request $request, string $slug): RedirectResponse
    {
        $webForm = $this->resolve($slug);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'website' => ['prohibited'],
        ]);

        DB::transaction(function () use ($webForm, $validated): void {
            $owner = ['user_id' => $webForm->user_id];

            $organization = filled($validated['company'] ?? null)
                ? Organization::withoutGlobalScopes()->firstOrCreate($owner + ['name' => $validated['company']])
                : null;

            $person = Person::withoutGlobalScopes()->firstOrCreate(
                $owner + ['email' => $validated['email']],
                ['name' => $validated['name'], 'phone' => $validated['phone'] ?? null, 'organization_id' => $organization?->id],
            );

            Lead::create($owner + [
                'title' => ($organization?->name ?? $person->name).' lead',
                'person_id' => $person->id,
                'organization_id' => $organization?->id,
                'source' => LeadSource::WebForm,
                'web_form_id' => $webForm->id,
                'message' => $validated['message'] ?? null,
            ]);

            WebForm::withoutGlobalScopes()->whereKey($webForm->id)->increment('submissions_count');
        });

        return back()->with('submitted', true);
    }

    /**
     * Public forms are looked up outside of the signed-in user's workspace scope.
     */
    private function resolve(string $slug): WebForm
    {
        return WebForm::withoutGlobalScopes()->where('slug', $slug)->where('is_active', true)->firstOrFail();
    }
}
