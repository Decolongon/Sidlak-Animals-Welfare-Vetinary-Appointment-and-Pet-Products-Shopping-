<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesTracking extends ChartWidget
{
    protected static ?string $heading = 'Sales';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        return [
            'datasets' =>[
                [
                    'label' => 'Sales',
                    'data' => $this->getSalesData(),
                    'borderColor' => '#4CAF50',
                    'fill' => false,
                ],

            ],
            'labels' => $this->getLabel(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getSalesData(): array
    {
         DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total_sales')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_sales')
            ->toArray();
            $allMonths = range(1, 12);
            $salesData = array_map(fn($month) => $sales[$month] ?? 0, $allMonths);

            return $salesData;
    }

    protected function getLabel(): array
    {
        return [
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec',
            'Jan',
            'Feb',
            'Mar',
            'Apr',
        ];
    }
}
