<?php

namespace App\Enums;

enum LeadSource: string
{
    case Manual = 'manual';
    case WebForm = 'web_form';
    case Import = 'import';
    case Referral = 'referral';
    case Event = 'event';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manually created',
            self::WebForm => 'Web form',
            self::Import => 'Import',
            self::Referral => 'Referral',
            self::Event => 'Event',
        };
    }
}
