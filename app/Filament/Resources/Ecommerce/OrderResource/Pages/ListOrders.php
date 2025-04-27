<?php

namespace App\Filament\Resources\Ecommerce\OrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\Ecommerce\OrderResource;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Order')->icon('heroicon-m-plus-circle'),
        ];
    }


    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'New' => Tab::make()->query(fn ($query) => $query->where('order_status', 'new')),
            'Processing' => Tab::make()->query(fn ($query) => $query->where('order_status', 'processing')),
            'Shipped' => Tab::make()->query(fn ($query) => $query->where('order_status', 'shipped')),
            'Delivered' => Tab::make()->query(fn ($query) => $query->where('order_status', 'delivered')),
            'Cancelled' => Tab::make()->query(fn ($query) => $query->where('order_status', 'cancelled')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return OrderResource::getWidgets();
    }
}
