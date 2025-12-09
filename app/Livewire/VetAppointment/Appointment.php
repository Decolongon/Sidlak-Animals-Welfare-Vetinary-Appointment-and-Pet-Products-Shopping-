<?php

namespace App\Livewire\VetAppointment;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\VetAppointmentHelper;
use Luigel\Paymongo\Facades\Paymongo;
use App\Models\Appointment\DoctorSchedule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Appointment\AppointmentCategory;
use App\Models\Appointment\Appointment as VetAppointment;
use App\Models\Appointment\VetSchedule as VetClinicSchedule;

class Appointment extends Component
{
    use LivewireAlert;

    public $currentStep = 1;
    public $appointment_category_id = [];
    // public $appointment_category_id;
    public $pet_name;
    public $pet_type;
    public $pet_breed;
    public $pet_age;
    public $pet_age_unit;
    public $pet_gender;
    public $pet_weight;
    public $isPetVaccinated;

    public $selectedDate = null;
    public $selectedTime = null;
    public $calendarMonth;
    public $calendarDays = [];
    public $availableSlots = [];
    public $currentMonth;
    public $currentYear;

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
    public $searchTerm = '';

    #[Locked()]
    public $selectedAppointmentId;

    public function mount()
    {
        $this->getVetSchedule();
        $this->initializeCalendar();
    }

    private function appointmentHelper()
    {
        return app(VetAppointmentHelper::class);
    }

    // Calendar Methods
    protected function initializeCalendar()
    {
        // // Set current month to the first month with available schedules
        //     $firstSchedule = $this->getDoctorShedules()->first();

        //     if ($firstSchedule) {
        //         $this->currentMonth = $firstSchedule->effective_from->month;
        //        // dd($this->currentMonth);
        //         $this->currentYear = $firstSchedule->effective_from->year;
        //     } else {
        //         $this->currentMonth = now()->month;
        //         $this->currentYear = now()->year;
        //     }
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->calendarMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->format('F Y');
        $this->generateCalendarDays();
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->calendarMonth = $date->format('F Y');
        $this->generateCalendarDays();

        $this->reset(['selectedDate', 'selectedTime', 'availableSlots']);
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->calendarMonth = $date->format('F Y');
        $this->generateCalendarDays();
        $this->reset(['selectedDate', 'selectedTime', 'availableSlots']);
    }


    protected function rules()
    {
        $rules = [
            'pet_name' => 'required|string|max:255',
            'pet_type' => 'required|string|max:255',
            'pet_breed' => 'required',
            'pet_age' => 'required|min:1|numeric',
            'pet_gender' => 'required',
            'pet_age_unit' => 'required',
            'pet_weight' => 'required|numeric|max:30',
            'isPetVaccinated' => 'required',
            // 'appointment_category_id' => 'required|array|min:1',
            'appointment_category_id' => 'required',
            'selectedDate' => 'required|date',
            'selectedTime' => 'required',
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


    public function generateCalendarDays()
    {
        $this->calendarDays = [];

        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Get doctor schedules for the current month - filtered by assigned doctors if services are selected
        $doctorSchedules = $this->getDoctorShedulesForMonth();

        // Start from the first day of the month
        $currentDate = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);

        // Generate 6 weeks of calendar
        for ($week = 0; $week < 6; $week++) {
            for ($day = 0; $day < 7; $day++) {
                $dateString = $currentDate->format('Y-m-d');
                $isCurrentMonth = $currentDate->month == $this->currentMonth;
                $isToday = $currentDate->isToday();

                // Check if date is available based on doctor schedules
                $isAvailable = $this->isDateAvailable($currentDate, $doctorSchedules);

                // Check if there are existing appointments on this date
                $hasAppointments = $this->hasExistingAppointments($currentDate);

                $this->calendarDays[] = [
                    'date' => $dateString,
                    'day' => $currentDate->day,
                    'isCurrentMonth' => $isCurrentMonth,
                    'isToday' => $isToday,
                    'isAvailable' => $isAvailable,
                    'hasAppointments' => $hasAppointments,
                ];

                $currentDate->addDay();
            }
        }
    }

    protected function isDateAvailable(Carbon $date, $doctorSchedules)
    {
        // Check if date is in the past
        if ($date->isPast() && !$date->isToday()) {
            return false;
        }

        // Get day name in lowercase (e.g., 'monday', 'tuesday')
        $dayName = strtolower($date->format('l'));

        // Get assigned doctors for selected services
        $assignedDoctorIds = $this->getAssignedDoctorsForServices();

        // Check if any doctor schedule exists for this day within the validity period
        foreach ($doctorSchedules as $schedule) {
            // Filter by assigned doctors if services are selected
            if (!empty($assignedDoctorIds) && !in_array($schedule->doctor_id, $assignedDoctorIds)) {
                continue;
            }

            // Check if date is within the schedule's validity period
            if ($date->between($schedule->effective_from, $schedule->effective_to)) {
                $days = is_array($schedule->days) ? $schedule->days : [$schedule->days];

                // Convert schedule days to lowercase for consistent comparison
                $scheduleDays = array_map('strtolower', $days);

                if (in_array($dayName, $scheduleDays)) {
                    return true;
                }
            }
        }

        return false;
    }

    #[Computed()]
    public function hasExistingAppointments(Carbon $date)
    {
        return $this->appointmentHelper()->hasExistingAppointments($date);
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->selectedTime = null;
        $this->generateAvailableSlots();
    }

    public function selectTimeSlot($time)
    {
        $this->selectedTime = $time;
    }

    public function generateAvailableSlots()
    {
        if (!$this->selectedDate) {
            $this->availableSlots = collect();
            return;
        }

        $selectedDate = Carbon::parse($this->selectedDate);
        $dayName = strtolower($selectedDate->format('l'));

        // Get assigned doctors for selected services
        $assignedDoctorIds = $this->getAssignedDoctorsForServices();

        // Get doctor schedules for this day within validity period
        $doctorSchedules = $this->getDoctorShedules()->filter(function ($schedule) use ($dayName, $selectedDate, $assignedDoctorIds) {
            // Check if selected date is within schedule validity period
            if (!$selectedDate->between($schedule->effective_from, $schedule->effective_to)) {
                return false;
            }

            // Filter by assigned doctors if services are selected
            if (!empty($assignedDoctorIds) && !in_array($schedule->doctor_id, $assignedDoctorIds)) {
                return false;
            }

            $days = is_array($schedule->days) ? $schedule->days : [$schedule->days];
            $scheduleDays = array_map('strtolower', $days);
            return in_array($dayName, $scheduleDays);
        });

        $availableSlots = collect();

        foreach ($doctorSchedules as $schedule) {
            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);

            // Generate 1-hour intervals
            $currentTime = $startTime->copy();

            while ($currentTime < $endTime) {
                $timeString = $currentTime->format('H:i:s');
                $displayTime = $currentTime->format('g:i A');

                // Check if this time slot is already booked for this specific doctor
                $isBooked = $this->isTimeSlotBookedForDoctor($this->selectedDate, $timeString, $schedule->doctor_id);

                $availableSlots->push([
                    'time' => $timeString,
                    'display_time' => $displayTime,
                    'isBooked' => $isBooked,
                    'doctor_id' => $schedule->doctor_id,
                    'doctor_name' => $schedule->doctor->name ?? 'Doctor',
                ]);

                $currentTime->addHour(); // 1-hour intervals
            }
        }

        $this->availableSlots = $availableSlots->sortBy('time');
    }

    /**
     * Get assigned doctors for selected services
     */
    protected function getAssignedDoctorsForServices()
    {
        if (empty($this->appointment_category_id)) {
            return [];
        }

        $assignedDoctorIds = [];
        if (is_array($this->appointment_category_id)) {
            $categoryIds = $this->appointment_category_id;
        } else {
            $categoryIds = $this->appointment_category_id ? [$this->appointment_category_id] : [];
        }

        foreach ($categoryIds as $categoryId) {
            $category = AppointmentCategory::find($categoryId);
            if ($category && $category->doctor_id) {
                $assignedDoctorIds[] = $category->doctor_id;
            }
        }

        // If no specific doctors assigned, return empty array to show all doctors
        if (empty($assignedDoctorIds)) {
            return [];
        }

        return array_unique($assignedDoctorIds);
    }

    #[Computed()]
    public function countAvailableSlots()
    {
        if (!$this->availableSlots) {
            return 0;
        }

        return $this->availableSlots->where('isBooked', false)->count();
    }

    #[Computed()]
    public function isTimeSlotBooked($date, $time)
    {
        return $this->appointmentHelper()->isTimeSlotBooked($date, $time);
    }

    /**
     * Check if time slot is booked for a specific doctor
     */
    #[Computed()]
    public function isTimeSlotBookedForDoctor($date, $time, $doctorId)
    {
        return VetAppointment::whereDate('appoint_sched', $date)
            ->whereTime('appoint_sched', $time)
            //->where('doctor_id', $doctorId)
            ->exists();
    }

    #[Computed()]
    public function getDoctorShedules()
    {
        return $this->appointmentHelper()->getDoctorShedules();
    }

    #[Computed()]
    public function getDoctorShedulesForMonth()
    {
        $doctorSchedules = $this->appointmentHelper()
            ->getDoctorShedulesForMonth($this->currentYear, $this->currentMonth);

        // Filter by assigned doctors if services are selected
        $assignedDoctorIds = $this->getAssignedDoctorsForServices();
        if (!empty($assignedDoctorIds)) {
            $doctorSchedules = $doctorSchedules->filter(function ($schedule) use ($assignedDoctorIds) {
                return in_array($schedule->doctor_id, $assignedDoctorIds);
            });
        }

        return $doctorSchedules;
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
                // 'appointment_category_id' => 'required|array|min:1',
                'appointment_category_id' => 'required',
            ];
        }

        if ($this->currentStep == 3) {
            $rules = [
                'selectedDate' => 'required|date',
                'selectedTime' => 'required',
            ];
        }

        if ($this->currentStep == 4) {
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

    // Rest of the methods remain the same...
    // [Previous methods like sanitizeInput, messages, rules, getAppointmentCat, etc.]

    // Add this method to regenerate calendar when services change
    public function updatedAppointmentCategoryId()
    {
        // Only regenerate if we're on step 3 or beyond
        // if ($this->currentStep >= 3) {
        $this->generateCalendarDays();
        $this->reset(['selectedDate', 'selectedTime', 'availableSlots']);
        $this->selectedDate = null;
        $this->selectedTime = null;
        //}
    }

    #[Computed()]
    public function getAppointmentCat()
    {
        return $this->appointmentHelper()->getAppointmentCat();
    }

    #[Computed()]
    public function getPreviousAppointment()
    {
        return $this->appointmentHelper()->getPreviousAppointment();
    }

    #[Computed()]
    public function getAllAppointmentsForDropdown()
    {
        $appointments = VetAppointment::query()
            ->with(['categories'])
            ->where('user_id', auth()->id())
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($q) {
                    $q->where('pet_name', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('pet_type', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('pet_breed', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($appointment) {
                $petAgeData = $this->extractAgeAndUnit($appointment->pet_age);

                return [
                    'id' => $appointment->id,
                    'pet_name' => $appointment->pet_name,
                    'pet_type' => $appointment->pet_type,
                    'pet_breed' => $appointment->pet_breed,
                    'pet_gender' => $appointment->pet_gender,
                    'pet_weight' => $appointment->pet_weight,
                    'isPetVaccinated' => $appointment->isPetVaccinated,
                    'pet_age' => $petAgeData['age'],
                    'pet_age_unit' => $petAgeData['unit'],
                    'created_at' => $appointment->created_at,
                    'categories' => $appointment->categories->pluck('id')->toArray()
                ];
            })
            ->toArray();

        // Limit to 5 results if not searching
        if (empty($this->searchTerm)) {
            return array_slice($appointments, 0, 5);
        }

        return $appointments;
    }

    #[Computed()]
    public function getSpecificAppointment($appointmentId)
    {
        return $this->appointmentHelper()->getSpecificAppointment($appointmentId);
    }

    #[Computed()]
    public function getSelectedPrevAppointment()
    {
        if (!$this->selectedAppointmentId) {
            return null;
        }

        return $this->appointmentHelper()->getSelectedPrevAppointment($this->selectedAppointmentId);
    }

    public function clearSelectedAppointment()
    {
        $this->selectedAppointmentId = null;
        $this->resetForm();
    }

    //sanitization
    protected function sanitizeInput(array $data): array
    {
        return array_map(function ($value) {
            return is_array($value) ? $this->sanitizeInput($value) : strip_tags($value);
        }, $data);
    }

    protected function resetForm()
    {
        $this->reset([
            'pet_name',
            'pet_type',
            'pet_breed',
            'pet_age',
            'pet_age_unit',
            'pet_gender',
            'pet_weight',
            'isPetVaccinated',
            'appointment_category_id'
        ]);
    }

    public function autoFillFromAppointment($appointmentId)
    {
        $this->selectedAppointmentId = $appointmentId;
        // Find the specific appointment
        $appointment = $this->getSpecificAppointment($appointmentId);

        if ($appointment) {
            $petAgeData = $this->extractAgeAndUnit($appointment->pet_age);

            // Fill the form with the selected appointment data
            $this->pet_name = $appointment->pet_name;
            $this->pet_type = $appointment->pet_type;
            $this->pet_breed = $appointment->pet_breed;
            $this->pet_age = $petAgeData['age'];
            $this->pet_age_unit = $petAgeData['unit'];
            $this->pet_gender = $appointment->pet_gender;
            $this->pet_weight = $appointment->pet_weight;
            $this->isPetVaccinated = $appointment->isPetVaccinated ? '1' : '0';

            // Get the categories from the selected appointment
            $previousCategories = $appointment->categories->pluck('id')->toArray();
            $this->appointment_category_id = $previousCategories;

            // Regenerate calendar with new services
            $this->generateCalendarDays();

            // Optional: Show success message
            $this->alert('success', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Appointment data filled successfully!',
            ]);

            return true;
        }

        // Show error if appointment not found
        $this->alert('warning', '', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'text' => 'Appointment not found.',
        ]);

        return false;
    }

    protected function extractAgeAndUnit($petAge)
    {
        $age = 0;
        $unit = 'months'; // default

        if (preg_match('/(\d+)\s*(year|month)/i', $petAge, $matches)) {
            $age = (int)$matches[1];
            $unit = strtolower($matches[2]) === 'year' ? 'years old' : 'months';
        }

        return [
            'age' => $age,
            'unit' => $unit
        ];
    }

    protected function getTotalPrice()
    {
        //for mulitple services
        $this->amount = 0;
        // foreach ($this->appointment_category_id as $id) {
        //     $category = AppointmentCategory::find($id);
        //     $this->amount += $category->price;

        //     if ($this->isGrooming($category)) {
        //         $this->amount += $this->getAdditionalFeeForGrooming();
        //     }
        // }
        // return $this->amount;



        if (is_array($this->appointment_category_id)) {
            $categoryIds = $this->appointment_category_id;
        } else {
            $categoryIds = $this->appointment_category_id ? [$this->appointment_category_id] : [];
        }

        foreach ($categoryIds as $id) {
            $category = AppointmentCategory::find($id);
            if ($category) {
                $this->amount += $category->price;

                if ($this->isGrooming($category)) {
                    $this->amount += $this->getAdditionalFeeForGrooming();
                }
            }
        }
        //dd($this->amount);
        return $this->amount;
    }

    protected function isGrooming($category)
    {
        return str_contains(strtolower($category->appoint_cat_name), 'grooming');
    }

    protected function getAdditionalFeeForGrooming()
    {
        if (!$this->pet_weight) {
            return 0;
        }

        $weight = floatval($this->pet_weight);

        $additionalFee = 0;
        if ($weight <= 5) {
            $additionalFee = 0; // 0-5kg: No additional fee
        } elseif ($weight <= 10) {
            $additionalFee = 100; // 6-10kg: +100 pesos
        } elseif ($weight <= 20) {
            $additionalFee = 200; // 11-20kg: +200 pesos
        } else {
            $additionalFee = 300; // 20kg and above: +300 pesos
        }

        return $additionalFee;
    }

    // this is for clinic hours
    protected function getVetSchedule()
    {
        $now = now();

        $activeSchedule = $this->appointmentHelper()->VetClinicHours();

        if ($activeSchedule) {
            $appointmentsCount = VetAppointment::whereDate('appoint_sched', $now->toDateString())
                ->whereTime('appoint_sched', '>=', $activeSchedule->vet_schedule_open->format('H:i:s'))
                ->whereTime('appoint_sched', '<=', $activeSchedule->vet_schedule_close->format('H:i:s'))
                ->count();

            return [
                'schedules' => $activeSchedule,
                'isOpen' => true,
            ];
        }

        return [
            'schedules' => null,
            'isOpen' => false,
        ];
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

            if ($this->selectedAppointmentId) {
                $this->deletePrevRecord();
            }

            // Get the selected time slot details to get the doctor_id
            $selectedSlot = $this->availableSlots->firstWhere('time', $this->selectedTime);
            if (!$selectedSlot) {
                throw new \Exception('The selected time slot is no longer available. Please choose another time.');
            }

            // Check if the selected time slot is still available for the specific doctor
            if ($this->isTimeSlotBookedForDoctor($this->selectedDate, $this->selectedTime, $selectedSlot['doctor_id'])) {
                throw new \Exception('The selected time slot is no longer available. Please choose another time.');
            }

            // Create appointment datetime from selected date and time
            $appointmentDateTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);

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
                'total_amount' => $this->getTotalPrice(),
                'appoint_sched' => $appointmentDateTime, // Set the actual appointment datetime
                'doctor_id' => $selectedSlot['doctor_id'], // Assign the doctor from the selected time slot
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
                    $appointmentData['appointment_status'] = 'approved';

                    // Create appointment record
                    $insertCat = VetAppointment::create($appointmentData);
                    $insertCat->categories()->attach($sanitizedData['appointment_category_id']);
                    $this->manualNotifcation($insertCat);

                    DB::commit();

                    // Redirect to payment gateway
                    return redirect()->away($attachedPaymentIntent->next_action['redirect']['url']);
                }

                if ($attachedPaymentIntent->status === 'succeeded') {
                    // Payment successful
                    $appointmentData['payment_status'] = 'completed';
                    $appointmentData['appointment_status'] = 'approved';
                    // Create appointment record
                    $insertCat = VetAppointment::create($appointmentData);
                    $insertCat->categories()->attach($sanitizedData['appointment_category_id']);
                    $this->manualNotifcation($insertCat);

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
                $appointmentData['appointment_status'] = 'approved';
                $insertCat = VetAppointment::create($appointmentData);
                $insertCat->categories()->attach($sanitizedData['appointment_category_id']);

                $this->manualNotifcation($insertCat);


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

            //dd($e->getMessage());

            return redirect()->route('appointment');
        }
    }

    private function manualNotifcation($insertCat)
    {

        app(\App\Observers\VetAppointmentObserver::class)->notify($insertCat);
    }

    #[Layout('layouts.app')]
    #[Title('Appoinment')]
    public function render()
    {
        return view('livewire.vet-appointment.appointment', [
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
                'amount' => $this->amount,
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

    protected function deletePrevRecord()
    {
        $oldAppointment = $this->getSelectedPrevAppointment();

        if ($oldAppointment) {
            // Detach categories first
            $oldAppointment->categories()->detach();
            // Delete the appointment
            $oldAppointment->delete();
        }
    }
}
