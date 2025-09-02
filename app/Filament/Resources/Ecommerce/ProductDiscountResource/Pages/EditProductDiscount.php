<?php

namespace App\Filament\Resources\Ecommerce\ProductDiscountResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ContentTabPosition;
use App\Filament\Resources\Ecommerce\ProductDiscountResource;

class EditProductDiscount extends EditRecord
{
    protected static string $resource = ProductDiscountResource::class;
    protected static ?string $badge = 'new';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabPosition(): ?ContentTabPosition
    {
        return ContentTabPosition::Before;
    }

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }
}
