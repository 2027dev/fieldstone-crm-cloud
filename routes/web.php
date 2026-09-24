<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\DemoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContactsTimelineController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DuplicateController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InsightsController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadFeatureController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PublicWebFormController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SampleDataController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SetupGuideController;
use App\Http\Controllers\WebFormController;
use Illuminate\Support\Facades\Route;

Route::get('/forms/{slug}', [PublicWebFormController::class, 'show'])->name('forms.public.show');
Route::post('/forms/{slug}', [PublicWebFormController::class, 'store'])->middleware('throttle:10,1')->name('forms.public.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/demo', DemoController::class)->middleware('throttle:10,1')->name('demo');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::redirect('/', '/setup');
    Route::get('/setup', SetupGuideController::class)->name('setup');
    Route::get('/search', SearchController::class)->name('search');

    Route::get('/contacts/timeline', ContactsTimelineController::class)->name('contacts.timeline');
    Route::get('/contacts/duplicates', [DuplicateController::class, 'index'])->name('contacts.duplicates');
    Route::post('/contacts/duplicates/merge', [DuplicateController::class, 'merge'])->name('contacts.duplicates.merge');
    Route::delete('/people/bulk', [PersonController::class, 'bulkDestroy'])->name('people.bulk-destroy');
    Route::resource('people', PersonController::class)->except(['create']);
    Route::resource('organizations', OrganizationController::class)->except(['create']);
    Route::post('/import/{type}', [ImportController::class, 'store'])->whereIn('type', ['people', 'leads'])->name('import');
    Route::delete('/sample-data', [SampleDataController::class, 'destroy'])->name('sample-data.destroy');
    Route::post('/sample-data', [SampleDataController::class, 'store'])->name('sample-data.store');

    Route::patch('/activities/bulk', [ActivityController::class, 'bulk'])->name('activities.bulk');
    Route::patch('/activities/{activity}/toggle', [ActivityController::class, 'toggle'])->name('activities.toggle');
    Route::resource('activities', ActivityController::class)->except(['create', 'show']);

    Route::patch('/deals/{deal}/move', [DealController::class, 'move'])->name('deals.move');
    Route::patch('/deals/{deal}/status', [DealController::class, 'status'])->name('deals.status');
    Route::resource('deals', DealController::class)->except(['create']);

    Route::get('/leads/web-forms', [WebFormController::class, 'index'])->name('web-forms.index');
    Route::post('/leads/web-forms', [WebFormController::class, 'store'])->name('web-forms.store');
    Route::put('/leads/web-forms/{webForm}', [WebFormController::class, 'update'])->name('web-forms.update');
    Route::delete('/leads/web-forms/{webForm}', [WebFormController::class, 'destroy'])->name('web-forms.destroy');
    Route::get('/leads/features/{feature}', LeadFeatureController::class)
        ->whereIn('feature', array_keys(LeadFeatureController::FEATURES))
        ->name('leads.feature');
    Route::patch('/leads/bulk', [LeadController::class, 'bulk'])->name('leads.bulk');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::patch('/leads/{lead}/archive', [LeadController::class, 'archive'])->name('leads.archive');
    Route::resource('leads', LeadController::class)->except(['create']);

    Route::get('/insights', [InsightsController::class, 'index'])->name('insights');
    Route::post('/insights/dashboards', [DashboardController::class, 'store'])->name('dashboards.store');
    Route::get('/insights/dashboards/{dashboard}', [DashboardController::class, 'show'])->name('dashboards.show');
    Route::put('/insights/dashboards/{dashboard}', [DashboardController::class, 'update'])->name('dashboards.update');
    Route::delete('/insights/dashboards/{dashboard}', [DashboardController::class, 'destroy'])->name('dashboards.destroy');
    Route::post('/insights/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/insights/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::delete('/insights/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

    Route::get('/inbox', [EmailController::class, 'index'])->name('inbox');
    Route::post('/inbox', [EmailController::class, 'store'])->name('emails.store');
    Route::get('/inbox/{email}', [EmailController::class, 'show'])->name('emails.show');
    Route::patch('/inbox/{email}', [EmailController::class, 'update'])->name('emails.update');
    Route::delete('/inbox/{email}', [EmailController::class, 'destroy'])->name('emails.destroy');

    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});
