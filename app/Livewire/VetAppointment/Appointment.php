<?php

namespace App\Livewire\VetAppointment;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Luigel\Paymongo\Facades\Paymongo;
use App\Models\Appointment\VetSchedule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Appointment\AppointmentCategory;
use App\Models\Appointment\Appointment as VetAppointment;

class Appointment extends Component
{
    use LivewireAlert;

    public $currentStep = 1;
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
    public $payment_status;
    public $e_wallet_method;
    public $card_name;
    public $card_number;
    public $expiration_month;
    public $expiration_year;
    public $cvv;

    protected $paymentMethod;
    protected $paymentIntent;
    protected $paymentIntent_id;
    protected $createPaymentIntent;
    protected $amount;

    public $appointments_today;
    public $is_fully_booked;

    public function mount()
    {
        // $this->getAppointmentCat();
        $this->getVetSchedule();
    }

    // Stepper navigation methods
    public function nextStep()
    {
        $this->validateCurrentStep();
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    protected function validateCurrentStep()
    {
        $rules = [];

        if ($this->currentStep == 1) {
            $rules = [
                'pet_name' => 'required|string|max:255',
                'pet_type' => 'required|string|max:255',
                'pet_breed' => 'required',
                'pet_age' => 'required|min:1|numeric',
                'pet_gender' => 'required',
                'pet_age_unit' => 'required',
                'pet_weight' => 'required|numeric|max:30',
                'isPetVaccinated' => 'required',
            ];

            if ($this->pet_age_unit === 'years old') {
                $rules['pet_age'] .= '|max:20';
            } elseif ($this->pet_age_unit === 'months') {
                $rules['pet_age'] .= '|max:240'; // 20 years * 12 months = 240 months
            }
        }

        if ($this->currentStep == 2) {
            //services selected
            $rules = [
                'appointment_category_id' => 'required|array|min:1',

            ];
        }

        if ($this->currentStep == 3) {
            $rules = [
                'payment_method' => 'required',
            ];

            if ($this->payment_method === 'E-Wallets') {
                $rules['payment_method'] = 'required|in:gcash,card,paymaya,grab_pay';
            }

            if ($this->payment_method === 'card') {
                $rules['card_name'] = 'required|string|min:3|max:100';
                $rules['card_number'] = 'required|numeric|digits:16';
                $rules['expiration_month'] = 'required|numeric|min:1|max:12';
                $rules['expiration_year'] = 'required|numeric|min:' . date('Y') . '|max:' . (date('Y') + 15);
                $rules['cvv'] = 'required|numeric|digits:3';
            }
        }

        $this->validate($rules);
    }

    //sanitization
    protected function sanitizeInput(array $data): array
    {
        return array_map(function ($value) {
            return is_array($value) ? $this->sanitizeInput($value) : strip_tags($value);
        }, $data);
    }

    protected function messages()
    {
        return [
            'pet_age.max' => 'Pet age must not be greater than 20 years old.',
        ];
    }

    protected function rules()
    {
        // $rules = [];

        $rules = [
            'pet_name' => 'required|string|max:255',
            'pet_type' => 'required|string|max:255',
            'pet_breed' => 'required',
            'pet_age' => 'required|min:1|numeric',
            'pet_gender' => 'required',
            'pet_age_unit' => 'required',
            'pet_weight' => 'required|numeric|max:30',
            'isPetVaccinated' => 'required',
            'appointment_category_id' => 'required|array|min:1',
            'payment_method' => 'required',
        ];

        if ($this->pet_age_unit === 'years old') {
            $rules['pet_age'] .= '|max:20';
        } elseif ($this->pet_age_unit === 'months') {
            $rules['pet_age'] .= '|max:240'; // 20 years * 12 months = 240 months
        }

        if ($this->payment_method === 'E-Wallets') {
            $rules['payment_method'] = 'required|in:gcash,card,paymaya,grab_pay';
        }

        if ($this->payment_method === 'card') {
            $rules['card_name'] = 'required|string|min:3|max:100';
            $rules['card_number'] = 'required|numeric|digits:16';
            $rules['expiration_month'] = 'required|numeric|min:1|max:12';
            $rules['expiration_year'] = 'required|numeric|min:' . date('Y') . '|max:' . (date('Y') + 15);
            $rules['cvv'] = 'required|numeric|digits:3';
        }



        return $rules;
    }

    #[Computed()]
    public function getAppointmentCat()
    {
        return AppointmentCategory::with([
            'doctorschedules' => function ($query) {
                $query->where('effective_from', '<=', now())
                    ->where('effective_to', '>=', now());
            }

        ])->get(['id', 'appoint_cat_name', 'price']);
    }

    protected function getTotalPrice()
    {
        $this->amount = 0;
        foreach ($this->appointment_category_id as $id) {
            $category = AppointmentCategory::find($id);
            $this->amount += $category->price;
        }
        return $this->amount;
    }

    public function getVetSchedule()
    {
        $now = now();

        $activeSchedule = VetSchedule::with('user')
            ->where('vet_schedule_open', '<=', $now)
            ->where('vet_schedule_close', '>=', $now)
            ->first();

        if ($activeSchedule) {
            $appointmentsCount = \App\Models\Appointment\Appointment::whereDate('created_at', $now->toDateString())
                ->whereTime('created_at', '>=', $activeSchedule->vet_schedule_open->format('H:i:s'))
                ->whereTime('created_at', '<=', $activeSchedule->vet_schedule_close->format('H:i:s'))
                ->count();

            return [
                'schedules' => $activeSchedule,
                'appointmentsCount' => $appointmentsCount,
            ];
        }

        return [
            'schedules' => null,
            'appointmentsCount' => 0,
        ];
    }

    // public function submit()
    // {
    //     $validatedData = $this->validate();

    //     $sanitizedData = $this->sanitizeInput([
    //         'pet_name' => $validatedData['pet_name'],
    //         'pet_type' => $validatedData['pet_type'],
    //         'pet_breed' => $validatedData['pet_breed'],
    //         'pet_age' => $validatedData['pet_age'],
    //         'pet_age_unit' => $validatedData['pet_age_unit'],
    //         'pet_gender' => $validatedData['pet_gender'],
    //         'pet_weight' => $validatedData['pet_weight'],
    //         'isPetVaccinated' => $validatedData['isPetVaccinated'],
    //         'appointment_category_id' => $validatedData['appointment_category_id'],
    //         'payment_method' =>  $validatedData['payment_method'],
    //         'user_id' => Auth::user()->id,
    //         'total_amount' => $this->getTotalPrice()
    //     ]);

    //     if ($this->payment_method === 'card') {
    //         $sanitizedData['card_name'] = $validatedData['card_name'];
    //         $sanitizedData['expiration_month'] = $validatedData['expiration_month'];
    //         $sanitizedData['expiration_year'] = $validatedData['expiration_year'];
    //         $sanitizedData['cvv'] = $validatedData['cvv'];
    //     }

    //     try {
    //         $insertCat = VetAppointment::create([
    //             'pet_name' => $sanitizedData['pet_name'],
    //             'pet_type' => $sanitizedData['pet_type'],
    //             'pet_breed' => $sanitizedData['pet_breed'],
    //             'pet_age' => $sanitizedData['pet_age'] . ' ' . ucwords($sanitizedData['pet_age_unit']),
    //             'pet_gender' => $sanitizedData['pet_gender'],
    //             'pet_weight' => $sanitizedData['pet_weight'],
    //             'isPetVaccinated' => $sanitizedData['isPetVaccinated'],
    //             'user_id' => $sanitizedData['user_id'],
    //             'payment_method' =>  $sanitizedData['payment_method'],
    //             'total_amount' => $this->getTotalPrice()
    //         ]);

    //         $insertCat->categories()->attach($sanitizedData['appointment_category_id']);

    //         if ($this->payment_method !== 'Over The Counter') {
    //             $this->paymentCreateIntent($this->getTotalPrice());
    //             $this->paymentIntent = Paymongo::paymentIntent()->find($this->paymentIntent_id)->getAttributes();

    //             $this->paymentCreateMethod($sanitizedData);

    //             $insertCat->update([
    //                 'paymentIntent_id' => $this->paymentIntent_id
    //             ]);
    //             $attachedPaymentIntent = $this->createPaymentIntent->attach($this->paymentMethod->id, route('payment_vet'));
    //             if (isset($attachedPaymentIntent->next_action['redirect']['url'])) {
    //                 $insertCat->update([
    //                     'payment_status' => 'pending'
    //                 ]);
    //                 return redirect()->away($attachedPaymentIntent->next_action['redirect']['url']);
    //             }

    //             if ($attachedPaymentIntent->status === 'succeeded') {
    //                 $insertCat->update([
    //                     'payment_status' => 'completed'
    //                 ]);
    //                 $this->alert('success', '', [
    //                     'position' => 'top-end',
    //                     'timer' => 5000,
    //                     'toast' => true,
    //                     'text' => 'Successfully Booked Appointment!',
    //                 ]);
    //                 return redirect()->route('appointment');
    //             }
    //         }

    //         $this->alert('success', '', [
    //             'position' => 'top-end',
    //             'timer' => 3000,
    //             'toast' => true,
    //             'text' => 'Successfully Booked Appointment!',
    //         ]);
    //         return redirect()->route('appointment');
    //     } catch (\Exception $e) {

    //         if (isset($insertCat)) {
    //             $insertCat->update([
    //                 'payment_status' => 'failed'
    //             ]);
    //         }
    //         $insertCat->delete();
    //         $this->alert('warning', '', [
    //             'position' => 'top-end',
    //             'timer' => 10000,
    //             'toast' => true,
    //             'text' => 'Payment processing failed: ' . $e->getMessage(),
    //         ]);

    //         return redirect()->route('appointment');
    //     }
    // }

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
            'payment_method' =>  $validatedData['payment_method'],
            'user_id' => Auth::user()->id
        ]);

        if ($this->payment_method === 'card') {
            $sanitizedData['card_name'] = $validatedData['card_name'];
            $sanitizedData['expiration_month'] = $validatedData['expiration_month'];
            $sanitizedData['expiration_year'] = $validatedData['expiration_year'];
            $sanitizedData['cvv'] = $validatedData['cvv'];
        }

        try {
            // Start database transaction
            DB::beginTransaction();

            // Only create appointment record after successful payment processing
            $appointmentData = [
                'pet_name' => $sanitizedData['pet_name'],
                'pet_type' => $sanitizedData['pet_type'],
                'pet_breed' => $sanitizedData['pet_breed'],
                'pet_age' => $sanitizedData['pet_age'] . ' ' . ucwords($sanitizedData['pet_age_unit']),
                'pet_gender' => $sanitizedData['pet_gender'],
                'pet_weight' => $sanitizedData['pet_weight'],
                'isPetVaccinated' => $sanitizedData['isPetVaccinated'],
                'user_id' => $sanitizedData['user_id'],
                'payment_method' =>  $sanitizedData['payment_method'],
                'total_amount' => $this->getTotalPrice()
            ];

            // Process payment first for non-OTC methods
            if ($this->payment_method !== 'Over The Counter') {
                $this->paymentCreateIntent($this->getTotalPrice());
                $this->paymentIntent = Paymongo::paymentIntent()->find($this->paymentIntent_id)->getAttributes();

                $this->paymentCreateMethod($sanitizedData);

                $appointmentData['paymentIntent_id'] = $this->paymentIntent_id;

                // Attach payment method to payment intent
                $attachedPaymentIntent = $this->createPaymentIntent->attach($this->paymentMethod->id, route('payment_vet'));

                if (isset($attachedPaymentIntent->next_action['redirect']['url'])) {
                    // Payment requires further action (3DS authentication)
                    $appointmentData['payment_status'] = 'pending';

                    // Create appointment record
                    $insertCat = VetAppointment::create($appointmentData);
                    $insertCat->categories()->attach($sanitizedData['appointment_category_id']);

                    DB::commit();

                    // Redirect to payment gateway
                    return redirect()->away($attachedPaymentIntent->next_action['redirect']['url']);
                }

                if ($attachedPaymentIntent->status === 'succeeded') {
                    // Payment successful
                    $appointmentData['payment_status'] = 'completed';

                    // Create appointment record
                    $insertCat = VetAppointment::create($appointmentData);
                    $insertCat->categories()->attach($sanitizedData['appointment_category_id']);

                    DB::commit();

                    $this->alert('success', '', [
                        'position' => 'top-end',
                        'timer' => 5000,
                        'toast' => true,
                        'text' => 'Successfully Booked Appointment!',
                    ]);
                    return redirect()->route('appointment');
                } else {
                    // Payment failed
                    throw new \Exception('Payment processing failed with status: ' . $attachedPaymentIntent->status);
                }
            } else {
                // Over The Counter payment - create appointment immediately
                $appointmentData['payment_status'] = 'pending';
                $insertCat = VetAppointment::create($appointmentData);
                $insertCat->categories()->attach($sanitizedData['appointment_category_id']);

                DB::commit();

                $this->alert('success', '', [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true,
                    'text' => 'Successfully Booked Appointment!',
                ]);
                return redirect()->route('appointment');
            }
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 10000,
                'toast' => true,
                'text' => 'Appointment booking failed: ' . $e->getMessage(),
            ]);

            return redirect()->route('appointment');
        }
    }

    #[Layout('layouts.app')]
    #[Title('Appoinment')]
    public function render()
    {
        return view('livewire.vet-appointment.appointment', [
            // 'appointmentCategories' => $this->getAppointmentCat(),
            'schedules' => $this->getVetSchedule(),
            'amount' => $this->getTotalPrice(),
        ]);
    }

    public function paymentCreateIntent($amount)
    {
        $paymentIntent = Paymongo::paymentIntent()->create([
            'amount' => $amount,
            'payment_method_allowed' => [
                'card',
                'paymaya',
                'grab_pay',
                'gcash',
            ],
            'currency' => 'PHP',
            'description' => 'Sidlak Animal Welfare Vetinary payment',
            'statement_descriptor' => 'SIDLAK ANIMAL WELFARE',
        ]);

        $this->paymentIntent_id = $paymentIntent->id;
        $this->createPaymentIntent = $paymentIntent;
    }

    public function paymentCreateMethod($sanitizedData)
    {
        if (in_array($this->payment_method, ['gcash', 'paymaya', 'grab_pay'])) {
            $this->paymentMethod = Paymongo::paymentMethod()->create([
                'type' => $this->payment_method,
                'amount' => $this->amount * 100,
                'currency' => 'PHP',
            ]);
        }

        if ($this->payment_method === 'card') {
            $cardNumber = preg_replace('/[^0-9]/', '', $this->card_number);
            $this->paymentMethod = Paymongo::paymentMethod()->create([
                'type' => 'card',
                'details' => [
                    'card_number' => (string)$cardNumber,
                    'exp_month' => (int)$sanitizedData['expiration_month'] ?? 0,
                    'exp_year' => (int)$sanitizedData['expiration_year'] ?? 0,
                    'cvc' => $sanitizedData['cvv'] ?? '',
                ],
                'billing' => [
                    'email' => Auth::user()->email,
                    'name' => $sanitizedData['card_name'],
                ],
            ]);
        }
    }

    public function getIsEWalletMethodProperty()
    {
        return in_array($this->payment_method, ['gcash', 'card', 'paymaya', 'grab_pay']);
    }
}
