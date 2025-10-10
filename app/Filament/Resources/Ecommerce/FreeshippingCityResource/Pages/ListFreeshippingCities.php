<?php

namespace App\Filament\Resources\Ecommerce\FreeshippingCityResource\Pages;

use App\Filament\Resources\Ecommerce\FreeshippingCityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFreeshippingCities extends ListRecords
{
    protected static string $resource = FreeshippingCityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-s-plus-circle'),
        ];
    }
}
