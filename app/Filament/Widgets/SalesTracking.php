<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class SalesTracking extends ChartWidget
{
    use HasWidgetShield;
    protected static ?string $heading = 'Sales Tracking';
    protected static ?int $sort = 5;

    // public ?string $filter = 'monthly'; // default

    // protected function getFilters(): ?array
    // {
    //     return [
    //         'monthly' => 'Monthly',
    //         'quarterly' => 'Quarterly',
    //     ];
    // }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $this->getSalesData(),
                    'borderColor' => '#4CAF50',
                    'fill' => false,
                ],
            ],
            'labels' => $this->getLabels(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    // public function getColumnSpan(): array|string|int
    // {
    //     return 'full';
    // }
    protected function getSalesData(): array
    {
        
            $sales = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->selectRaw('MONTH(orders.created_at) as month, SUM(orders.total) as total_sales')
                ->whereYear('orders.created_at', now()->year)
                ->where('orders.payment_status', 'completed')
                ->where('orders.order_status', 'delivered')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total_sales', 'month')
                ->toArray();

            $months = range(1, 12);
            return array_map(fn($m) => $sales[$m] ?? 0, $months);
       
    }



    protected function getLabels(): array
    {   
        //output label quarterly
        // if ($this->filter === 'quarterly') {
        //     return [
        //         'Jan – Mar',
        //         'Apr – Jun',
        //         'Jul – Sep',
        //         'Oct – Dec',
        //     ];
        // } 
            return [
                'Jan', 'Feb', 'Mar',
                'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep',
                'Oct', 'Nov', 'Dec',
            ];
        
    }
}
