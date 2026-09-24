<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Database\Factories\EmailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['folder', 'from_name', 'from_email', 'to_email', 'subject', 'body', 'read_at', 'is_starred', 'person_id', 'deal_id', 'is_sample', 'user_id'])]
class Email extends Model
{
    /** @use HasFactory<EmailFactory> */
    use BelongsToUser, HasFactory;

    public const FOLDERS = ['inbox' => 'Inbox', 'drafts' => 'Drafts', 'sent' => 'Sent', 'archive' => 'Archive'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'is_starred' => 'boolean',
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

    public function counterpart(): string
    {
        return $this->folder === 'inbox' || $this->folder === 'archive'
            ? ($this->from_name ?: $this->from_email)
            : $this->to_email;
    }
}
