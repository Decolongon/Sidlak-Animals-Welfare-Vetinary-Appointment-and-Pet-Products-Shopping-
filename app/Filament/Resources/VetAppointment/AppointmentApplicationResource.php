<?php

namespace App\Filament\Resources\VetAppointment;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Enums\PaymentStatusEnum;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Grouping\Group;
use App\Enums\AppointmentStatusEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use App\Models\Appointment\Appointment;
use App\Models\Appointment\VetSchedule;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use App\Models\Appointment\AppointmentCategory;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Tables\Actions\Action as TableAction;
use App\Filament\Resources\VetAppointment\AppointmentApplicationResource\Pages;
use App\Filament\Resources\VetAppointment\AppointmentApplicationResource\RelationManagers;

class AppointmentApplicationResource extends Resource
{
    protected static ?string $model = Appointment::class;
    protected static ?string $navigationGroup = 'Vetinary Appointment';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Appointment Details')
                    ->schema([
                        Select::make('user_id')
                            ->label('Pet Owner')
                            ->relationship('user', 'name')
                            ->preload()
                            ->optionsLimit(5)
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn($record) => ucwords($record->name))
                            ->required(),



                    ]),

                Section::make('Pet Details')
                    ->schema([
                        Select::make('categories')
                            ->label('Choose Service you need')
                            ->optionsLimit(5)
                            ->preload()
                            ->multiple()
                            ->searchable()
                            ->relationship(name: 'categories', titleAttribute: 'appoint_cat_name')
                            ->getOptionLabelFromRecordUsing(fn($record) => ucwords($record->appoint_cat_name))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                function ($set, $state, $get) {
                                    // Calculate total amount based on selected categories
                                    $total = 0;

                                    if (!empty($state)) {
                                        // Get the selected category IDs
                                        $categoryIds = $state;
                                        //dd($categoryIds);
                                        // Fetch prices for selected categories
                                        $categories = \App\Models\Appointment\AppointmentCategory::whereIn('id', $categoryIds)
                                            ->get(['id', 'price']);
                                        foreach ($categories as $category) {
                                            $total += $category->price;
                                        }
                                    }

                                    // Set the total amount
                                    $set('total_amount', $total);
                                }
                            )
                            ->createOptionForm([
                                TextInput::make('appoint_cat_name')
                                    ->required()
                                    ->label('Appointment Category Name')
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('appoint_cat_slug', Str::slug($state)))
                                    ->unique(AppointmentCategory::class, 'appoint_cat_name', ignoreRecord: true)
                                    ->columnSpan(1),

                                TextInput::make('appoint_cat_slug')
                                    ->disabled()
                                    ->label('Appointment Category Slug')
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(AppointmentCategory::class, 'appoint_cat_slug', ignoreRecord: true)
                                    ->columnSpan(1),

                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(50)
                                    ->prefix('PHP')
                                    ->label('Service Price'),

                                MarkdownEditor::make('appoint_cat_description')
                                    ->label('Appointment Category Description')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),

                        ToggleButtons::make('appointment_status')
                            ->options(AppointmentStatusEnum::class)
                            ->default(AppointmentStatusEnum::Pending)
                            ->dehydrated()
                            ->inline()
                            ->required()
                            ->reactive()
                            ->label('Appointment Status'),

                        DateTimePicker::make('appoint_sched')
                            ->required()
                            ->seconds(false)
                            ->default(fn($record) => $record?->appoint_sched ?? now()->addDays(1))
                            ->minDate(now()->startOfDay())
                            ->hidden(fn($get) => $get('appointment_status') !== 'approved')
                            ->label('Schedule Date'),


                        ToggleButtons::make('payment_status')
                            ->options(PaymentStatusEnum::class)
                            ->default(PaymentStatusEnum::Pending)
                            ->dehydrated()
                            ->inline()
                            ->required()
                            ->label('Payment Status'),

                        TextInput::make('pet_name')
                            ->label('Pet Name')
                            ->required()
                            ->maxLength(255),

                        Select::make('pet_type')
                            ->label('Pet Type')
                            ->options([
                                'dog' => 'Dog',
                                'cat' => 'Cat',
                                'other' => 'Other',
                            ])
                            ->preload()
                            ->required(),

                        TextInput::make('pet_breed')
                            ->label('Pet Breed')
                            ->required()
                            ->maxLength(255),

                        Select::make('pet_gender')
                            ->label('Pet Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',

                            ])
                            ->preload()
                            ->required(),

                        TextInput::make('pet_weight')
                            ->label('Pet Weight (kg)')
                            ->required()
                            ->numeric(),

                        TextInput::make('pet_age')
                            ->label('Pet Age')
                            ->required()
                            ->numeric(),

                        Select::make('pet_age_unit')
                            ->options([
                                'years old' => 'Years',
                                'months' => 'Months',
                            ])
                            ->required()
                            ->label('Age (Years/Months)'),


                        ToggleButtons::make('isPetVaccinated')
                            ->label('Is Pet Vaccinated?')
                            ->boolean()
                            ->dehydrated()
                            ->grouped()
                            ->colors([
                                false => 'warning',
                                true => 'success',
                            ])
                            ->icons([
                                false => 'heroicon-m-x-circle',
                                true => 'heroicon-m-check-circle',
                            ])
                            ->default(false),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->default('Over the Counter')
                            ->required()
                            ->hiddenOn('edit')
                            ->options([
                                'Over the Counter' => 'Over the Counter',
                            ]),

                        TextInput::make('total_amount')
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->label('Total Amount'),


                    ])->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('created_at')
                    ->label('Date Created')
                    ->collapsible()
                    ->date(),
            ])
            ->columns([

                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Pet Owner')
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('user.email')
                    ->searchable()
                    ->sortable()
                    ->label('Email')
                    ->formatStateUsing(fn($state) => $state),



                Tables\Columns\TextColumn::make('categories.appoint_cat_name')
                    ->searchable()
                    ->wrap()
                    ->html()
                    ->color('info')
                    ->badge()
                    ->label('Service')
                    ->formatStateUsing(fn($state) => ucwords($state)),

                Tables\Columns\TextColumn::make('pet_name')
                    ->sortable()
                    ->searchable()
                    ->label('Pet Name')
                    ->formatStateUsing(fn($state) => ucfirst($state)),


                TextColumn::make('appointment_status')
                    ->label('Appointment Status')
                    ->formatStateUsing(fn($state) => AppointmentStatusEnum::tryFrom($state)?->getLabel() ?? 'Unknown') // display label halin sa AppointmentStatusEnum
                    ->color(fn($state) => AppointmentStatusEnum::tryFrom($state)?->getColor() ?? 'gray')
                    ->icon(fn($state) => AppointmentStatusEnum::tryFrom($state)?->getIcon() ?? null),

                TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->formatStateUsing(fn($state) => PaymentStatusEnum::tryFrom($state)?->getLabel() ?? 'Unknown') // display label halin sa AppointmentStatusEnum
                    ->color(fn($state) => PaymentStatusEnum::tryFrom($state)?->getColor() ?? 'gray')
                    ->icon(fn($state) => PaymentStatusEnum::tryFrom($state)?->getIcon() ?? null),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn($state) => (strtolower(trim($state)) !== 'over the counter' ? 'success' : 'info'))
                    ->formatStateUsing(fn($state) => (strtolower(trim($state)) === 'over the counter' ? ucwords($state) : 'E-wallets/' . ucfirst($state))),

                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2)),

                TextColumn::make('appoint_sched')
                    ->label('Appointment Date')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('M d, Y g:i A'))
            ])
            ->filters([

                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        PaymentStatusEnum::Pending->value => 'Pending',
                        PaymentStatusEnum::Completed->value => 'Completed',
                        PaymentStatusEnum::Failed->value => 'Failed',
                    ]),

                Tables\Filters\Filter::make('appointment_date')
                    ->form([
                        DatePicker::make('date')
                            ->label('Appointment Date')
                            // ->native(false)
                            // ->displayFormat('M d, Y')
                            ->closeOnDateSelection()
                            // nullable para ma clear ang filter
                            ->nullable()
                            ->live(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('appoint_sched', $date),
                            );
                    })
                    //if date is selected show the date else return null
                    ->indicateUsing(function (array $data): ?string {
                        if (!isset($data['date'])) {
                            return null;
                        }

                        return 'Appointment Date: ' . Carbon::parse($data['date'])->format('M d, Y');
                    })


            ])
            ->filtersTriggerAction(
                fn(TableAction $action) => $action
                    // ->slideOver()
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Action::make('update_appointment_status')
                        ->label('Update Status')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->requiresConfirmation()
                        ->tooltip('Update Appointment status')
                        ->modalHeading(fn($record) => 'Confirm Status Update')
                        ->modalDescription(fn($record) => 'Are you sure you want to update the status of ' . ucwords($record->user->name) . '?')
                        ->color('warning')
                        ->modalSubmitActionLabel('Confirm Update')
                        ->modalWidth('2xl')
                        ->form([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    ToggleButtons::make('appointment_status')
                                        ->options(AppointmentStatusEnum::class)
                                        ->default(fn($record) => $record->appointment_status)
                                        ->dehydrated()
                                        ->inline()
                                        ->reactive()
                                        ->required()
                                        ->label('Appointment Status')
                                        ->columnSpan(2),

                                    DateTimePicker::make('appoint_sched')
                                        ->required()
                                        ->seconds(false)
                                        ->default(fn($record) => $record->appoint_sched ?? now()->startOfDay())
                                        ->minDate(now()->startOfDay())
                                        ->maxDate(now()->addWeek()->endOfDay())
                                        ->hidden(fn(Get $get) => $get('appointment_status') !== 'approved')
                                        ->dehydrated()
                                        ->label('Schedule Date')
                                        ->columnSpan(1),

                                    ToggleButtons::make('payment_status')
                                        ->options(PaymentStatusEnum::class)
                                        ->default(fn($record) => $record->payment_status)
                                        ->dehydrated()
                                        ->inline()
                                        ->required()
                                        ->label('Payment Status')
                                        ->columnSpan(2), // Make this span both columns
                                ])

                        ])
                        ->action(function ($record, array $data) {
                            // $record->update([
                            //     'appointment_status' => $data['appointment_status'],
                            // ]);
                            $updateData = [
                                'appointment_status' => $data['appointment_status'],
                                'payment_status' => $data['payment_status'],
                                'appoint_sched' => $data['appoint_sched'] ?? null,
                            ];

                            // if($data['appointment_status'] === AppointmentStatusEnum::Approved) {
                            //     $updateData['appoint_sched'] = $data['appoint_sched'];
                            // }

                            $record->update($updateData);
                            Notification::make()
                                ->title('Updates Successfuly')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('update_appointment_status')
                        ->label('Update Status')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->requiresConfirmation()
                        ->tooltip('Update Appointment status')
                        ->modalHeading('Confirm Status Update')
                        ->modalDescription('Are you sure you want to update the status of all selected appointments?')
                        ->color('warning')
                        ->modalSubmitActionLabel('Confirm Update')
                        ->modalWidth('2xl')
                        ->form([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    ToggleButtons::make('appointment_status')
                                        ->options(AppointmentStatusEnum::class)
                                        ->inline()
                                        ->required()
                                        ->live()
                                        ->columnSpan(2)
                                        ->label('Appointment Status'),

                                    DateTimePicker::make('appoint_sched')
                                        ->required()
                                        ->seconds(false)
                                        // ->default(fn($record) => $record->appoint_sched ?? now()->addDays(1))
                                        ->minDate(now()->startOfDay())
                                        ->maxDate(now()->addWeek()->endOfDay())
                                        ->hidden(fn(Get $get) => $get('appointment_status') !== 'approved')
                                        ->dehydrated()
                                        ->columnSpan(1)
                                        ->label('Schedule Date'),

                                    ToggleButtons::make('payment_status')
                                        ->options(PaymentStatusEnum::class)
                                        // ->default(PaymentStatusEnum::Pending)
                                        ->dehydrated()
                                        ->inline()
                                        ->required()
                                        ->columnSpan(2)
                                        ->label('Payment Status'),

                                ])

                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->update([
                                    'appointment_status' => $data['appointment_status'],
                                    'payment_status' => $data['payment_status'],
                                    'appoint_sched' => $data['appoint_sched'] ?? null,
                                ]);
                            }
                            Notification::make()
                                ->title('Updates Successfuly')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-m-plus')
                    ->label(__('Create new Application')),
            ])->emptyStateIcon('heroicon-o-rectangle-stack')
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No Appointments');
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
            'index' => Pages\ListAppointmentApplications::route('/'),
            'create' => Pages\CreateAppointmentApplication::route('/create'),
            'view' => Pages\ViewRecords::route('/{record}'),
            'edit' => Pages\EditAppointmentApplication::route('/{record}/edit'),
        ];
    }

    public static function getRecordSubnavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewRecords::class,
            Pages\EditAppointmentApplication::class
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('pet_name')
                            ->label('Pet Name')
                            ->formatStateUsing(fn(string $state): string => ucwords($state))
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::ExtraBold),

                        TextEntry::make('pet_type')
                            ->label('Pet Type')
                            ->formatStateUsing(fn(string $state): string => ucfirst($state))
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::ExtraBold),

                        TextEntry::make('pet_breed')
                            ->label('Pet Breed')
                            ->formatStateUsing(fn(string $state): string => ucfirst($state))
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::ExtraBold),

                        TextEntry::make('pet_gender')
                            ->label('Pet Gender')
                            ->formatStateUsing(fn(string $state): string => ucfirst($state))
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::ExtraBold),

                        TextEntry::make('pet_age')
                            ->label('Pet Age')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::ExtraBold),

                        TextEntry::make('pet_weight')
                            ->label('Pet Weight (kg)')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::ExtraBold),

                        TextEntry::make('isPetVaccinated')
                            ->label('Is Pet Vaccinated?')
                            ->formatStateUsing(fn($state) => $state ? '✅ Yes' : '❌ No')

                    ])
            ]);
    }
}
