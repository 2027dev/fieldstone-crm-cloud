<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'email_label', 'phone', 'phone_label', 'job_title', 'organization_id', 'is_sample', 'user_id'])]
class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return ['is_sample' => 'boolean'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->latest();
    }

    public function initials(): string
    {
        return Str::of(Str::replace('[Sample]', '', $this->name))
            ->trim()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }
}
