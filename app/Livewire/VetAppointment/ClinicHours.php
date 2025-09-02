<?php

namespace App\Livewire\VetAppointment;

use Livewire\Component;

class ClinicHours extends Component
{

    public $schedules;

    public function mount($schedules = null)
    {
        $this->schedules = $schedules;
    }

    public function render()
    {
        return view('livewire.vet-appointment.clinic-hours');
    }
}
