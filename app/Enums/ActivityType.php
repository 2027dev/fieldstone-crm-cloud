<?php

namespace App\Enums;

enum ActivityType: string
{
    case Call = 'call';
    case Meeting = 'meeting';
    case Task = 'task';
    case Deadline = 'deadline';
    case Email = 'email';
    case Lunch = 'lunch';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::Call => 'phone',
            self::Meeting => 'users',
            self::Task => 'clock',
            self::Deadline => 'flag',
            self::Email => 'mail',
            self::Lunch => 'utensils',
        };
    }
}
