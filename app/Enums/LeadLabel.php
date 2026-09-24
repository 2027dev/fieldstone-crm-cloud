<?php

namespace App\Enums;

enum LeadLabel: string
{
    case Hot = 'hot';
    case Warm = 'warm';
    case Cold = 'cold';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Hot => 'bg-red-100 text-red-700',
            self::Warm => 'bg-amber-100 text-amber-800',
            self::Cold => 'bg-sky-100 text-sky-800',
        };
    }
}
