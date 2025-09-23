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
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Navigation\NavigationItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Split;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Infolists\Components\ImageEntry;
use App\Models\Appointment\AppointmentCategory;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\Section as ComponentsSection;
use App\Filament\Resources\VetAppointment\AppointmentCategoryResource\Pages;
use App\Filament\Resources\VetAppointment\AppointmentCategoryResource\RelationManagers\DoctorschedulesRelationManager;


class AppointmentCategoryResource extends Resource
{
    protected static ?string $model = AppointmentCategory::class;
    protected static ?string $navigationGroup = 'Vetinary Appointment';
    protected static ?string $modelLabel = 'Service'; // Singular name para sa form
    protected static ?string $pluralModelLabel = 'Services Offered'; // Plural name para sa table
    protected static ?string $navigationLabel = 'Services Offered';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Hidden::make('doctor_id')
                                    ->default(auth()->user()->id)
                                    ->columnSpanFull(),

                                TextInput::make('appoint_cat_name')
                                    ->required()
                                    ->label('Service Name')
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('appoint_cat_slug', Str::slug($state)))
                                    ->unique(AppointmentCategory::class, 'appoint_cat_name', ignoreRecord: true)
                                    ->columnSpanFull(),

                                TextInput::make('appoint_cat_slug')
                                    ->disabled()
                                    ->label('Service Slug')
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(AppointmentCategory::class, 'appoint_cat_slug', ignoreRecord: true)
                                    ->columnSpanFull(),

                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(50)
                                    ->prefix('PHP')
                                    ->label('Service Price')
                                    ->columnSpanFull(),

                                MarkdownEditor::make('appoint_cat_description')
                                    ->label('Service Description')
                                    ->required()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(['lg' => 2]),

                        Section::make()
                            ->schema([
                                FileUpload::make('img')
                                    ->label('Image')
                                    ->image()
                                    ->imageEditorAspectRatios([null, '16:9', '4:3'])
                                    ->maxSize(2048)
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
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
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                TextColumn::make('price')
                    ->sortable()
                    ->label('Service Price')
                    ->badge()
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2)),

                Tables\Columns\TextColumn::make('appoint_cat_description')
                    ->formatStateUsing(fn($state) => ucfirst(Str::limit(strip_tags($state), 50, '...')))
                    ->label('Services Description')

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
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasAnyRole(['super_admin', 'super-admin'])) {
                    return $query;
                }
                return $query->where('doctor_id', auth()->user()->id);
            })
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
            DoctorschedulesRelationManager::class
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
                        Split::make([
                            InfoSection::make([
                                TextEntry::make('appoint_cat_name')
                                    ->label('Service Name')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('price')
                                    ->label('Service Price')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2))
                                    ->color('primary'),

                                TextEntry::make('appoint_cat_description')
                                    ->label('Service Description')
                                    ->markdown()
                                    ->prose(),
                            ]),
                            InfoSection::make([
                                ImageEntry::make('img')
                                    ->label('Image')
                            ])->grow(false),
                        ])->from('md')

                    ])
            ]);
    }
}
