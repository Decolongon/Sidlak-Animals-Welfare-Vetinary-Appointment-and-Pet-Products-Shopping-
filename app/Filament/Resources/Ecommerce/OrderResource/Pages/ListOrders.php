<?php

namespace App\Filament\Resources\Ecommerce\OrderResource\Pages;

use Filament\Actions;
use App\Models\Ecommerce\Order;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Ecommerce\OrderResource;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Order')->icon('heroicon-m-plus-circle')
            // ->hidden(function() {
            //     if(auth()->user()->hasAnyRole(['super-admin','super_admin'])) {
            //         return false;
            //     }
            //     return true;
            // }),
        ];
    }


    public function getTabs(): array
    {
       return $this->getOrderTab();
    }

    protected function getOrderTab(): array
    {
         return [
            'All' => Tab::make()
                    ->badge(Order::count()),

            'Pending' => Tab::make()
                    ->modifyQueryUsing(fn ($query) => $query->where('order_status', 'pending'))
                    ->badge(Order::where('order_status', 'pending')->count()),

            'Processing' => Tab::make()
                            ->modifyQueryUsing(fn ($query) => $query->where('order_status', 'processing'))
                            ->badge(Order::where('order_status', 'processing')->count()),

            'Shipped' => Tab::make()
                        ->modifyQueryUsing(fn ($query) => $query->where('order_status', 'shipped'))
                        ->badge(Order::where('order_status', 'shipped')->count()),

            'Delivered' => Tab::make()
                            ->modifyQueryUsing(fn ($query) => $query->where('order_status', 'delivered'))
                            ->badge(Order::where('order_status', 'delivered')->count()),


            'Cancelled' => Tab::make()
                            ->modifyQueryUsing(fn ($query) => $query->where('order_status', 'cancelled'))
                            ->badge(Order::where('order_status', 'cancelled')->count()),
        ];
    }

    // protected function getHeaderWidgets(): array
    // {
    //    return [
    //         OrderResource\Widgets\OrderStatsOverview::class,
    //     ];  
    // }
}
