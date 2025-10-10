<?php

namespace App\Filament\Resources\Ecommerce\FreeshippingCityResource\Pages;

use App\Filament\Resources\Ecommerce\FreeshippingCityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFreeshippingCity extends CreateRecord
{
    protected static string $resource = FreeshippingCityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
