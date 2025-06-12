<?php

namespace App\Filament\Resources\Ecommerce;

use DateTime;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Models\Ecommerce\ProductDiscount;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Ecommerce\ProductDiscountResource\Pages;
use App\Filament\Resources\Ecommerce\ProductDiscountResource\RelationManagers;
use App\Filament\Resources\Ecommerce\ProductDiscountResource\RelationManagers\ProductsRelationManager;

class ProductDiscountResource extends Resource
{
    protected static ?string $model = ProductDiscount::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-pound';
    protected static ?string $navigationGroup = 'Ecommerce';
    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
          Section::make()
            ->schema([
                TextInput::make('discount_name')
                ->required()
                ->unique(ProductDiscount::class, 'discount_name', ignoreRecord: true)
                ->label('Discount Name')
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('discount_slug', Str::slug($state)))
                ->maxLength(255),


                TextInput::make('discount_slug')
                ->disabled()
                ->dehydrated()
                ->required()
                ->unique(ProductDiscount::class, 'discount_slug', ignoreRecord: true)
                ->label('Discount Slug'),

                MarkdownEditor::make('desc_discount')
                ->label('Discount Description')
                ->columnSpanFull()
                ->maxLength(65535),

             ])->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 3,
             ]),

             Group::make([
                Section::make()
                ->schema([
                    DateTimePicker::make('start_at')
                    ->label('Start Date')
                    // ->native(false)
                    ->seconds(false)
                    ->live()
                    ->minDate(now()->startOfDay()) // allow today
                    ->rules(['after_or_equal:now' ]) // must be today or later
                    ->date('F j, Y, g:i a')
                    ->required(),

                    DateTimePicker::make('end_at')
                    ->label('End Date')
                    //->native(false)
                    ->seconds(false)
                    ->minDate(fn (callable $get) => $get('start_at')) // live binding
                    ->rules(['after:start_at']) // must be after start_at
                    ->date('F j, Y, g:i a')
                    ->required(),
                ])
             ])
               
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('discount_name')
                ->label('Discount Name')
                ->formatStateUsing(fn ($state) => Str::title($state))
                ->sortable()
                ->searchable(),

                TextColumn::make('desc_discount')
                ->label('Description'),

                TextColumn::make('start_at')
                ->label('Start Date')
                ->formatStateUsing(fn ($state) => date('F j, Y h:i A', strtotime($state))),

                TextColumn::make('end_at')
                ->label('End Date')
                ->formatStateUsing(fn ($state) => date('F j, Y h:i A', strtotime($state))),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

               Tables\Actions\ActionGroup::make([
                 Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                   
                ])->tooltip('Actions')
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                ->icon('heroicon-m-plus-circle')
                ->label(__('New Product Discount')),
            ])
            ->emptyStateIcon('heroicon-o-percent-badge')  
            ->emptyStateHeading('No Discount Available');
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductDiscounts::route('/'),
            'create' => Pages\CreateProductDiscount::route('/create'),
            'edit' => Pages\EditProductDiscount::route('/{record}/edit'),
        ];
    }
}
