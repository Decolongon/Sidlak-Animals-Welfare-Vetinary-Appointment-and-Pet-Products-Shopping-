<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Appointment\Appointment;
use App\Models\Appointment\VetSchedule;
use App\Models\Appointment\DoctorSchedule;
use App\Models\Appointment\AppointmentCategory;

class VetAppointmentHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getDoctorShedules()
    {
        return DoctorSchedule::query()
            ->with(['doctor'])
            ->where('effective_from', '<=', now()->endOfMonth()) // Show future schedules too
            ->where('effective_to', '>=', now()->startOfMonth())
            //->orderBy('effective_from')
            ->get();
    }

    public function getDoctorShedulesForMonth($currentYear, $currentMonth)
    {
        return DoctorSchedule::query()
            ->with(['doctor'])
            ->where('effective_from', '<=', Carbon::create($currentYear, $currentMonth, 1)->endOfMonth())
            ->where('effective_to', '>=', Carbon::create($currentYear, $currentMonth, 1))
            ->get();
    }

    public function getPreviousAppointment()
    {
        return Appointment::query()
            ->where('user_id', auth()->user()->id)
            ->latest()
            ->first();
    }

    public function getAppointmentCat()
    {
        return AppointmentCategory::get(['id', 'appoint_cat_name', 'price']);
    }

    public function getSpecificAppointment($appointmentId)
    {
        return Appointment::with('categories')
            ->where('id', $appointmentId)
            ->where('user_id', auth()->id())
            ->first();
    }

    public function getSelectedPrevAppointment($selectedAppointmentId)
    {
        return Appointment::with('categories')
            ->where('id', $selectedAppointmentId)
            ->where('user_id', auth()->id())
            ->first();
    }

    public function VetClinicHours()
    {
        return VetSchedule::with('user')
            ->where('vet_schedule_open', '<=', now())
            ->where('vet_schedule_close', '>=', now())
            ->first();
    }

    public function isTimeSlotBooked($date, $time)
    {
        return Appointment::whereDate('appoint_sched', $date)
            ->whereTime('appoint_sched', $time)
            ->exists();
    }

    public function hasExistingAppointments(Carbon $date)
    {
        return Appointment::whereDate('appoint_sched', $date->format('Y-m-d'))
            ->exists();
    }
}
