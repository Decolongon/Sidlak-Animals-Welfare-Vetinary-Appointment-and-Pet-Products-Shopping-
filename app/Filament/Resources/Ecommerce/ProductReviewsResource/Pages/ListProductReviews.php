<?php

namespace App\Filament\Resources\Ecommerce\ProductReviewsResource\Pages;

use App\Filament\Resources\Ecommerce\ProductReviewsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductReviews extends ListRecords
{
    protected static string $resource = ProductReviewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Product Review')->icon('heroicon-m-plus-circle'),
        ];
    }
}
