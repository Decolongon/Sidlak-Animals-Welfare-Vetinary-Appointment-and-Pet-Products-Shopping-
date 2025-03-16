<?php

namespace App\Filament\Resources\Ecommerce\ProductReviewsResource\Pages;

use App\Filament\Resources\Ecommerce\ProductReviewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductReviews extends CreateRecord
{
    protected static string $resource = ProductReviewsResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
