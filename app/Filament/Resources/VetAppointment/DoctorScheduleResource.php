<?php

namespace App\Filament\Resources\VetAppointment;

use Closure;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Enums\DayEnum;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Appointment\DoctorSchedule;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VetAppointment\DoctorScheduleResource\Pages;
use App\Filament\Resources\VetAppointment\DoctorScheduleResource\RelationManagers;

class DoctorScheduleResource extends Resource
{
    protected static ?string $model = DoctorSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Vetinary Appointment';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Hidden::make('doctor_id')
                    ->default(fn () => auth()->user()->hasAnyRole(['admin_vet']) ? auth()->user()->id : null)
                    ->disabled(function(){
                        if(auth()->user()->hasAnyRole(['super_admin','super-admin', 'admin_vet'])){
                            return true;
                        }
                        return false;
                    })
                    ->dehydrated()
                    ->columnSpanFull(),

                Section::make('Availability Schedule')
                    ->description('Set your working days and hours')
                    ->schema([
                        Select::make('doctor_id')
                            ->options(User::role('admin_vet')->pluck('name', 'id'))
                            ->label('Veterinarian')
                            ->required()
                            //->hiddenOn('edit')
                            ->disabled(function(){
                                if(auth()->user()->hasAnyRole(['secretary_vet','super_admin','super-admin'])){
                                    return false;
                                }
                                return true;
                            })
                            ->hidden(function(){
                                if(auth()->user()->hasAnyRole(['admin_vet'])){
                                    return true;
                                }
                                return false;
                            })
                            ->dehydrated()
                            // ->hidden(function(){
                            //     if(auth()->user()->hasRole(['admin_vet'])) {
                            //         return true;
                            //     }
                            //     return false;
                            // })
                            ->optionsLimit(5)
                            ->searchable(),
                            
                        Fieldset::make('Working Days')
                            ->schema([
                                CheckboxList::make('days')
                                    ->required()
                                    ->label('Select working days')
                                    ->options(DayEnum::class)
                                    ->columns(3)
                                    ->gridDirection('row')
                                    ->bulkToggleable()
                            ]),

                        Fieldset::make('Working Hours')
                            ->schema([
                                TimePicker::make('start_time')
                                    ->seconds(false)
                                    ->label('Start Time')
                                    ->required()
                                    ->live()
                                    ->prefixIcon('heroicon-m-clock')
                                    ->placeholder('Select start time'),

                                TimePicker::make('end_time')
                                    ->label('End Time')
                                    ->seconds(false)
                                    ->required()
                                    ->prefixIcon('heroicon-m-clock')
                                    ->placeholder('Select end time')
                                    ->rules([
                                        function ($get) {
                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                $startTime = $get('start_time');
                                                if ($value && $startTime && strtotime($value) <= strtotime($startTime)) {
                                                    $fail('End Time must be after Start Time.');
                                                }
                                            };
                                        }
                                    ]),
                            ]),

                        Fieldset::make('Schedule Validity')
                            ->schema([
                                DatePicker::make('effective_from')
                                    ->label('Effective From')
                                    ->minDate(now()->startOfDay())
                                    //->unique(ignoreRecord: true)
                                    ->required()
                                    ->live()
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->placeholder('Select start date')
                                    ->helperText('When this schedule becomes active'),

                                DatePicker::make('effective_to')
                                    ->label('Effective To')
                                    ->minDate(now()->startOfDay())
                                    ->required()
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->placeholder('Select end date')
                                    ->helperText('When this schedule expires')
                                    ->rules([
                                        function ($get) {
                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                $effectiveFrom = $get('effective_from');
                                                if ($value && $effectiveFrom && strtotime($value) <= strtotime($effectiveFrom)) {
                                                    $fail('Effective To must be after Effective From.');
                                                }
                                            };
                                        }
                                    ])
                                    ->minDate(now()->startOfDay()),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doctor.name')
                ->searchable()
                ->label('Doctor Name'),

                TextColumn::make('days')
                    ->label('Days')
                    ->wrap()
                    ->html()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->formatStateUsing(fn($state) => ucwords($state))
                    ->badge(),

                TextColumn::make('start_time')
                    ->label('Start Time')
                    ->formatStateUsing(fn($state) => date('h:i A', strtotime($state))),

                TextColumn::make('end_time')
                    ->label('End Time')
                    ->formatStateUsing(fn($state) => date('h:i A', strtotime($state))),

                TextColumn::make('effective_from')
                    ->label('Effective From')
                    ->formatStateUsing(fn($state) => date('F j, Y', strtotime($state))),

                TextColumn::make('effective_to')
                    ->label('Effective To')
                    ->formatStateUsing(fn($state) => date('F j, Y', strtotime($state))),
            ])
            ->filters([
                //
            ])
            ->modifyQueryUsing(function () {
                if (auth()->user()->hasAnyRole(['super_admin', 'super-admin','secretary_vet'])) {
                    return DoctorSchedule::query();
                }
                return DoctorSchedule::query()->where('doctor_id', auth()->user()->id);
            })
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\EditAction::make(),
                ])->tooltip('Actions'),

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
            'index' => Pages\ListDoctorSchedules::route('/'),
            'create' => Pages\CreateDoctorSchedule::route('/create'),
            'edit' => Pages\EditDoctorSchedule::route('/{record}/edit'),
        ];
    }
}
