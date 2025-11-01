<?php

namespace App\Filament\Resources\Ecommerce\ProductResource\Pages;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Ecommerce\ProductResource;
use IbrahimBougaoua\FilamentRatingStar\Forms\Components\RatingStar;
use IbrahimBougaoua\FilamentRatingStar\Columns\Components\RatingStar as TableRatingStar;

class ProductReviews extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'reviews';

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function getNavigationLabel(): string
    {
        return 'View Product Reviews';
    }

    public function form(Form $form): Form
    {
       return $form
            ->schema([
                Section::make('Product Review Information')
                    ->schema([
                        Grid::make()
                            ->schema([
                                // Left column fields
                                Group::make()
                                    ->schema([
                                        Select::make('user_id')
                                            ->relationship('user', 'name')
                                            ->required()
                                            ->searchable()
                                            ->optionsLimit(5)
                                            ->preload()
                                            ->getOptionLabelFromRecordUsing(fn($record) => ucwords($record->name)),

                                        Select::make('product_id')
                                            ->relationship('product', 'prod_name')
                                            ->required()
                                            ->searchable()
                                            ->optionsLimit(5)
                                            ->preload()
                                            ->getOptionLabelFromRecordUsing(fn($record) => ucwords($record->prod_name)),

                                        RatingStar::make('rating')
                                            ->required()
                                            // ->min(1)
                                            ->label('Rating'),

                                        Textarea::make('review')
                                            //->required()
                                            ->maxLength(255)
                                            ->label('Review'),
                                    ])
                                    ->columnSpan(1), // Left side

                                // Right column field (File Upload only)
                                Group::make()
                                    ->schema([
                                        FileUpload::make('image_review')
                                            ->label('Image Review')
                                            ->image()
                                            ->multiple()
                                            ->nullable()
                                            ->formatStateUsing(fn($state) => is_array($state) ? $state : json_decode($state, true))
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                null,
                                                '16:9',
                                                '4:3',
                                            ])
                                            ->maxSize(2048),
                                    ])
                                    ->columnSpan(1), // Right side
                            ])
                            ->columns(2), // Two equal columns
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('rating')
            ->columns([
                 TextColumn::make('user.name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('review')
                    ->placeholder('No review')
                    ->limit(20)
                    ->label('Review'),
                  

                TableRatingStar::make('rating')
                    ->size('sm')
                   // ->summarize(\Filament\Tables\Columns\Summarizers\Average::make())
                    ->label('Rating'),

                // TextColumn::make('review')
                // ->label('Review'),

                ImageColumn::make('image_review')
                    ->circular()
                    ->height(50)
                    ->limit(1)
                    ->width(50)
                    ->placeholder('No image uploaded')
                    ->label('Image Review'),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('M d, Y g:i A'))
                    ->sortable(),


            ])
            ->filters([
                //Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                //Tables\Actions\EditAction::make(),
               
            ])
            ->bulkActions([
             
            ])
            ->emptyStateIcon('heroicon-o-star')
            ->emptyStateHeading('No Reviews Found')
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
