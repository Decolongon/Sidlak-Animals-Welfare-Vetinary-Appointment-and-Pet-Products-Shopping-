<?php

namespace App\Filament\Resources\Ecommerce\ProductDiscountResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Ecommerce\Product;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Filters\SelectFilter;
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
                        'percent' => 'Percentage off',
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
                        ->hidden(fn(Get $get): bool => $get('discount_type') !== 'percent')
                        ->afterStateUpdated(function (string $state, Forms\Set $set, Forms\Get $get) {
                            if ($get('discount_type') === 'percent') {
                                $set('discounted_price', $state);
                            }
                        })
                        ->live(onBlur: true)
                        ->afterStateHydrated(function (Set $set, Get $get, $state, $record) {
                            if ($record && $get('discount_type') === 'percent') {
                                $set('percentage_value', $record->pivot->discounted_price);
                            }
                        }),

                    TextInput::make('fixed_value')
                        ->label('Fixed Value')
                        ->prefix('PHP')
                        ->required()
                        ->numeric()
                        ->live(onBlur: true)
                        ->hidden(fn(Get $get): bool => $get('discount_type') !== 'fixed')
                        ->afterStateUpdated(function (string $state, Forms\Set $set, Forms\Get $get) {
                            if ($get('discount_type') === 'fixed') {
                                $set('discounted_price', $state);
                            }
                        })
                        ->afterStateHydrated(function (Set $set, Get $get, $state, $record) {
                            if ($record && $get('discount_type') === 'fixed') {
                                $set('fixed_value', $record->pivot->discounted_price);
                            }
                        }),

                    Hidden::make('discounted_price')
                        ->required()
                        ->dehydrated()

                ])
            ]);
    }



    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('prod_name')
            ->columns([
                TextColumn::make('prod_name')->label('Product Name'),
                TextColumn::make('pivot.discount_type')->label('Type'),
                TextColumn::make('pivot.discounted_price')
                    ->label('Discounted Price')
                    ->badge()
                    ->color(fn($record) => match ($record->pivot->discount_type) {
                        'fixed' => 'success',
                        'percent' => 'warning',
                    })
                    ->formatStateUsing(function ($state, $record) {
                        $type = $record->pivot->discount_type;
                        if ($type === 'percent') {
                            return (int)$state . '% OFF';
                        } elseif ($type === 'fixed') {
                            return '₱' . number_format($state, 2) . ' OFF';
                        }
                        return $state;
                    }),
            ])
            ->filters([
                SelectFilter::make('discount_type')
                    ->label('Discount Type')
                    ->options([
                        'fixed' => 'Fixed',
                        'percent' => 'Percent',
                    ])
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                     ->recordSelectOptionsQuery(fn (Builder $query) => $query->where('prod_unit', '!=', 'diff_size'))
                    ->preloadRecordSelect()
                    ->slideOver()
                    ->button('Attach')
                    ->icon('heroicon-s-plus-circle')
                    ->color('primary')
                    ->multiple()
                    ->label('Attach Product')
                    ->form(fn(AttachAction $action): array => [

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
                                ->maxValue(60)
                                ->hidden(fn(Get $get): bool => $get('discount_type') !== 'percent')
                                ->afterStateUpdated(function (string $state, Forms\Set $set, Forms\Get $get) {
                                    if ($get('discount_type') === 'percent') {
                                        $set('discounted_price', $state);
                                    }
                                })
                                ->live(onBlur: true)
                                ->afterStateHydrated(function (Set $set, Get $get, $state, $record) {
                                    if ($record && $get('discount_type') === 'percent') {
                                        $set('percentage_value', $record->pivot->discounted_price);
                                    }
                                }),

                            TextInput::make('fixed_value')
                                ->label('Fixed Value')
                                ->required()
                                ->prefix('PHP')
                                ->numeric()
                                ->maxValue(150)
                                ->live(onBlur: true)
                                ->hidden(fn(Get $get): bool => $get('discount_type') !== 'fixed')
                                ->afterStateUpdated(function (string $state, Forms\Set $set, Forms\Get $get) {
                                    if ($get('discount_type') === 'fixed') {
                                        $set('discounted_price', $state);
                                    }
                                })
                                ->afterStateHydrated(function (Set $set, Get $get, $state, $record) {
                                    if ($record && $get('discount_type') === 'fixed') {
                                        $set('fixed_value', $record->pivot->discounted_price);
                                    }
                                }),

                            Hidden::make('discounted_price')
                                ->required()
                                ->dehydrated()

                        ]),

                        $action->getRecordSelect(),
                    ]),
            ])
            ->deferLoading()
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])->emptyStateActions([
                Tables\Actions\AttachAction::make()
                    ->color('primary')
                    ->icon('heroicon-m-plus-circle')
                    ->label(__('Attach Product')),
            ])->emptyStateIcon('heroicon-o-swatch')
            ->emptyStateHeading('No Products Attached')
            ->emptyStateDescription('Attach product to this discount.');
    }
}
