<?php

namespace App\Filament\Resources\Ecommerce\ProductDiscountResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                 Radio::make('discount_type')
                ->label('Discount Type')
                ->required()
                ->inline()
                ->inlineLabel(false)
                ->live()
                ->options([
                    'fixed' => 'Fixed',
                    'percent' => 'Percent',
                ])
                ->descriptions([
                    'fixed' => 'Fixed amount',
                    'percent' => 'Percent',
                ])
                ->columnSpanFull(),

                Group::make([
                     TextInput::make('percentage_value')
                    ->label('% Value')
                    ->suffix(' % OFF')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->hidden(fn (Get $get): bool => $get('discount_type') !== 'percent')
                    ->afterStateUpdated(function(string $state, Forms\Set $set, Forms\Get $get) {
                        if($get('discount_type') === 'percent') {
                            $set('discounted_price', $state);
                        }
                    })
                    ->live(onBlur: true)
                    ->afterStateHydrated(function(Set $set, Get $get, $state, $record) {
                        if ($record && $get('discount_type') === 'percent') {
                            $set('percentage_value', $record->pivot->discounted_price);
                        }
                    }),

                    TextInput::make('fixed_value')
                    ->label('Fixed Value')
                    ->required()
                    ->numeric()
                    ->live(onBlur: true)
                    ->hidden(fn (Get $get): bool => $get('discount_type') !== 'fixed')
                    ->afterStateUpdated(function(string $state, Forms\Set $set, Forms\Get $get) {
                        if($get('discount_type') === 'fixed') {
                            $set('discounted_price', $state);
                        }
                    })
                    ->afterStateHydrated(function(Set $set, Get $get, $state, $record) {
                        if ($record && $get('discount_type') === 'fixed') {
                            $set('fixed_value', $record->pivot->discounted_price);
                        }
                    }),
                ])
            ]);
    }

  

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('prod_name')
            ->columns([
                Tables\Columns\TextColumn::make('discount_code'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
