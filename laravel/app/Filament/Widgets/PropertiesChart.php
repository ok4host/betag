<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PropertiesChart extends ChartWidget
{
    protected static ?string $heading = 'العقارات المضافة';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = $this->getPropertiesPerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'للبيع',
                    'data' => $data['sale'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'للإيجار',
                    'data' => $data['rent'],
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getPropertiesPerMonth(): array
    {
        $months = collect(range(5, 0))->map(function ($month) {
            return Carbon::now()->subMonths($month);
        });

        $labels = $months->map(fn ($month) => $month->translatedFormat('M Y'))->toArray();

        $saleData = $months->map(function ($month) {
            return Property::where('type', 'sale')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        })->toArray();

        $rentData = $months->map(function ($month) {
            return Property::where('type', 'rent')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        })->toArray();

        return [
            'labels' => $labels,
            'sale' => $saleData,
            'rent' => $rentData,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}
