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
        return $this->getProductTab();
    }
   
    protected function getProductTab():array
    {
        return [
            'All' => Tab::make()
                    ->badge(Product::count()),
                    

            'In Stock' => Tab::make()
                        ->modifyQueryUsing(fn ($query)=> $query->InStock())
                        ->badge(Product::InStock()->count()),

            'Low In Stock' => Tab::make()
                        ->modifyQueryUsing(fn ($query)=> $query->LowInStock())
                        ->badge(Product::LowInStock()->count()),
                         
            'Out of Stock' => Tab::make()
                        ->modifyQueryUsing(fn ($query)=> $query->OutOfStock())                                  
                        ->badge(Product::OutOfStock()->count()), 
                        
            'With Variants' => Tab::make()
                        ->modifyQueryUsing(fn ($query)=> $query->WithVariants())
                        ->badge(Product::WithVariants()->count()), 
        ];
    }
}
