<?php

namespace App\Http\Controllers;

use App\Models\WebForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebFormController extends Controller
{
    public function index(): View
    {
        return view('leads.web-forms', [
            'webForms' => WebForm::withCount('leads')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        WebForm::create($this->validated($request));

        return back()->with('status', 'Web form created. Share its link to start collecting leads.');
    }

    public function update(Request $request, WebForm $webForm): RedirectResponse
    {
        $webForm->update($this->validated($request) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Web form updated.');
    }

    public function destroy(WebForm $webForm): RedirectResponse
    {
        $webForm->delete();

        return back()->with('status', 'Web form deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:255'],
            'button_label' => ['required', 'string', 'max:40'],
            'success_message' => ['required', 'string', 'max:255'],
        ]);
    }
}
