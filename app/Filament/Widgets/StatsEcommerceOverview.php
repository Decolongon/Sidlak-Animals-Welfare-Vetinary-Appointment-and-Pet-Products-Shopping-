<?php

namespace App\Filament\Widgets;

use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Product;
use App\Filament\Widgets\SalesTracking;
use App\Helpers\YearFilterHelper;
use App\Models\Appointment\Appointment;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use PHPUnit\TextUI\Configuration\Php;

class StatsEcommerceOverview extends BaseWidget
{
    use HasWidgetShield;
    //protected static ?string $pollingInterval = '60s';
    protected static ?int $sort = 2;
    protected ?int $currentYear = null;

    private YearFilterHelper $helper;
    public function __construct()
    {
        //$this->currentYear = app(YearFilterHelper::class)->getYear();
        $this->helper = app(YearFilterHelper::class);
        $this->currentYear = $this->helper->getYear();
    }

    // public function getListeners(): array
    // {
    //     return [
    //         'updateStats' => 'updatedCurrentYear',
    //     ];
    // }

    // public function updatedCurrentYear($year)
    // {
    //      $this->currentYear = app(YearFilterHelper::class)->setYear($year);
    //       if(app(YearFilterHelper::class)->getYear() !== $this->currentYear){
    //         $this->currentYear = app(YearFilterHelper::class)->setYear($year);
    //     }
    // }
    protected function getStats(): array
    {
        // dd($this->currentYear);

        return [
            Stat::make('Total Orders', Order::count())
                ->descriptionIcon('heroicon-m-shopping-cart', IconPosition::Before)
                ->description('Total orders')
                //  ->chart([1,3,5,7,9,12,13,15])
                ->chart([3, 1, 5, 2, 7, 3, 9, 8, 10])
                ->url(route('filament.admin.resources.ecommerce.orders.index'))
                ->color('success'),

            Stat::make('Total Products', Product::count())
                ->descriptionIcon('heroicon-o-squares-plus', IconPosition::Before)
                ->description('Total products')
                // ->chart([1,3,5,7,9,12,13,15])
                ->chart([3, 1, 5, 2, 7, 3, 9, 8, 10])
                ->url(route('filament.admin.resources.ecommerce.products.index'))
                ->color('primary'),

            Stat::make('Total Sales', Order::where('order_status', 'delivered')
                ->whereYear('created_at', $this->currentYear)
                ->where('payment_status', 'completed')
                ->sum('total'))
                ->chart([3, 1, 5, 2, 7, 3, 9, 8, 10])
                ->color('info')
                ->descriptionIcon('heroicon-o-presentation-chart-line', IconPosition::Before)
                ->description('Total Sales '. $this->currentYear)





        ];
    }
}
