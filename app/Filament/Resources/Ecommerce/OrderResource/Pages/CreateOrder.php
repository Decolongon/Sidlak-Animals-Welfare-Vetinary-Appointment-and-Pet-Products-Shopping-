<?php

namespace App\Filament\Resources\Ecommerce\OrderResource\Pages;

use Filament\Actions;
use App\Models\Ecommerce\Product;
use Illuminate\Support\Facades\Log;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Ecommerce\OrderResource;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        foreach ($this->record->orderItems as $item) {
            $product = Product::find($item->product_id);
            if ($product && isset($item->quantity)) {
                $product->prod_quantity -= $item->quantity;
                $product->save();
            }
        }
    }


}
