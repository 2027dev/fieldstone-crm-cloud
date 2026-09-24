<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Database\Factories\WebFormFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'headline', 'button_label', 'success_message', 'is_active', 'is_sample', 'user_id'])]
class WebForm extends Model
{
    /** @use HasFactory<WebFormFactory> */
    use BelongsToUser, HasFactory;

    protected static function booted(): void
    {
        static::creating(function (WebForm $webForm): void {
            $webForm->slug ??= Str::lower(Str::random(10));
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_sample' => 'boolean',
        ];
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
