<?php

namespace App\Filament\Resources\Ecommerce;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use App\Models\Ecommerce\ProductReview;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use App\Models\Ecommerce\ProductReviews;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Ecommerce\ProductReviewsResource\Pages;
// use IbrahimBougaoua\FilamentRatingStar\Columns\Components\RatingStar;
use IbrahimBougaoua\FilamentRatingStar\Columns\Components\RatingStar as TableRatingStar;
use IbrahimBougaoua\FilamentRatingStar\Entries\Components\RatingStar as InfolistRatingStar;
use IbrahimBougaoua\FilamentRatingStar\Forms\Components\RatingStar;
use App\Filament\Resources\Ecommerce\ProductReviewsResource\RelationManagers;

class ProductReviewsResource extends Resource
{
    protected static ?string $model = ProductReview::class;
    protected static ?string $navigationGroup = 'Ecommerce';
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Review Information')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->required()
                        ->searchable()
                        ->optionsLimit(5)
                        ->preload()
                        ->getOptionLabelFromRecordUsing(fn ($record) => ucwords($record->name)),

                    Select::make('product_id')
                        ->relationship('product', 'prod_name')
                        ->required()
                        ->searchable()
                        ->optionsLimit(5)
                        ->preload()
                        ->getOptionLabelFromRecordUsing(fn ($record) => ucwords($record->prod_name)),

                        // 'product_id',
                        // 'user_id',
                        // 'rating',
                        // 'image_review',
                        // 'review',
                   RatingStar::make('rating')
                        ->required()
                        ->label('Rating'),

                    Textarea::make('review')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan('full')
                        ->label('Review'),

                    FileUpload::make('image_review')
                        ->label('Image Review')
                        ->image()
                        ->multiple()
                        ->nullable()
                        //check if state is array kng array na nga daan return as it is else convert json to array
                        ->formatStateUsing(fn ($state) => is_array($state) ? $state : json_decode($state, true))
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            null,
                            '16:9',
                            '4:3',
                    ])->maxSize(2048),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                ->label('Customer Name')
                ->searchable()
                ->sortable()
                ->searchable(),

                TextColumn::make('product.prod_name')
                ->sortable()
                ->searchable()
                ->label('Product Name')
                ->searchable(),

                TableRatingStar::make('rating')
                 ->size('sm')
                ->label('Rating'),

                // TextColumn::make('review')
                // ->label('Review'),

                ImageColumn::make('image_review')
                ->circular()
                ->height(50)
                ->limit(1)
                ->width(50)
                ->label('Image Review'),

                TextColumn::make('created_at')
                ->label('Created At')
                ->sortable()
                ->dateTime(),
            ])
            ->filters([
               
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
            ])->emptyStateActions([
                Tables\Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label(__('Create Product Review')),
            ])
            ->emptyStateIcon('heroicon-o-star')
            ->emptyStateHeading('No Product Reviews');
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
            'index' => Pages\ListProductReviews::route('/'),
            'create' => Pages\CreateProductReviews::route('/create'),
            'edit' => Pages\EditProductReviews::route('/{record}/edit'),
        ];
    }
}
