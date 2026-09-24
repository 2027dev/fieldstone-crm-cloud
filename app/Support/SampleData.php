<?php

namespace App\Support;

use App\Enums\ActivityPriority;
use App\Enums\ActivityType;
use App\Enums\DealStage;
use App\Enums\DealStatus;
use App\Enums\LeadLabel;
use App\Enums\LeadSource;
use App\Enums\ReportType;
use App\Models\Activity;
use App\Models\Dashboard;
use App\Models\Deal;
use App\Models\Email;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Report;
use App\Models\User;
use App\Models\WebForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Seeds and removes the "[Sample]" records that help a new workspace feel alive.
 */
class SampleData
{
    /**
     * Models that carry the is_sample flag, ordered so dependants are removed first.
     *
     * @var list<class-string<Model>>
     */
    private const MODELS = [
        Note::class,
        Report::class,
        Dashboard::class,
        Email::class,
        Activity::class,
        Lead::class,
        WebForm::class,
        Deal::class,
        Person::class,
        Organization::class,
    ];

    public function seedFor(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $base = ['user_id' => $user->id, 'is_sample' => true];

            $moveer = Organization::create($base + ['name' => '[Sample] MoveEr', 'address' => '1200 Market St, San Francisco, CA', 'website' => 'https://moveer.com']);
            $brightline = Organization::create($base + ['name' => '[Sample] Brightline Logistics', 'address' => '48 Harbor Rd, Boston, MA', 'website' => 'https://brightline.example']);
            $northwind = Organization::create($base + ['name' => '[Sample] Northwind Studio', 'address' => '9 Elm Ave, Austin, TX', 'website' => 'https://northwind.example']);

            $benjamin = Person::create($base + ['name' => '[Sample] Benjamin Leon', 'email' => 'benjamin.leon@gmial.com', 'phone' => '785-202-7824', 'job_title' => 'Founder']);
            $tony = Person::create($base + ['name' => '[Sample] Tony Turner', 'organization_id' => $moveer->id, 'email' => 'tony.turner@moveer.com', 'phone' => '218-348-8528', 'job_title' => 'Head of Operations']);
            $maria = Person::create($base + ['name' => '[Sample] Maria Alvarez', 'organization_id' => $brightline->id, 'email' => 'maria@brightline.example', 'phone' => '617-555-0142', 'job_title' => 'VP Procurement']);
            $kenji = Person::create($base + ['name' => '[Sample] Kenji Sato', 'organization_id' => $northwind->id, 'email' => 'kenji@northwind.example', 'phone' => '512-555-0199', 'job_title' => 'Creative Director']);
            $priya = Person::create($base + ['name' => '[Sample] Priya Nair', 'organization_id' => $brightline->id, 'email' => 'priya@brightline.example', 'phone' => '617-555-0178', 'job_title' => 'Operations Analyst']);

            $contextDeal = Deal::create($base + ['title' => '[Sample] Tony Turner', 'value' => 12500, 'stage' => DealStage::ContactMade, 'person_id' => $tony->id, 'organization_id' => $moveer->id, 'expected_close_date' => today()->addWeeks(3), 'position' => 0]);
            Deal::create($base + ['title' => '[Sample] Brightline fleet rollout', 'value' => 48000, 'stage' => DealStage::ProposalMade, 'person_id' => $maria->id, 'organization_id' => $brightline->id, 'expected_close_date' => today()->addWeeks(5), 'position' => 0]);
            Deal::create($base + ['title' => '[Sample] Northwind website retainer', 'value' => 9000, 'stage' => DealStage::Qualified, 'person_id' => $kenji->id, 'organization_id' => $northwind->id, 'expected_close_date' => today()->addWeeks(6), 'position' => 0]);
            Deal::create($base + ['title' => '[Sample] Leon consulting package', 'value' => 4200, 'stage' => DealStage::DemoScheduled, 'person_id' => $benjamin->id, 'expected_close_date' => today()->addWeeks(2), 'position' => 0]);
            Deal::create($base + ['title' => '[Sample] Brightline analytics add-on', 'value' => 15500, 'stage' => DealStage::Negotiations, 'person_id' => $priya->id, 'organization_id' => $brightline->id, 'expected_close_date' => today()->addWeek(), 'position' => 0]);
            Deal::create($base + ['title' => '[Sample] MoveEr pilot', 'value' => 7800, 'stage' => DealStage::Negotiations, 'status' => DealStatus::Won, 'closed_at' => now()->subMonths(1), 'person_id' => $tony->id, 'organization_id' => $moveer->id, 'position' => 1]);
            Deal::create($base + ['title' => '[Sample] Northwind brand refresh', 'value' => 11200, 'stage' => DealStage::ProposalMade, 'status' => DealStatus::Won, 'closed_at' => now()->subMonths(3), 'person_id' => $kenji->id, 'organization_id' => $northwind->id, 'position' => 1]);
            Deal::create($base + ['title' => '[Sample] Leon annual plan', 'value' => 3000, 'stage' => DealStage::DemoScheduled, 'status' => DealStatus::Lost, 'closed_at' => now()->subMonths(2), 'lost_reason' => 'Went with a competitor', 'person_id' => $benjamin->id, 'position' => 1]);

            Activity::create($base + ['type' => ActivityType::Call, 'subject' => '[Sample] Final attempt', 'person_id' => $benjamin->id, 'due_date' => today(), 'due_time' => '10:00']);
            Activity::create($base + ['type' => ActivityType::Call, 'subject' => '[Sample] Context call', 'person_id' => $tony->id, 'deal_id' => $contextDeal->id, 'organization_id' => $moveer->id, 'due_date' => today()->addDay(), 'due_time' => '14:30', 'priority' => ActivityPriority::High]);
            Activity::create($base + ['type' => ActivityType::Meeting, 'subject' => '[Sample] Proposal walkthrough', 'person_id' => $maria->id, 'organization_id' => $brightline->id, 'due_date' => today()->addDays(3), 'due_time' => '11:00', 'duration_minutes' => 60, 'priority' => ActivityPriority::Medium]);
            Activity::create($base + ['type' => ActivityType::Email, 'subject' => '[Sample] Send pricing sheet', 'person_id' => $kenji->id, 'organization_id' => $northwind->id, 'due_date' => today()->subDays(2)]);
            Activity::create($base + ['type' => ActivityType::Task, 'subject' => '[Sample] Prepare contract draft', 'person_id' => $priya->id, 'organization_id' => $brightline->id, 'due_date' => today()->addDays(8), 'priority' => ActivityPriority::Low]);
            Activity::create($base + ['type' => ActivityType::Lunch, 'subject' => '[Sample] Lunch with Tony', 'person_id' => $tony->id, 'organization_id' => $moveer->id, 'due_date' => today()->subDays(5), 'done' => true, 'done_at' => now()->subDays(5), 'outcome' => 'Positive — wants a pilot']);
            Activity::create($base + ['type' => ActivityType::Deadline, 'subject' => '[Sample] Proposal due', 'person_id' => $maria->id, 'due_date' => today()->addDays(6)]);

            $form = WebForm::create($base + ['name' => '[Sample] Website contact form', 'headline' => 'Get in touch with our sales team', 'button_label' => 'Request a call']);

            Lead::create($base + ['title' => '[Sample] Greenfield Farms lead', 'value' => 6500, 'source' => LeadSource::WebForm, 'web_form_id' => $form->id, 'label' => LeadLabel::Hot, 'message' => 'We need a CRM for our 12-person sales team. Can we get a demo next week?']);
            Lead::create($base + ['title' => '[Sample] Priya Nair expansion', 'value' => 9000, 'source' => LeadSource::Referral, 'person_id' => $priya->id, 'organization_id' => $brightline->id, 'label' => LeadLabel::Warm]);
            Lead::create($base + ['title' => '[Sample] Trade show contact', 'source' => LeadSource::Event, 'label' => LeadLabel::Cold]);

            Email::create($base + ['folder' => 'inbox', 'from_name' => '[Sample] Tony Turner', 'from_email' => 'tony.turner@moveer.com', 'to_email' => $user->email, 'subject' => 'Re: Pilot timeline', 'body' => "Hi,\n\nThanks for the call yesterday. The team is excited about the pilot. Could you send over the updated timeline and the onboarding checklist?\n\nBest,\nTony", 'person_id' => $tony->id, 'deal_id' => $contextDeal->id, 'created_at' => now()->subHours(3)]);
            Email::create($base + ['folder' => 'inbox', 'from_name' => '[Sample] Maria Alvarez', 'from_email' => 'maria@brightline.example', 'to_email' => $user->email, 'subject' => 'Questions about the proposal', 'body' => "Hello,\n\nWe reviewed the proposal. Two quick questions: does the price include training, and can we start with 40 seats?\n\nMaria", 'person_id' => $maria->id, 'created_at' => now()->subDay()]);
            Email::create($base + ['folder' => 'inbox', 'from_name' => '[Sample] Kenji Sato', 'from_email' => 'kenji@northwind.example', 'to_email' => $user->email, 'subject' => 'Brand refresh — thank you!', 'body' => "Just wanted to say the new brand assets look fantastic. Let's talk about the website retainer soon.\n\nKenji", 'person_id' => $kenji->id, 'read_at' => now()->subDays(2), 'created_at' => now()->subDays(3)]);
            Email::create($base + ['folder' => 'sent', 'from_name' => $user->name, 'from_email' => $user->email, 'to_email' => 'benjamin.leon@gmial.com', 'subject' => 'Following up', 'body' => "Hi Benjamin,\n\nJust following up on our last conversation. Do you have 15 minutes this week?\n\nThanks!", 'person_id' => $benjamin->id, 'read_at' => now(), 'created_at' => now()->subDays(2)]);

            Note::create($base + ['notable_type' => Person::class, 'notable_id' => $tony->id, 'body' => 'Prefers calls in the afternoon. Decision maker for ops tooling.']);
            Note::create($base + ['notable_type' => Person::class, 'notable_id' => $maria->id, 'body' => 'Budget approved for Q4. Needs sign-off from finance.']);

            $dashboard = Dashboard::create($base + ['name' => '[Sample] Sales overview']);
            foreach ([ReportType::DealsByStage, ReportType::DealsByStatus, ReportType::RevenueByMonth, ReportType::ActivitiesByType] as $position => $type) {
                Report::create($base + ['dashboard_id' => $dashboard->id, 'name' => $type->label(), 'type' => $type, 'position' => $position]);
            }
        });
    }

    public function removeFor(User $user): void
    {
        DB::transaction(function () use ($user): void {
            foreach (self::MODELS as $model) {
                $model::withoutGlobalScopes()->where('user_id', $user->id)->where('is_sample', true)->delete();
            }
        });
    }

    public function existsFor(User $user): bool
    {
        return Person::withoutGlobalScopes()->where('user_id', $user->id)->where('is_sample', true)->exists()
            || Deal::withoutGlobalScopes()->where('user_id', $user->id)->where('is_sample', true)->exists();
    }
}
