<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Database\Factories\NoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['body', 'notable_type', 'notable_id', 'is_sample', 'user_id'])]
class Note extends Model
{
    /** @use HasFactory<NoteFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return ['is_sample' => 'boolean'];
    }

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}
