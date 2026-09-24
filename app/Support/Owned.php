<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class Owned
{
    /**
     * An exists rule restricted to rows owned by the authenticated user.
     */
    public static function exists(string $table): Exists
    {
        return Rule::exists($table, 'id')->where('user_id', Auth::id());
    }
}
