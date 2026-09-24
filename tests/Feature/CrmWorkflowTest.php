<?php

namespace Tests\Feature;

use App\Enums\DealStage;
use App\Enums\DealStatus;
use App\Enums\LeadSource;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Email;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Organization;
use App\Models\Person;
use App\Models\User;
use App\Models\WebForm;
use App\Support\SampleData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CrmWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_workspace_with_sample_data(): void
    {
        $this->post('/register', [
            'name' => 'Ada Lovelace',
            'company_name' => 'Analytical Engines',
            'email' => 'ada@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect(route('setup'));

        $user = User::where('email', 'ada@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(app(SampleData::class)->existsFor($user));
    }

    public function test_login_and_logout(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('setup'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_demo_creates_isolated_workspace(): void
    {
        $this->post('/demo')->assertRedirect(route('setup'));

        $this->assertAuthenticated();
        $this->assertSame(5, Person::count());
    }

    public function test_users_cannot_see_or_reference_each_others_records(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $person = Person::factory()->for($owner)->create();
        $organization = Organization::factory()->for($owner)->create();

        $this->actingAs($intruder);

        $this->get(route('people.show', $person))->assertNotFound();
        $this->delete(route('people.destroy', $person))->assertNotFound();
        $this->get('/people')->assertDontSee($person->name);

        $this->post(route('people.store'), ['name' => 'Sneaky', 'organization_id' => $organization->id])
            ->assertSessionHasErrors('organization_id');
    }

    public function test_person_can_be_created_with_new_organization(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('people.store'), [
            'name' => 'Grace Hopper',
            'organization_name' => 'Navy Labs',
            'email' => 'grace@navy.example',
            'phone' => '555-0100',
        ])->assertRedirect();

        $person = Person::where('name', 'Grace Hopper')->firstOrFail();
        $this->assertSame('Navy Labs', $person->organization->name);
        $this->assertSame($user->id, $person->user_id);
        $this->assertSame('Work', $person->email_label);
    }

    public function test_setup_progress_counts_only_real_records(): void
    {
        $user = User::factory()->create();
        app(SampleData::class)->seedFor($user);
        $this->actingAs($user);

        $this->get('/setup')->assertSee('1/4 suggested tasks completed');

        Person::factory()->for($user)->create();

        $this->get('/setup')->assertSee('2/4 suggested tasks completed');
    }

    public function test_deal_can_move_through_pipeline_and_be_won(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('deals.store'), ['title' => 'Big deal', 'value' => 5000, 'stage' => 'qualified'])->assertRedirect(route('deals.index'));
        $deal = Deal::where('title', 'Big deal')->firstOrFail();

        $this->patchJson(route('deals.move', $deal), ['stage' => 'proposal_made', 'order' => [$deal->id]])->assertOk();
        $this->assertSame(DealStage::ProposalMade, $deal->fresh()->stage);

        $this->patch(route('deals.status', $deal), ['status' => 'won'])->assertRedirect();
        $this->assertSame(DealStatus::Won, $deal->fresh()->status);
        $this->assertNotNull($deal->fresh()->closed_at);

        $this->patch(route('deals.status', $deal), ['status' => 'lost', 'lost_reason' => 'Budget'])->assertRedirect();
        $this->assertSame('Budget', $deal->fresh()->lost_reason);
    }

    public function test_lead_converts_into_deal(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $lead = Lead::factory()->for($user)->create(['value' => 7000]);
        $activity = Activity::factory()->for($user)->create(['lead_id' => $lead->id]);

        $response = $this->post(route('leads.convert', $lead));

        $deal = Deal::where('title', $lead->title)->firstOrFail();
        $response->assertRedirect(route('deals.show', $deal));
        $this->assertSame('7000.00', $deal->value);
        $this->assertNotNull($lead->fresh()->converted_at);
        $this->assertSame($deal->id, $activity->fresh()->deal_id);
        $this->get('/leads')->assertDontSee($lead->title);

        $this->post(route('leads.convert', $lead))->assertStatus(409);
    }

    public function test_leads_can_be_archived_and_restored(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $lead = Lead::factory()->for($user)->create();

        $this->patch(route('leads.archive', $lead));
        $this->assertNotNull($lead->fresh()->archived_at);

        $this->patch(route('leads.bulk'), ['ids' => [$lead->id], 'action' => 'unarchive']);
        $this->assertNull($lead->fresh()->archived_at);
    }

    public function test_public_web_form_submission_creates_lead_for_form_owner(): void
    {
        $owner = User::factory()->create();
        $form = WebForm::factory()->for($owner)->create();
        $visitor = User::factory()->create();

        $this->actingAs($visitor)->post(route('forms.public.store', $form->slug), [
            'name' => 'Jane Visitor',
            'email' => 'jane@visitor.example',
            'company' => 'Visitor Co',
            'message' => 'Hello!',
        ])->assertSessionHas('submitted', true);

        $lead = Lead::withoutGlobalScopes()->where('web_form_id', $form->id)->firstOrFail();
        $this->assertSame($owner->id, $lead->user_id);
        $this->assertSame(LeadSource::WebForm, $lead->source);
        $this->assertSame('Visitor Co lead', $lead->title);
        $this->assertSame($owner->id, Person::withoutGlobalScopes()->where('email', 'jane@visitor.example')->value('user_id'));
        $this->assertSame(1, $form->fresh()->submissions_count);
    }

    public function test_public_web_form_rejects_honeypot_and_inactive_forms(): void
    {
        $form = WebForm::factory()->create();

        $this->post(route('forms.public.store', $form->slug), ['name' => 'Bot', 'email' => 'bot@spam.example', 'website' => 'http://spam'])
            ->assertSessionHasErrors('website');

        $form->forceFill(['is_active' => false])->save();
        $this->get(route('forms.public.show', $form->slug))->assertNotFound();
    }

    public function test_activity_can_be_scheduled_toggled_and_bulk_completed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $deal = Deal::factory()->for($user)->create(['person_id' => Person::factory()->for($user)]);

        $this->post(route('activities.store'), ['type' => 'meeting', 'subject' => 'Kickoff', 'due_date' => today()->toDateString(), 'due_time' => '09:30', 'deal_id' => $deal->id])
            ->assertSessionHasNoErrors();

        $activity = Activity::where('subject', 'Kickoff')->firstOrFail();
        $this->assertSame($deal->person_id, $activity->person_id);

        $this->patchJson(route('activities.toggle', $activity))->assertJson(['done' => true]);
        $this->patchJson(route('activities.toggle', $activity))->assertJson(['done' => false]);

        $this->patch(route('activities.bulk'), ['ids' => [$activity->id], 'action' => 'done']);
        $this->assertTrue($activity->fresh()->done);
    }

    public function test_sample_data_can_be_removed_without_touching_real_records(): void
    {
        $user = User::factory()->create();
        app(SampleData::class)->seedFor($user);
        $real = Person::factory()->for($user)->create();
        $this->actingAs($user);

        $this->delete(route('sample-data.destroy'))->assertRedirect();

        $this->assertSame([$real->id], Person::pluck('id')->all());
        $this->assertSame(0, Deal::count());
        $this->assertSame(0, Email::count());
    }

    public function test_duplicates_are_merged_into_primary_person(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $primary = Person::factory()->for($user)->create(['name' => 'Sam Smith', 'email' => 'sam@example.com', 'phone' => null]);
        $duplicate = Person::factory()->for($user)->create(['name' => 'Sam  smith', 'email' => 'sam@example.com', 'phone' => '555-1234']);
        $deal = Deal::factory()->for($user)->create(['person_id' => $duplicate->id]);
        $note = Note::factory()->for($user)->create(['notable_type' => Person::class, 'notable_id' => $duplicate->id]);

        $this->get(route('contacts.duplicates'))->assertSee('2 possible duplicates');

        $this->post(route('contacts.duplicates.merge'), ['primary_id' => $primary->id, 'ids' => [$primary->id, $duplicate->id]])
            ->assertRedirect(route('contacts.duplicates'));

        $this->assertModelMissing($duplicate);
        $this->assertSame($primary->id, $deal->fresh()->person_id);
        $this->assertSame($primary->id, $note->fresh()->notable_id);
        $this->assertSame('555-1234', $primary->fresh()->phone);
    }

    public function test_people_can_be_imported_from_csv(): void
    {
        $user = User::factory()->create();
        $csv = "Name,Email,Phone,Organization\nAlan Turing,alan@example.com,555-0001,Bletchley\nKatherine Johnson,not-an-email,,NASA\n";

        $this->actingAs($user)->post(route('import', 'people'), [
            'file' => UploadedFile::fake()->createWithContent('people.csv', $csv),
        ])->assertSessionHas('status', 'Imported 2 people.');

        $this->assertSame('Bletchley', Person::where('name', 'Alan Turing')->first()->organization->name);
        $this->assertNull(Person::where('name', 'Katherine Johnson')->first()->email);
    }

    public function test_email_compose_links_to_matching_contact(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $person = Person::factory()->for($user)->create(['email' => 'client@example.com']);

        $this->post(route('emails.store'), ['to_email' => 'client@example.com', 'subject' => 'Proposal', 'body' => 'Attached.'])->assertRedirect();

        $email = Email::firstOrFail();
        $this->assertSame('sent', $email->folder);
        $this->assertSame($person->id, $email->person_id);
    }

    public function test_notes_attach_to_records(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $deal = Deal::factory()->for($user)->create();

        $this->post(route('notes.store'), ['notable_type' => 'deal', 'notable_id' => $deal->id, 'body' => 'Call back Friday'])->assertRedirect();

        $this->assertSame('Call back Friday', $deal->notes()->first()->body);
    }

    public function test_dashboard_is_created_with_starter_reports(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('dashboards.store'), ['name' => 'Team KPIs', 'starter' => 1])->assertRedirect();

        $this->get('/insights')->assertRedirect();
        $this->followingRedirects()->get('/insights')->assertSee('Team KPIs')->assertSee('Open deals by stage');
    }
}
