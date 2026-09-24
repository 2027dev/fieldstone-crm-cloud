<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['name', 'address', 'website', 'is_sample', 'user_id'])]
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return ['is_sample' => 'boolean'];
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->latest();
    }
}
