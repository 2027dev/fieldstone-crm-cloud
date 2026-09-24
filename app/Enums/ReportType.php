<?php

namespace App\Enums;

enum ReportType: string
{
    case DealsByStage = 'deals_by_stage';
    case DealsByStatus = 'deals_by_status';
    case RevenueByMonth = 'revenue_by_month';
    case ActivitiesByType = 'activities_by_type';
    case ActivitiesCompletion = 'activities_completion';
    case LeadsBySource = 'leads_by_source';

    public function label(): string
    {
        return match ($this) {
            self::DealsByStage => 'Open deals by stage',
            self::DealsByStatus => 'Deals by status',
            self::RevenueByMonth => 'Won revenue by month',
            self::ActivitiesByType => 'Activities by type',
            self::ActivitiesCompletion => 'Activity completion',
            self::LeadsBySource => 'Leads by source',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DealsByStage => 'How many open deals sit in each pipeline stage and what they are worth.',
            self::DealsByStatus => 'Share of open, won and lost deals.',
            self::RevenueByMonth => 'Value of deals won over the last six months.',
            self::ActivitiesByType => 'Calls, meetings, tasks and more, broken down by type.',
            self::ActivitiesCompletion => 'Completed versus outstanding activities.',
            self::LeadsBySource => 'Where your incoming leads come from.',
        };
    }

    /**
     * The visualisation used to render the report: horizontal bars, columns or a pie.
     */
    public function chart(): string
    {
        return match ($this) {
            self::DealsByStage, self::LeadsBySource => 'bar',
            self::RevenueByMonth, self::ActivitiesByType => 'column',
            self::DealsByStatus, self::ActivitiesCompletion => 'pie',
        };
    }
}
