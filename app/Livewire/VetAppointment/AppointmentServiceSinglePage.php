<?php

namespace App\Livewire\VetAppointment;

use App\Models\Appointment\AppointmentCategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AppointmentServiceSinglePage extends Component
{
    public $appoint_cat_slug;

    public function mount($appoint_cat_slug)
    {
        $this->appoint_cat_slug = $appoint_cat_slug;
    }

    #[Computed()]
    public function getSingleService()
    {
       return AppointmentCategory::with(['doctor',
       'doctorschedules' => function($query){
            $query->where('effective_from', '<=', now())
            ->where('effective_to', '>=', now());
       }
       ])
        ->where('appoint_cat_slug',$this->appoint_cat_slug)
        ->get();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.vet-appointment.appointment-service-single-page');
    }
}
