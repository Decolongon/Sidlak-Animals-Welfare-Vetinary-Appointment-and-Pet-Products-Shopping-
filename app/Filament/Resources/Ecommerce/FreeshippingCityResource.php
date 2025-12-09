<?php

namespace App\Filament\Resources\Ecommerce;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Ecommerce\FreeshippingCity;
use Woenel\Prpcmblmts\Models\PhilippineCity;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Ecommerce\FreeshippingCityResource\Pages;
use App\Filament\Resources\Ecommerce\FreeshippingCityResource\RelationManagers;
use Filament\Forms\Components\Toggle;

class FreeshippingCityResource extends Resource
{
    protected static ?string $model = FreeshippingCity::class;

    protected static ?string $navigationIcon = 'heroicon-m-truck';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('city_code')
                    ->label('City')
                    ->multiple()
                    ->options(
                        PhilippineCity::where('province_code', '0645')
                            ->get()
                            ->pluck('name', 'code')
                            ->toArray()

                        //->limit(10)
                    )
                    ->optionsLimit(5)
                    ->searchable()
                    ->required(),

                Toggle::make('is_active')->label('Is Active?')->default(true)->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('city_codes_display')
                    ->label('Cities')
                    ->searchable()
                    // ->sortable()
                    ->getStateUsing(function ($record) {
                        // Get the city codes from the record
                        $cityCodes = $record->city_code;

                        if (empty($cityCodes)) {
                            return 'No cities selected';
                        }

                        // Convert array to collection if it's not already
                        if (is_array($cityCodes)) {
                            $cityCodes = collect($cityCodes);
                        }

                        // Get city names from the codes
                        $cityNames = PhilippineCity::whereIn('code', $cityCodes)
                            ->pluck('name')
                            ->toArray();

                        return $cityNames;
                    })
                    ->html()
                    ->wrap()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->badge(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
                    // ->boolean()
                   // ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])


            ])
            ->emptyStateHeading('No Freeshipping Cities Found')
            ->emptyStateDescription('Create new Freeshipping City.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-m-truck')
                    ->label(__('Create Freeshipping City'))
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFreeshippingCities::route('/'),
            'create' => Pages\CreateFreeshippingCity::route('/create'),
            'edit' => Pages\EditFreeshippingCity::route('/{record}/edit'),
        ];
    }
}
