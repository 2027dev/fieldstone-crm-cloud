<?php

namespace App\Enums;

enum DealStage: string
{
    case Qualified = 'qualified';
    case ContactMade = 'contact_made';
    case DemoScheduled = 'demo_scheduled';
    case ProposalMade = 'proposal_made';
    case Negotiations = 'negotiations';

    public function label(): string
    {
        return match ($this) {
            self::Qualified => 'Qualified',
            self::ContactMade => 'Contact made',
            self::DemoScheduled => 'Demo scheduled',
            self::ProposalMade => 'Proposal made',
            self::Negotiations => 'Negotiations started',
        };
    }

    public function probability(): int
    {
        return match ($this) {
            self::Qualified => 10,
            self::ContactMade => 20,
            self::DemoScheduled => 40,
            self::ProposalMade => 60,
            self::Negotiations => 80,
        };
    }
}
