<?php

namespace App\Models;

use App\Enums\LeadLabel;
use App\Enums\LeadSource;
use App\Models\Concerns\BelongsToUser;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['title', 'value', 'currency', 'source', 'label', 'message', 'archived_at', 'converted_at', 'person_id', 'organization_id', 'deal_id', 'web_form_id', 'is_sample', 'user_id'])]
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'source' => LeadSource::class,
            'label' => LeadLabel::class,
            'archived_at' => 'datetime',
            'converted_at' => 'datetime',
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

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function webForm(): BelongsTo
    {
        return $this->belongsTo(WebForm::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->latest();
    }

    public function scopeInbox(Builder $query): Builder
    {
        return $query->whereNull('archived_at')->whereNull('converted_at');
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at')->whereNull('converted_at');
    }
}
