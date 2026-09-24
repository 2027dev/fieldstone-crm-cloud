<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;

/**
 * Tracks the onboarding milestones shown in the setup guide. A milestone counts as done once the
 * user has created their own (non-sample) record of that kind.
 */
class SetupProgress
{
    /**
     * @return array<string, bool>
     */
    public function state(): array
    {
        if (! Auth::check()) {
            return [];
        }

        return [
            'contact' => Person::where('is_sample', false)->exists(),
            'activity' => Activity::where('is_sample', false)->exists(),
            'deal' => Deal::where('is_sample', false)->exists(),
        ];
    }

    /**
     * @return list<array{key: string, title: string, description: string, duration: string, action: string, icon: string, url: string, done: bool}>
     */
    public function tasks(): array
    {
        $state = $this->state();

        return [
            ['key' => 'contact', 'title' => 'Add a contact', 'description' => 'Enter details about a person or company so their deals, emails and activities can be linked.', 'duration' => '1-2 min', 'action' => 'Add contact', 'icon' => 'contacts', 'url' => route('people.index', ['create' => 'person']), 'done' => $state['contact']],
            ['key' => 'activity', 'title' => 'Schedule an activity', 'description' => 'Arrange the details of a call, meeting or task to advance a deal.', 'duration' => '1-2 min', 'action' => 'Schedule activity', 'icon' => 'calendar', 'url' => route('activities.index', ['create' => 'activity']), 'done' => $state['activity']],
            ['key' => 'deal', 'title' => 'Add a deal', 'description' => 'Create an opportunity to move it through your sales process and close faster.', 'duration' => '2-4 min', 'action' => 'Add deal', 'icon' => 'dollar', 'url' => route('deals.index', ['create' => 'deal']), 'done' => $state['deal']],
        ];
    }

    /**
     * Completed milestones, including the always-complete "Set up account" step.
     */
    public function completedCount(): int
    {
        return 1 + count(array_filter($this->state()));
    }

    public function totalCount(): int
    {
        return 1 + count($this->state());
    }

    public function remainingCount(): int
    {
        return count(array_filter($this->state(), fn (bool $done): bool => ! $done));
    }
}
