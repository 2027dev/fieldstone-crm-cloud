<?php

namespace App\Models;

use App\Enums\ReportType;
use App\Models\Concerns\BelongsToUser;
use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'type', 'position', 'dashboard_id', 'is_sample', 'user_id'])]
class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return [
            'type' => ReportType::class,
            'is_sample' => 'boolean',
        ];
    }

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class);
    }
}
