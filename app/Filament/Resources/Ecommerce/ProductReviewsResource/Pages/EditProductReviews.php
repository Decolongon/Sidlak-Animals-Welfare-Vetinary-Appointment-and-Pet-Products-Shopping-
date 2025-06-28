<?php

namespace App\Filament\Resources\Ecommerce\ProductReviewsResource\Pages;

use App\Filament\Resources\Ecommerce\ProductReviewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductReviews extends EditRecord
{
    protected static string $resource = ProductReviewsResource::class;

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
