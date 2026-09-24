<?php

namespace App\Enums;

enum ActivityPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Low => 'bg-slate-100 text-slate-700',
            self::Medium => 'bg-amber-100 text-amber-800',
            self::High => 'bg-red-100 text-red-700',
        };
    }
}
