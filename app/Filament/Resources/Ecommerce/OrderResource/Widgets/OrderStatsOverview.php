<?php

namespace App\Filament\Resources\Ecommerce\OrderResource\Widgets;

use App\Models\Ecommerce\Order;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class OrderStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {

        $orderCounts = Order::select([
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN order_status = 'new' THEN 1 ELSE 0 END) as new"),
            DB::raw("SUM(CASE WHEN order_status = 'processing' THEN 1 ELSE 0 END) as processing"),
            DB::raw("SUM(CASE WHEN order_status = 'shipped' THEN 1 ELSE 0 END) as shipped"),
            DB::raw("SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered"),
            DB::raw("SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled"),
        ])->first();

        
        return [
            Stat::make('Total Orders', $orderCounts->total)
                ->color('success'),

            // Stat::make('New', $orderCounts->new)
            //     ->color('info'),

            // Stat::make('Processing', $orderCounts->processing)
            //     ->color('warning'),

            // Stat::make(' To Shipped', $orderCounts->shipped)
            //     ->color('success'),

            // Stat::make('Delivered', $orderCounts->delivered)
            //     ->color('success'),

            // Stat::make('Cancelled', $orderCounts->cancelled)
            //     ->color('danger'),

        ];
    }
}
