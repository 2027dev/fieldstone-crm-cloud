<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\Email;
use App\Models\Note;
use App\Models\Person;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ContactsTimelineController extends Controller
{
    /**
     * A chronological feed of everything that happened with your contacts.
     */
    public function __invoke(): View
    {
        $events = collect()
            ->merge(Activity::with('person')->whereNotNull('person_id')->latest('updated_at')->limit(40)->get()->map(fn (Activity $activity): array => [
                'kind' => 'activity',
                'icon' => $activity->type->icon(),
                'title' => $activity->subject,
                'meta' => ($activity->done ? 'Completed ' : 'Scheduled ').$activity->type->label(),
                'person' => $activity->person,
                'url' => route('people.show', $activity->person_id),
                'at' => $activity->done_at ?? $activity->updated_at,
            ]))
            ->merge(Deal::with('person')->whereNotNull('person_id')->latest()->limit(40)->get()->map(fn (Deal $deal): array => [
                'kind' => 'deal',
                'icon' => 'dollar',
                'title' => $deal->title,
                'meta' => 'Deal '.($deal->status->value === 'open' ? 'created' : $deal->status->value),
                'person' => $deal->person,
                'url' => route('deals.show', $deal),
                'at' => $deal->closed_at ?? $deal->created_at,
            ]))
            ->merge(Email::with('person')->whereNotNull('person_id')->latest()->limit(40)->get()->map(fn (Email $email): array => [
                'kind' => 'email',
                'icon' => 'mail',
                'title' => $email->subject,
                'meta' => $email->folder === 'sent' ? 'Email sent' : 'Email received',
                'person' => $email->person,
                'url' => route('emails.show', $email),
                'at' => $email->created_at,
            ]))
            ->merge(Note::where('notable_type', Person::class)->with('notable')->latest()->limit(40)->get()->map(fn (Note $note): array => [
                'kind' => 'note',
                'icon' => 'note',
                'title' => str($note->body)->limit(90)->toString(),
                'meta' => 'Note added',
                'person' => $note->notable,
                'url' => route('people.show', $note->notable_id),
                'at' => $note->created_at,
            ]))
            ->filter(fn (array $event): bool => $event['person'] !== null)
            ->sortByDesc('at')
            ->take(60);

        return view('contacts.timeline', ['groups' => $this->groupByDay($events)]);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $events
     * @return Collection<string, Collection<int, array<string, mixed>>>
     */
    private function groupByDay(Collection $events): Collection
    {
        return $events->groupBy(fn (array $event): string => match (true) {
            $event['at']->isToday() => 'Today',
            $event['at']->isYesterday() => 'Yesterday',
            default => $event['at']->format('l, F j'),
        });
    }
}
