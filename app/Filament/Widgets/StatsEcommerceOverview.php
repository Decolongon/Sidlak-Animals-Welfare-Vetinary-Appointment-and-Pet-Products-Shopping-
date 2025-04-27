<?php

namespace App\Filament\Widgets;

use App\Models\Ecommerce\Order;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\Ecommerce\OrderResource\Widgets\OrderStatsOverview;
use App\Models\Appointment\Appointment;
use App\Models\Ecommerce\Product;

class StatsEcommerceOverview extends BaseWidget
{
    use HasWidgetShield;
    // protected static ?string $pollingInterval = '60s';
     protected static ?int $sort = 2;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->descriptionIcon('heroicon-m-shopping-cart', IconPosition::Before)
                ->description('Total orders')
                // ->chart($this->getOrders())
                ->url(route('filament.admin.resources.ecommerce.orders.index', [
                    'tableFilters[order_status][value]' => 'new'
                ]))
                ->color('success'),

                Stat::make('Total Products In Stock', Product::where('prod_quantity', '>', 0)->count())
                ->descriptionIcon('heroicon-o-squares-plus', IconPosition::Before)
                ->description('Total products in stock')
                // ->chart($this->getOrders()) // optional, add a chart if you need
                ->url(route('filament.admin.resources.ecommerce.products.index'))
                ->color('primary'),

              
          
        ];

       
    }

    
}
