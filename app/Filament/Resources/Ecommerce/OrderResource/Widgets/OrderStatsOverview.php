<?php

namespace App\Filament\Resources\Ecommerce\OrderResource\Widgets;

use App\Enums\OrderStatusEnum;
use App\Models\Ecommerce\Order;
use Illuminate\Support\Facades\DB;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class OrderStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {

         $orderCounts = Order::select([
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN order_status = '".OrderStatusEnum::Pending->value."' THEN 1 ELSE 0 END) as pending"),
            DB::raw("SUM(CASE WHEN order_status = '".OrderStatusEnum::Processing->value."' THEN 1 ELSE 0 END) as processing"),
            DB::raw("SUM(CASE WHEN order_status = '".OrderStatusEnum::Shipped->value."' THEN 1 ELSE 0 END) as shipped"),
            DB::raw("SUM(CASE WHEN order_status = '".OrderStatusEnum::Delivered->value."' THEN 1 ELSE 0 END) as delivered"),
            DB::raw("SUM(CASE WHEN order_status = '".OrderStatusEnum::Cancelled->value."' THEN 1 ELSE 0 END) as cancelled"),
        ])->first();
        
        return [
            Stat::make('Total Orders', $orderCounts->total)
                ->descriptionIcon('heroicon-m-shopping-cart', IconPosition::Before)
                ->description('Total orders')
                ->color('success'),

            Stat::make('Pending', $orderCounts->pending ?? 0)
             ->descriptionIcon('heroicon-m-shopping-cart', IconPosition::Before)
             ->description('Pending orders')
                ->color('info'),

            Stat::make('Processing', $orderCounts->processing ?? 0)
                ->descriptionIcon('heroicon-m-shopping-cart', IconPosition::Before)
                ->description('Processing orders')
                ->color('warning'),

            Stat::make(' To Shipped', $orderCounts->shipped ?? 0)
                ->descriptionIcon('heroicon-m-shopping-cart', IconPosition::Before)
                ->description('Shipped orders')
                ->color('success'),

            Stat::make('Delivered', $orderCounts->delivered ?? 0)
                ->descriptionIcon('heroicon-m-shopping-cart', IconPosition::Before)
                ->description('Delivered orders')
                ->color('success'),

            Stat::make('Cancelled', $orderCounts->cancelled ?? 0)
                ->descriptionIcon('heroicon-m-shopping-cart', IconPosition::Before)
                ->description('Cancelled orders')
                ->color('danger'),

        ];
    }
}
