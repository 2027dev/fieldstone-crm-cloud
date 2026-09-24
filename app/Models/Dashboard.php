<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Database\Factories\DashboardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'is_sample', 'user_id'])]
class Dashboard extends Model
{
    /** @use HasFactory<DashboardFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return ['is_sample' => 'boolean'];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class)->orderBy('position');
    }
}
