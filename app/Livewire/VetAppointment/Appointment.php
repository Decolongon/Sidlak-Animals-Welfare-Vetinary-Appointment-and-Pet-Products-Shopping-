<?php

namespace App\Livewire\VetAppointment;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment\VetSchedule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Appointment\AppointmentCategory;
use App\Models\Appointment\Appointment as VetAppointment;

class Appointment extends Component
{
    use LivewireAlert;

    public $appointment_category_id = [];
    public $pet_name;
    public $pet_type;
    public $pet_breed;
    public $pet_age;
    public $pet_gender;
    public $pet_weight;
    public $isPetVaccinated;

    public $num_customer_to_accomodate;


    public $donor_payment_method;
    public $card_name;
    public $card_number;
    public $expiration_month;
    public $expiration_year;
    public $cvv;

    public function mount()
    {
        $this->getAppointmentCat();
        $this->getVetSchedule();
       
    }

    //sanitization
    protected function sanitizeInput(array $data): array
    {
        return array_map(function ($value) {
            return is_array($value) ? $this->sanitizeInput($value) : strip_tags($value);
        }, $data);
    }

    protected $rules = [
        'pet_name' => 'required|string|max:255',
        'pet_type' => 'required|string|max:255',
        'pet_breed' => 'required',
        'pet_age' => 'required|numeric',
        'pet_gender' => 'required',
        'pet_weight' => 'required',
        'isPetVaccinated' => 'required',
        'appointment_category_id' => 'required|array|min:1',
    ]; 

    public function getAppointmentCat()
    {
        return AppointmentCategory::get(['id', 'appoint_cat_name']);
    }

    public function getVetSchedule()
    {
        $schedules= VetSchedule::with('user')
        ->where('vet_schedule_open', '<=', now())
        ->where('vet_schedule_close', '>=', now())
        ->get();

        if ($schedules->isNotEmpty()) {
            $this->num_customer_to_accomodate = $schedules->first()->num_customers;
        }

        return $schedules;
    }

    public function submit()
    {
        $validatedData = $this->validate();

       $sanitizedData = $this->sanitizeInput([
            'pet_name' => $validatedData['pet_name'],
            'pet_type' => $validatedData['pet_type'],
            'pet_breed' => $validatedData['pet_breed'],
            'pet_age' => $validatedData['pet_age'],
            'pet_gender' => $validatedData['pet_gender'],
            'pet_weight' => $validatedData['pet_weight'],
            'isPetVaccinated' => $validatedData['isPetVaccinated'],
            'appointment_category_id' => $validatedData['appointment_category_id'],
            'user_id' => Auth::user()->id
        ]);

        $insertCat = VetAppointment::create([
            'pet_name' => $sanitizedData['pet_name'],
            'pet_type' => $sanitizedData['pet_type'],
            'pet_breed' => $sanitizedData['pet_breed'],
            'pet_age' => $sanitizedData['pet_age'],
            'pet_gender' => $sanitizedData['pet_gender'],
            'pet_weight' => $sanitizedData['pet_weight'],
            'isPetVaccinated' => $sanitizedData['isPetVaccinated'],
            'user_id' => $sanitizedData['user_id']
        ]);

        $insertCat->categories()->attach($sanitizedData['appointment_category_id']);
        
          $this->alert('success','', [
            'position' => 'top-end',
            'timer' => 4000,
            'toast' => true,
            'text' => 'Successfully Booked Appointment',
        ]);
        return redirect()->route('appointment');
    }

    #[Layout('layouts.app')]
    #[Title('Appoinment')]
    public function render()
    {
        return view('livewire.vet-appointment.appointment',[
            'appointmentCategories' => $this->getAppointmentCat(),
            'schedules' => $this->getVetSchedule()
        ]);
    }
}
