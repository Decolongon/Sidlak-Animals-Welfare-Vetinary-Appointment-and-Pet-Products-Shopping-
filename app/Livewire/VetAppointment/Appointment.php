<?php

namespace App\Livewire\VetAppointment;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment\VetSchedule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Appointment\AppointmentCategory;
use App\Models\Appointment\Appointment as VetAppointment;
use Luigel\Paymongo\Facades\Paymongo;
class Appointment extends Component
{
    use LivewireAlert;

    public $appointment_category_id = [];
    public $pet_name;
    public $pet_type;
    public $pet_breed;
    public $pet_age;
    public $pet_age_unit;
    public $pet_gender;
    public $pet_weight;
    public $isPetVaccinated;

    public $num_customer_to_accomodate;


    public $payment_method;
    public $card_name;
    public $card_number;
    public $expiration_month;
    public $expiration_year;
    public $cvv;
   
    protected $paymentMethod;
    protected $paymentIntent;
    protected $paymentIntent_id;
    protected $createPaymentIntent;
    protected $amount = 500;

    public $appointments_today;
    public $is_fully_booked;


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

    
    protected function rules()
    {
         $rules = [ 
            'pet_name' => 'required|string|max:255',
            'pet_type' => 'required|string|max:255',
            'pet_breed' => 'required',
            'pet_age' => 'required|max:20|min:1',
            'pet_gender' => 'required',
            'pet_age_unit' => 'required',
            'pet_weight' => 'required|numeric|max:30',
            'isPetVaccinated' => 'required',
            'appointment_category_id' => 'required|array|min:1',
            'payment_method' => 'required',
        ];

        if ($this->payment_method === 'card') {
            $rules['card_name'] = 'required|string|min:3|max:100';
            $rules['card_number'] = 'required|numeric|digits:16';
            $rules['expiration_month'] = 'required|numeric|min:1|max:12';
            $rules['expiration_year'] = 'required|numeric|min:' . date('Y') . '|max:' . (date('Y') + 15);
            $rules['cvv'] = 'required|numeric|digits:3';
        }
        return $rules;
    }

    public function getAppointmentCat()
    {
        return AppointmentCategory::get(['id', 'appoint_cat_name']);
    }

    public function getVetSchedule()
    {
        $schedules= VetSchedule::with('user')
        // ->where('vet_schedule_open', '<=', now())
        // ->where('vet_schedule_close', '>=', now())
        ->get();

        // if ($schedules->isNotEmpty()) {
        //     $this->num_customer_to_accomodate = $schedules->first()->num_customers;
        // } 
      
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
            'pet_age_unit' => $validatedData['pet_age_unit'],
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
            'pet_age' => $sanitizedData['pet_age'] . ' ' . $sanitizedData['pet_age_unit'],
            'pet_gender' => $sanitizedData['pet_gender'],
            'pet_weight' => $sanitizedData['pet_weight'],
            'isPetVaccinated' => $sanitizedData['isPetVaccinated'],
            'user_id' => $sanitizedData['user_id']
        ]);

        $insertCat->categories()->attach($sanitizedData['appointment_category_id']);
        
          $this->alert('success','', [
            'position' => 'top-end',
            'timer' => 3000,
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

     public function paymentCreateIntent($amount){
        
        $paymentIntent = Paymongo::paymentIntent()->create([
            'amount' => $amount,
            'payment_method_allowed' => [
                'card','paymaya','grab_pay', 'gcash',
            ],
            'currency' => 'PHP',
            'description' => 'Sidlak Animal Welfare Vetinary payment',
            'statement_descriptor' => 'SIDLAK ANIMAL WELFARE',
        ]);
    
        $this->paymentIntent_id = $paymentIntent->id;
        $this->createPaymentIntent = $paymentIntent;
        // dd($this->createPaymentIntent);

    }



     public function paymentCreateMethod($sanitizedData)
    {
      
        if (in_array($this->payment_method, ['gcash', 'paymaya', 'grab_pay'])) {
            $this->paymentMethod = Paymongo::paymentMethod()->create([
                'type' => $this->shipping_method,
                'amount' => $this->amount * 100,
                'currency' => 'PHP',
                
            ]);
           
        }
    
        // Card logic
        if ($this->payment_method === 'card') {
             $cardNumber = preg_replace('/[^0-9]/', '', $this->card_number);
           $this->paymentMethod = Paymongo::paymentMethod()->create([
                'type' => 'card',
                'details' => [
                    'card_number' => (string)$cardNumber,
                    'exp_month' =>(int)$sanitizedData['expiration_month'],
                    'exp_year' => (int)$sanitizedData['expiration_year'],
                    'cvc' => $sanitizedData['cvv'],
                ],
                'billing' => [
                    'email' => Auth::user()->email,
                    'name' => $sanitizedData['card_name'], 
                ],
            ]);
        }
    }



}
