<?php

namespace App\Models;

use App\Enums\DealStage;
use App\Enums\DealStatus;
use App\Models\Concerns\BelongsToUser;
use Database\Factories\DealFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['title', 'value', 'currency', 'stage', 'status', 'position', 'expected_close_date', 'closed_at', 'lost_reason', 'person_id', 'organization_id', 'is_sample', 'user_id'])]
class Deal extends Model
{
    /** @use HasFactory<DealFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'stage' => DealStage::class,
            'status' => DealStatus::class,
            'expected_close_date' => 'date',
            'closed_at' => 'datetime',
            'is_sample' => 'boolean',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->latest();
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', DealStatus::Open);
    }

    public function scopeWon(Builder $query): Builder
    {
        return $query->where('status', DealStatus::Won);
    }
}
