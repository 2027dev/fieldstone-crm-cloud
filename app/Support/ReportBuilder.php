<?php

namespace App\Support;

use App\Enums\ActivityType;
use App\Enums\DealStage;
use App\Enums\DealStatus;
use App\Enums\LeadSource;
use App\Enums\ReportType;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;

/**
 * Turns a report type into chart-ready data points for the current workspace.
 */
class ReportBuilder
{
    /**
     * @return array{total: string, points: list<array{label: string, value: float, display: string, color: string}>}
     */
    public function build(ReportType $type): array
    {
        $points = match ($type) {
            ReportType::DealsByStage => $this->dealsByStage(),
            ReportType::DealsByStatus => $this->dealsByStatus(),
            ReportType::RevenueByMonth => $this->revenueByMonth(),
            ReportType::ActivitiesByType => $this->activitiesByType(),
            ReportType::ActivitiesCompletion => $this->activitiesCompletion(),
            ReportType::LeadsBySource => $this->leadsBySource(),
        };

        $sum = array_sum(array_column($points, 'value'));

        return [
            'total' => $type === ReportType::RevenueByMonth || $type === ReportType::DealsByStage
                ? Money::format($type === ReportType::DealsByStage ? (float) Deal::open()->sum('value') : $sum)
                : number_format($sum),
            'points' => $points,
        ];
    }

    /**
     * @return list<array{label: string, value: float, display: string, color: string}>
     */
    private function dealsByStage(): array
    {
        $rows = Deal::open()->selectRaw('stage, count(*) as aggregate')->groupBy('stage')->pluck('aggregate', 'stage');
        $palette = ['#1b5e20', '#2e7d32', '#43a047', '#66bb6a', '#a5d6a7'];

        return collect(DealStage::cases())->values()->map(fn (DealStage $stage, int $index): array => [
            'label' => $stage->label(),
            'value' => (float) ($rows[$stage->value] ?? 0),
            'display' => (string) ($rows[$stage->value] ?? 0),
            'color' => $palette[$index],
        ])->all();
    }

    /**
     * @return list<array{label: string, value: float, display: string, color: string}>
     */
    private function dealsByStatus(): array
    {
        $rows = Deal::query()->selectRaw('status, count(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $colors = ['open' => '#6c5ce7', 'won' => '#43a047', 'lost' => '#e05252'];

        return collect(DealStatus::cases())->map(fn (DealStatus $status): array => [
            'label' => $status->label(),
            'value' => (float) ($rows[$status->value] ?? 0),
            'display' => (string) ($rows[$status->value] ?? 0),
            'color' => $colors[$status->value],
        ])->all();
    }

    /**
     * @return list<array{label: string, value: float, display: string, color: string}>
     */
    private function revenueByMonth(): array
    {
        $start = now()->startOfMonth()->subMonths(5);
        $won = Deal::won()->where('closed_at', '>=', $start)->get(['value', 'closed_at']);

        return collect(range(0, 5))->map(function (int $offset) use ($start, $won): array {
            $month = $start->copy()->addMonths($offset);
            $value = (float) $won->filter(fn (Deal $deal): bool => $deal->closed_at->isSameMonth($month))->sum('value');

            return [
                'label' => $month->format('M'),
                'value' => $value,
                'display' => Money::compact($value),
                'color' => '#43a047',
            ];
        })->all();
    }

    /**
     * @return list<array{label: string, value: float, display: string, color: string}>
     */
    private function activitiesByType(): array
    {
        $rows = Activity::query()->selectRaw('type, count(*) as aggregate')->groupBy('type')->pluck('aggregate', 'type');

        return collect(ActivityType::cases())->map(fn (ActivityType $type): array => [
            'label' => $type->label(),
            'value' => (float) ($rows[$type->value] ?? 0),
            'display' => (string) ($rows[$type->value] ?? 0),
            'color' => '#6c5ce7',
        ])->all();
    }

    /**
     * @return list<array{label: string, value: float, display: string, color: string}>
     */
    private function activitiesCompletion(): array
    {
        $done = Activity::where('done', true)->count();
        $pending = Activity::where('done', false)->count();

        return [
            ['label' => 'Done', 'value' => (float) $done, 'display' => (string) $done, 'color' => '#43a047'],
            ['label' => 'To do', 'value' => (float) $pending, 'display' => (string) $pending, 'color' => '#a29bfe'],
        ];
    }

    /**
     * @return list<array{label: string, value: float, display: string, color: string}>
     */
    private function leadsBySource(): array
    {
        $rows = Lead::query()->selectRaw('source, count(*) as aggregate')->groupBy('source')->pluck('aggregate', 'source');

        return collect(LeadSource::cases())->map(fn (LeadSource $source): array => [
            'label' => $source->label(),
            'value' => (float) ($rows[$source->value] ?? 0),
            'display' => (string) ($rows[$source->value] ?? 0),
            'color' => '#2e7d32',
        ])->all();
    }
}
