<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Enums\AppointmentStatusEnum;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment\Appointment;
use Filament\Notifications\Notification;

use Filament\Notifications\Actions\Action;
use App\Mail\VetAppointmentStatusUpdatedMail;
use App\Models\Appointment\AppointmentCategory;
use App\Filament\Resources\VetAppointment\AppointmentApplicationResource;

class VetAppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        //  if (AppointmentStatusEnum::tryFrom($appointment->appointment_status) === AppointmentStatusEnum::Pending) {
       
        if ($appointment->user) {

            Mail::to($appointment->user->email)
                ->send(new VetAppointmentStatusUpdatedMail(
                    $appointment->user,
                    $appointment->appointment_status,
                    $appointment
                ));
        }
        //}
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        // if ($appointment->wasChanged('appointment_status') && $appointment->appointment_status !== AppointmentStatusEnum::Completed->value) {
        //     if ($appointment->user) {
        //         Mail::to($appointment->user->email)
        //             ->send(new VetAppointmentStatusUpdatedMail(
        //                 $appointment->user,
        //                 $appointment->appointment_status,
        //                 $appointment
        //             ));
        //     }
        // }

    }


    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }

    // private function notify(Appointment $appointment): void
    // {
    //     $staffs = User::role(['admin_vet', 'Admin_vet','secretary_vet'])->get();

    //     foreach ($staffs as $staff) {
    //         $notify_to = $this->createNotification(
    //             'New Appointment Created',
    //             'New appointment for ' . $appointment->user->name . ' has been created.',
    //             $appointment,
    //             $staff
    //         );
    //         $notify_to->sendToDatabase($staff);
    //     }
    // }


    // private function createNotification(string $title, string $body, Appointment $appointment): Notification
    // {
    //     return Notification::make()
    //         ->title($title)
    //         ->icon('heroicon-o-newspaper')
    //         ->body($body)
    //         ->actions([
    //             Action::make('View')
    //                 ->button()
    //                 ->icon('heroicon-o-eye')
    //                 ->label('View')
    //                 ->url(AppointmentApplicationResource::getUrl('view',['record' => $appointment])),
    //         ]);


    // }

    public function notify(Appointment $appointment): void
    {
        // Load the appointment with categories (to ensure fresh data)
        $appointment->load('categories');

        // Get all selected category IDs from the appointment
        $selectedCategoryIds = $appointment->categories->pluck('id')->toArray();
        //dd($selectedCategoryIds);
        if (empty($selectedCategoryIds)) {
            return; // No selected categories, nothing to notify
        }

        // Get all doctors who have matching doctorservices
        $doctors = User::with('doctorservices')
            ->role(['admin_vet', 'Admin_vet'])
            ->get()
            ->filter(function ($doctor) use ($selectedCategoryIds) {
                // Check if doctor has any service in the selected categories
                return $doctor->doctorservices->pluck('id')->intersect($selectedCategoryIds)->isNotEmpty();
            });

        // Include secretaries (if they should always get notified)
        $secretaries = User::role(['secretary_vet'])->get();

        // Merge all staff who need to be notified
        $staffs = $doctors->merge($secretaries);

        foreach ($staffs as $staff) {
            // Get the categories assigned to this doctor for this appointment
            $doctorCategories = $appointment->categories->filter(function ($category) use ($staff) {
                return $staff->doctorservices->pluck('id')->contains($category->id);
            });

            $categoryNames = $doctorCategories->pluck('appoint_cat_name')->implode(', ');

            $notification = $this->createNotification(
                'New Appointment Created',
                'New appointment for ' . $appointment->user->name .
                    ' with services: ' . $categoryNames,
                $appointment,
                $staff
            );

            $notification->sendToDatabase($staff);
        }
    }

    private function createNotification(string $title, string $body, Appointment $appointment, User $staff): Notification
    {
        return Notification::make()
            ->title($title)
            ->icon('heroicon-o-newspaper')
            ->body($body)
            ->actions([
                Action::make('View')
                    ->button()
                    ->icon('heroicon-o-eye')
                    ->label('View')
                    ->url(AppointmentApplicationResource::getUrl('view', ['record' => $appointment])),
            ]);
    }
}
