<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Dashboard;
use App\Models\Deal;
use App\Models\Email;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Report;
use App\Models\User;
use App\Models\WebForm;
use App\Support\SampleData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CrmPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['company_name' => 'Agent Solutions']);
        app(SampleData::class)->seedFor($this->user);
        $this->actingAs($this->user);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function indexPages(): array
    {
        return [
            'setup' => ['/setup'],
            'people' => ['/people'],
            'people filtered' => ['/people?filter=has_activities'],
            'people search' => ['/people?q=tony'],
            'organizations' => ['/organizations'],
            'timeline' => ['/contacts/timeline'],
            'duplicates' => ['/contacts/duplicates'],
            'activities' => ['/activities'],
            'activities overdue' => ['/activities?period=overdue&type=email'],
            'activities calendar' => ['/activities?view=calendar'],
            'activities next month' => ['/activities?view=calendar&month=2030-02'],
            'deals pipeline' => ['/deals'],
            'deals won list' => ['/deals?status=won'],
            'leads' => ['/leads'],
            'leads archived' => ['/leads?tab=archived'],
            'web forms' => ['/leads/web-forms'],
            'lead feature' => ['/leads/features/live-chat'],
            'inbox' => ['/inbox'],
            'inbox sent' => ['/inbox?folder=sent&q=follow'],
            'search' => ['/search?q=sample'],
            'settings' => ['/settings'],
        ];
    }

    #[DataProvider('indexPages')]
    public function test_index_pages_render(string $url): void
    {
        $this->get($url)->assertOk();
    }

    public function test_detail_and_edit_pages_render(): void
    {
        $person = Person::first();
        $organization = Organization::first();
        $deal = Deal::first();
        $lead = Lead::first();
        $activity = Activity::first();
        $email = Email::first();
        $dashboard = Dashboard::first();
        $report = Report::first();

        foreach ([
            route('people.show', $person), route('people.edit', $person),
            route('organizations.show', $organization), route('organizations.edit', $organization),
            route('deals.show', $deal), route('deals.edit', $deal),
            route('leads.show', $lead), route('leads.edit', $lead),
            route('activities.edit', $activity),
            route('emails.show', $email),
            route('dashboards.show', $dashboard),
            route('reports.show', $report),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_create_modals_are_not_prefilled_with_list_rows(): void
    {
        $lastPerson = Person::orderByDesc('name')->first();
        $lastDeal = Deal::open()->first();

        $this->get('/people')->assertDontSee('value="'.$lastPerson->phone.'"', false);
        $this->get('/deals')->assertDontSee('value="'.$lastDeal->title.'"', false);
        $this->get(route('people.show', $lastPerson))->assertDontSee('value="'.Activity::where('person_id', $lastPerson->id)->value('subject').'"', false);
    }

    public function test_insights_shows_empty_state_without_dashboards(): void
    {
        Dashboard::query()->delete();

        $this->get('/insights')->assertOk()->assertSee('Identify growth opportunities. Take action.');
    }

    public function test_insights_redirects_to_first_dashboard(): void
    {
        $this->get('/insights')->assertRedirect(route('dashboards.show', Dashboard::first()));
    }

    public function test_setup_guide_shows_progress(): void
    {
        $this->get('/setup')
            ->assertSee("Let's get you set up", false)
            ->assertSee('1/4 suggested tasks completed')
            ->assertSee('Agent Solutions');
    }

    public function test_search_returns_json_for_live_results(): void
    {
        $this->getJson('/search?q=Tony')
            ->assertOk()
            ->assertJsonPath('People.0.title', '[Sample] Tony Turner');
    }

    public function test_public_web_form_renders_for_guests(): void
    {
        $form = WebForm::first();
        auth()->logout();

        $this->get(route('forms.public.show', $form->slug))->assertOk()->assertSee($form->headline);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        auth()->logout();

        $this->get('/people')->assertRedirect('/login');
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }
}
