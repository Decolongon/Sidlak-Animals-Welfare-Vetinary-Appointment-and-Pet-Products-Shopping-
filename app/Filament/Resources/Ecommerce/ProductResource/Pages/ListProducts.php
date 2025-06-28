<?php

namespace App\Filament\Resources\Ecommerce\ProductResource\Pages;


use Filament\Tables;
use Filament\Actions;
use App\Models\Ecommerce\Product;
use Filament\Resources\Components\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Filament\Resources\Ecommerce\ProductResource;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Product')->icon('heroicon-m-plus-circle'),
        ];
    }


    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                    ->badge(Product::count()),
                    

            'In Stock' => Tab::make()
                        ->query(fn ($query)=> $query->where('prod_quantity', '>', 10))
                        ->badge(Product::where('prod_quantity', '>',10)->count()),

            'Low In Stock' => Tab::make()
                        ->query(fn ($query)=> $query->where('prod_quantity', '>', 0)
                                                     ->where('prod_quantity', '<=', 10)
                                                  
                        )
                        ->badge(Product::where('prod_quantity', '>',0)
                            ->where('prod_quantity', '<=', 10)
                            ->count()
                        ),

            'Out of Stock' => Tab::make()
                        ->query(fn ($query)=> $query->where('prod_quantity', 0))
                        ->badge(Product::where('prod_quantity', 0)->count()),   
        ];
    }
   


}
