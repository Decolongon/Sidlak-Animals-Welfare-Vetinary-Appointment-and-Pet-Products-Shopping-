<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Ecommerce\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use App\Models\Ecommerce\ProductCategory;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TopRatesProducts extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Top Rated Products';
    protected static ?int $sort = 6;
    protected static string $color = 'primary';
    protected int | string | array $columnSpan = 'full';


    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->with(['reviews', 'images', 'productCategories'])
                    ->withCount('reviews') // Get reviews count
                    ->withAvg('reviews', 'rating') // Get average rating 
                    ->having('reviews_avg_rating', '>=', 1) // Only products with at least 1 rating
                    ->orderBy('reviews_avg_rating', 'desc') // Order by average rating
                    ->orderBy('reviews_count', 'desc') // Then by number of reviews
            )
            ->columns([
                TextColumn::make('prod_name')
                    ->label('Product Name')
                    // ->sortable()
                    ->searchable(),

                ImageColumn::make('images.url')
                    ->label('Product Image')
                    ->limit(1)
                    ->getStateUsing(fn($record) => $record->images()->where('is_primary', true)?->value('url')   ?? $record->images()->orderBy('created_at')->value('url'))
                    ->circular(),

                TextColumn::make('reviews_avg_rating')
                    ->label('Avg Rating')
                    ->numeric(1) // Show 1 decimal place
                    // ->sortable()
                    ->alignCenter()
                    ->color(function ($state) {
                        return match (true) {
                            $state >= 5.0 => 'success',
                            $state >= 4.0 => 'warning',
                            $state >= 3.0 => 'gray',
                            default => 'danger',
                        };
                    })
                    ->icon('heroicon-o-star')
                    ->iconColor(function ($state) {
                        return match (true) {
                            $state >= 5.0 => 'success',
                            $state >= 4.0 => 'warning',
                            $state >= 3.0 => 'gray',
                            default => 'danger',
                        };
                    }),

                TextColumn::make('reviews_count')
                    ->label('Reviews')
                    //->sortable()
                    ->alignCenter()
                    ->color('primary')
                    ->weight('bold'),

            ])
            ->filters([

                SelectFilter::make('id')
                    // ->relationship('product', 'prod_name')
                    ->label('Product Name')
                    ->options(Product::query()
                        ->pluck('prod_name', 'id'))
                    //->optionsLimit(5)
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('productCategories.prod_cat_name')
                    ->relationship('productCategories', 'prod_cat_name')
                    ->label('Product Category')
                    ->options(ProductCategory::query()->pluck('prod_cat_name', 'id'))
                    ->multiple()
                    //->optionsLimit(5)
                    ->preload()
                    ->searchable()
            ])
            ->filtersTriggerAction(
                fn(\Filament\Tables\Actions\Action $action) => $action
                    ->button()
                    ->slideOver()
                    ->label('Filter'),
            )
            ->paginated([3, 5, 10, 25, 50])
            ->emptyStateHeading('No Top Rates Product');
    }

    protected function getProductRatings()
    {
        return Product::query()
            ->withCount('reviews')
            ->get();
    }
}
