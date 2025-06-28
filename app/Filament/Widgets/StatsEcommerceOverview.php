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
                //  ->chart([1,3,5,7,9,12,13,15])
                ->chart([3,1,5,2,7,3,9,8,10])
                ->url(route('filament.admin.resources.ecommerce.orders.index'))
                ->color('success'),

                Stat::make('Total Products', Product::count())
                ->descriptionIcon('heroicon-o-squares-plus', IconPosition::Before)
                ->description('Total products')
                // ->chart([1,3,5,7,9,12,13,15])
                ->chart([3,1,5,2,7,3,9,8,10])
                ->url(route('filament.admin.resources.ecommerce.products.index'))
                ->color('primary'),

              
          
        ];

       
    }

    
}
