<?php

namespace App\Models;

use App\Enums\ActivityPriority;
use App\Enums\ActivityType;
use App\Models\Concerns\BelongsToUser;
use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['type', 'subject', 'due_date', 'due_time', 'duration_minutes', 'priority', 'outcome', 'note', 'done', 'done_at', 'person_id', 'deal_id', 'organization_id', 'lead_id', 'is_sample', 'user_id'])]
class Activity extends Model
{
    /** @use HasFactory<ActivityFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return [
            'type' => ActivityType::class,
            'priority' => ActivityPriority::class,
            'due_date' => 'date',
            'done' => 'boolean',
            'done_at' => 'datetime',
            'is_sample' => 'boolean',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('done', false);
    }

    public function isOverdue(): bool
    {
        return ! $this->done && $this->due_date !== null && $this->due_date->lt(today());
    }

    public function formattedDueTime(): ?string
    {
        return $this->due_time ? substr($this->due_time, 0, 5) : null;
    }
}
