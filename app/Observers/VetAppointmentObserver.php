<?php

namespace App\Observers;

use App\Enums\AppointmentStatusEnum;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment\Appointment;
use App\Mail\VetAppointmentStatusUpdatedMail;

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
        if ($appointment->wasChanged('appointment_status') && $appointment->appointment_status !== AppointmentStatusEnum::Completed->value) {
            if ($appointment->user) {
                Mail::to($appointment->user->email)
                    ->send(new VetAppointmentStatusUpdatedMail(
                        $appointment->user,
                        $appointment->appointment_status,
                        $appointment
                    ));
            }
        }
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
}
