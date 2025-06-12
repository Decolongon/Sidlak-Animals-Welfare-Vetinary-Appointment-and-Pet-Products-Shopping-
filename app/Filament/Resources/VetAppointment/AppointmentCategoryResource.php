<?php

namespace App\Filament\Resources\VetAppointment;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Filament\Navigation\NavigationItem;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\MarkdownEditor;
use App\Models\Appointment\AppointmentCategory;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\Section as ComponentsSection;
use App\Filament\Resources\VetAppointment\AppointmentCategoryResource\Pages;
use App\Filament\Resources\VetAppointment\AppointmentCategoryResource\RelationManagers;

class AppointmentCategoryResource extends Resource
{
    protected static ?string $model = AppointmentCategory::class;
    protected static ?string $navigationGroup = 'Vetinary Appointment';
     protected static ?string $navigationLabel = 'Services Offered';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('appoint_cat_name')
                ->required()
                ->label('Service Name')
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('appoint_cat_slug', Str::slug($state)))
                ->unique(AppointmentCategory::class, 'appoint_cat_name', ignoreRecord: true)
                ->columnSpan(1),

                TextInput::make('appoint_cat_slug')
                ->disabled()
                ->label('Service Slug')
                ->dehydrated()
                ->required()
                ->maxLength(255)
                ->unique(AppointmentCategory::class, 'appoint_cat_slug', ignoreRecord: true)
                ->columnSpan(1),

                MarkdownEditor::make('appoint_cat_description')
                ->label('Service Description')
                ->maxLength(65535)
                ->columnSpanFull(),

                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('appoint_cat_name')
                ->searchable()
                ->sortable()
                ->label('Services')    
                ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('appoint_cat_description')
                ->formatStateUsing(fn ($state) => ucfirst(Str::limit(strip_tags($state), 50, '...')))
                ->label('Sercvices Description')

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
            ])->emptyStateActions([
                Tables\Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label(__('Create New Service')),
            ])->emptyStateIcon('heroicon-o-folder')
            ->emptyStateHeading('No Services Found')
            ->emptyStateDescription('Get started by creating a new service.');
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
            'index' => Pages\ListAppointmentCategories::route('/'),
            'create' => Pages\CreateAppointmentCategory::route('/create'),
            'view' => Pages\ViewAppointmentCategory::route('/{record}'),
            'edit' => Pages\EditAppointmentCategory::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubnavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewAppointmentCategory::class,
            Pages\EditAppointmentCategory::class
        ]);

        
    }



    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make()
                ->schema([
                    TextEntry::make('appoint_cat_name')
                    ->label('Appointment Category Name')
                    ->formatStateUsing(fn (string $state) : string => ucwords($state))
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::ExtraBold),

                    

                    ComponentsSection::make('Appointment Category Details')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                      
                        TextEntry::make('appoint_cat_description')
                            ->label('')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->html()
                            // ->formatStateUsing(fn ($state) => strip_tags($state))
                            ->weight(FontWeight::ExtraBold),
                        
                    ])->collapsible(),

                ])
            ]);
    }



}
