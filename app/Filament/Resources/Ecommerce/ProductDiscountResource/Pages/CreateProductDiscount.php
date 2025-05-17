<?php

namespace App\Filament\Resources\Ecommerce\ProductDiscountResource\Pages;

use App\Filament\Resources\Ecommerce\ProductDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductDiscount extends CreateRecord
{
    protected static string $resource = ProductDiscountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
