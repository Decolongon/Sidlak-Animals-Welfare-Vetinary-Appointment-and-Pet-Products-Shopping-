<?php

namespace App\Livewire\VetAppointment;

use App\Models\Appointment\AppointmentCategory;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Layout;

class VissionAndServices extends Component
{

    public function mount()
    {

    }

    #[Computed()]
    public function getServiceSched(){
       return AppointmentCategory::with(['doctorschedules','doctor'])->get();
    }

    public function render()
    {
        return view('livewire.vet-appointment.vission-and-services');
    }
}
