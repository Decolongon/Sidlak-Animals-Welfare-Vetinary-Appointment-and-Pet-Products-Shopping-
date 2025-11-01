<?php

namespace App\Livewire\VetAppointment;

use App\Models\Appointment\AppointmentCategory;
use App\Models\Appointment\DoctorSchedule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AppointmentServiceSinglePage extends Component
{
    #[Locked]
    public $appoint_cat_slug;

    public function mount($appoint_cat_slug)
    {
        $this->appoint_cat_slug = $appoint_cat_slug;
    }

    #[Computed()]
    public function getSingleService()
    {
        return AppointmentCategory::query()
           // ->with(['doctor', 'doctorschedules'])
            ->where('appoint_cat_slug', $this->appoint_cat_slug)
            ->get();
    }

    #[Computed()]
    public function  getDoctorShedules(){
        return DoctorSchedule::query()
            ->with(['doctor'])
            ->where('effective_from', '<=', now())
            ->where('effective_to', '>=', now())
            ->get();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.vet-appointment.appointment-service-single-page');
    }
}
