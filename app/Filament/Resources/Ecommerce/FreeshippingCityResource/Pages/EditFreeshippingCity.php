<?php

namespace App\Filament\Resources\Ecommerce\FreeshippingCityResource\Pages;

use App\Filament\Resources\Ecommerce\FreeshippingCityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFreeshippingCity extends EditRecord
{
    protected static string $resource = FreeshippingCityResource::class;

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
