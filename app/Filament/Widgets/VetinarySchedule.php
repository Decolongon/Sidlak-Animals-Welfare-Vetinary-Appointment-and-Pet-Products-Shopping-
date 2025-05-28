<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Widgets\Widget;
use Filament\Actions\CreateAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use App\Models\Appointment\VetSchedule;
use Filament\Forms\Components\TextInput;
use Illuminate\Notifications\Notification;
use \Guava\Calendar\Widgets\CalendarWidget;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use Guava\Calendar\ValueObjects\CalendarEvent;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class VetinarySchedule extends CalendarWidget
{
    use HasWidgetShield;
    protected bool $eventClickEnabled = true;
    protected bool $eventResizeEnabled = true;
    // protected bool $dateClickEnabled = true;
    protected ?string $defaultEventClickAction = 'edit';
    //protected ?string $defaultEventClickAction = 'view';
    
    protected static ?int $sort = 5;
  
    public function getHeading(): string
    {
         return 'Vetinary Schedule';
    }

   
    public function getHeaderActions(): array
    {
        return [
            Action::make('create_sched')->label('Create Vetinary Schedule')
            ->icon('heroicon-s-plus-circle')
            ->form([
                   Section::make()
                ->schema([
                
           
                Hidden::make('user_id')
                ->default(auth()->user()->id),

                DateTimePicker::make('vet_schedule_open')
                ->required()
                ->rule('after_or_equal:now')
                ->native(false)
                 ->seconds(false)
                ->date('F j, Y, g:i a')
               
                ->hint('Must be today or later')
                ->hintColor('warning')
                ->label('Opening Time'),

                DateTimePicker::make('vet_schedule_close')
                ->required()
                ->seconds(false)
                ->native(false) 
                ->date('F j, Y, g:i a')
                ->rule(function ($get) {
                    $open = $get('vet_schedule_open');
            
                    return function ($attribute, $value, $fail) use ($open) {
                        if (!$open || !$value) return;
            
                        $openTime = Carbon::parse($open);
                        $closeTime = Carbon::parse($value);
                    //if ang user select data greater than the opening time throw error message
                        if ($openTime->toDateString() !== $closeTime->toDateString()) {
                            $fail('The closing time must be on the same date as the opening time.');
                            //if ang time is less than or equal sa opening time throw error message
                        } elseif ($closeTime->lessThanOrEqualTo($openTime)) {
                            $fail('The closing time must be after the opening time.');
                        }
                    };
                })
                ->hint('Must be after opening time, but not later than now.')
                ->hintColor('warning')
                ->label('Closing Time'),

                // ToggleButtons::make('is_the_same_schedule')
                // ->required()
                // ->boolean()
                // ->label('Is the same schedule every day?')
                // ->grouped()
                // ->colors([
                //     false => 'warning',
                //     true => 'success',
                // ])
                // ->icons([
                //     false => 'heroicon-m-x-circle',
                //     true => 'heroicon-m-check-circle',
                // ])
                // ->default(false),

                TextInput::make('num_customers')
                ->label('Enter the number of appointments you wish to accommodate')
                ->required()
                ->columnSpanfull()
                ->rules([
                   'min:1',
                   'max:50',
                ])
                ->numeric(),
               
                
            ])->columns(2),
            ])
            ->action(function (array $data, $record = null) {
           
                $schedule = $record ?? new VetSchedule();

                $schedule->user_id = Auth::id();
                $schedule->vet_schedule_open = $data['vet_schedule_open'];
                $schedule->vet_schedule_close = $data['vet_schedule_close'];
                //$schedule->is_the_same_schedule = $data['is_the_same_schedule'];
                $schedule->num_customers = $data['num_customers'];
                $schedule->save();
               $this->refreshRecords();
                return $schedule;
                
                
        })
       
      
       
        ];
    }

   public function getOptions(): array
    {
        return [
            'nowIndicator' => true,
            'slotDuration' => '00:15:00',
           
      
        ];
    }
     public function getEvents(array $fetchInfo = []): Collection | array
    {
        return VetSchedule::get(['id', 'vet_schedule_open', 'vet_schedule_close','num_customers']);
         
       
    }

    public function getSchema(?string $model = null): ?array
{
    // If you only work with one model type, you can ignore the $model parameter and simply return a schema
    // return [
    //     TextInput::make('title')
    // ];
 
    // If you have multiple model types on your calendar, you can return different schemas based on the $model property
    return match($model) {
        VetSchedule::class => [

              Section::make()
                ->schema([
                
           
                Hidden::make('user_id')
                ->default(auth()->user()->id),

                DateTimePicker::make('vet_schedule_open')
                ->required()
                //->displayFormat('F j, Y h:i A') //format full year, day, month, time
                ->rule('after_or_equal:now')
                 ->native(false)
                 ->seconds(false)
                ->date('F j, Y, g:i a')
               
                ->hint('Must be today or later')
                ->hintColor('warning')
                ->label('Opening Time'),

                DateTimePicker::make('vet_schedule_close')
                ->required()
                ->seconds(false)
                ->native(false) 
                  ->date('F j, Y, g:i a')
                // ->displayFormat('F j, Y h:i A')
                // ->rule(function ( $get) {
                //     $openTime = $get('vet_shedule_open');
                   
                //     //dapat ang closing sched is greater than sa opening sched pero nd sa dapat mag lapaw sa date subong
                //     return [
                //         'after:' . $openTime,
                //     ];
                // })
                ->rule(function ($get) {
                    $open = $get('vet_schedule_open');
            
                    return function ($attribute, $value, $fail) use ($open) {
                        if (!$open || !$value) return;
            
                        $openTime = Carbon::parse($open);
                        $closeTime = Carbon::parse($value);
                    //if ang user select data greater than the opening time throw error message
                        if ($openTime->toDateString() !== $closeTime->toDateString()) {
                            $fail('The closing time must be on the same date as the opening time.');
                            //if ang time is less than or equal sa opening time throw error message
                        } elseif ($closeTime->lessThanOrEqualTo($openTime)) {
                            $fail('The closing time must be after the opening time.');
                        }
                    };
                })
                ->hint('Must be after opening time, but not later than now.')
                ->hintColor('warning')
                ->label('Closing Time'),

                // ToggleButtons::make('is_the_same_schedule')
                // ->required()
                // ->boolean()
                // ->label('Is the same schedule every day?')
                // ->grouped()
                // ->colors([
                //     false => 'warning',
                //     true => 'success',
                // ])
                // ->icons([
                //     false => 'heroicon-m-x-circle',
                //     true => 'heroicon-m-check-circle',
                // ])
                // ->default(false),

                TextInput::make('num_customers')
                ->label('Enter the number of appointments you wish to accommodate')
                ->required()
                ->columnSpanfull()
                ->rules([
                   'min:1',
                   'max:50',
                ])
                ->numeric(),
               
                
            ])->columns(1),


        ],
       
    };
}

    public function getEventClickContextMenuActions(): array
    {
        return [
            $this->viewAction(),
            $this->editAction(),
            $this->deleteAction(),
        ];
    }
   
}
