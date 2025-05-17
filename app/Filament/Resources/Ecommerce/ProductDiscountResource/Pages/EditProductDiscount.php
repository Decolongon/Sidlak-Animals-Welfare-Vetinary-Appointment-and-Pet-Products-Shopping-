<?php

namespace App\Filament\Resources\Ecommerce\ProductDiscountResource\Pages;

use App\Filament\Resources\Ecommerce\ProductDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductDiscount extends EditRecord
{
    protected static string $resource = ProductDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
