<?php

namespace App\Filament\Resources\Ecommerce\OrderResource\Pages;

use Filament\Actions;
use App\Models\Ecommerce\Product;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Ecommerce\OrderResource;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        foreach ($this->record->orderItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->prod_quantity -= $item->quantity;
                $product->save();
            }
        }
    }

   
}
