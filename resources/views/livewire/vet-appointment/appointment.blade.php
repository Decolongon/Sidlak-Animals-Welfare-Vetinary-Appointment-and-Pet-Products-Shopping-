<div>

  @livewire('vet-appointment.vission-and-services')

  <livewire:vet-appointment.clinic-hours :schedules="$schedules"/>

    @if (Auth::user())
        <!-- Vet form -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-neutral-100 mb-8">
                Book Appointment With Us
            </h1>

            <div
                class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200 mb-6">
                    Pet & Appointment Details
                    @if ($amount > 0)
                        <span class="block sm:inline-block sm:ml-2 mt-2 sm:mt-0 text-amber-600 dark:text-amber-400">
                            Amount Due: {{ number_format($amount, 2) }}
                        </span>
                    @endif
                </h2>

                @if ($schedules['schedules'] && $schedules['appointmentsCount'] < $schedules['schedules']->num_customers)
                    <div>
                        <!-- Stepper -->
                        <div class="max-w-2xl mx-auto mb-8">
                            <div class="flex justify-between">
                                @foreach ([1, 2, 3, 4] as $step)
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center 
                    {{ $currentStep >= $step ? 'bg-amber-500 text-white' : 'bg-gray-200 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300' }}">
                                            <span>{{ $step }}</span>
                                        </div>
                                        <span
                                            class="mt-2 text-sm {{ $currentStep >= $step ? 'text-amber-600 font-medium dark:text-amber-400' : 'text-gray-500 dark:text-neutral-400' }}">
                                            @if ($step == 1)
                                                Pet Details
                                            @endif
                                            @if ($step == 2)
                                                Services
                                            @endif
                                            @if ($step == 3)
                                                Payment
                                            @endif
                                            @if ($step == 4)
                                                Review
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Progress bar -->
                            <div class="relative mt-4">
                                <div
                                    class="absolute top-0 left-0 h-1 bg-gray-200 dark:bg-neutral-700 w-full rounded-full">
                                </div>
                                <div class="absolute top-0 left-0 h-1 bg-amber-500 transition-all duration-300 rounded-full"
                                    style="width: {{ (($currentStep - 1) / 3) * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Step Content -->
                        <form wire:submit.prevent="submit">
                            <!-- Step 1: Pet Details -->
                            @if ($currentStep == 1)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                                    <!-- pet_name -->
                                    <div class="space-y-2">
                                        <label for="pet_name"
                                            class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Pet
                                            Name</label>
                                        <input type="text" id="pet_name" name="pet_name" wire:model="pet_name"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                        @error('pet_name')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- pet_type -->
                                    <div class="space-y-2">
                                        <label for="pet_type"
                                            class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Pet
                                            Type</label>
                                        <select id="pet_type" name="pet_type" wire:model="pet_type"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                            <option value="">Select</option>
                                            <option value="Dog">Dog</option>
                                            <option value="Cat">Cat</option>
                                        </select>
                                        @error('pet_type')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- pet_breed -->
                                    <div class="space-y-2">
                                        <label for="pet_breed"
                                            class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Pet
                                            Breed</label>
                                        <input type="text" id="pet_breed" name="pet_breed" wire:model="pet_breed"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                        @error('pet_breed')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- pet_gender -->
                                    <div class="space-y-2">
                                        <label for="pet_gender"
                                            class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Pet
                                            Gender</label>
                                        <select id="pet_gender" name="pet_gender" wire:model="pet_gender"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        @error('pet_gender')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- pet_weight -->
                                    <div class="space-y-2">
                                        <label for="pet_weight"
                                            class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Pet
                                            Weight (kg)</label>
                                        <input type="number" id="pet_weight" name="pet_weight"
                                            wire:model="pet_weight" step="0.01"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                        @error('pet_weight')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- pet_age -->
                                    <div class="space-y-2">
                                        <label for="pet_age"
                                            class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Pet
                                            Age</label>
                                        <div class="flex gap-2">
                                            <input type="number" id="pet_age" name="pet_age" wire:model="pet_age"
                                                class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">

                                            <select name="pet_age_unit" id="pet_age_unit" wire:model.live="pet_age_unit"
                                                class="py-2.5 px-4 border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                                <option value="">Select</option>
                                                <option value="years old">Years</option>
                                                <option value="months">Months</option>
                                            </select>
                                        </div>
                                        @error('pet_age')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                        @error('pet_age_unit')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- isPetVaccinated -->
                                    <div class="space-y-2">
                                        <label for="isPetVaccinated"
                                            class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Vaccinated?</label>
                                        <select id="isPetVaccinated" name="isPetVaccinated"
                                            wire:model="isPetVaccinated"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        @error('isPetVaccinated')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <!-- Step 2: Services -->
                            @if ($currentStep == 2)
                                <div class="space-y-6">
                                    <div x-data x-init="new TomSelect($refs.select, {
                                        plugins: ['remove_button'],
                                        onChange() {
                                            let selected = Array.from($refs.select.selectedOptions).map(o => o.value);
                                            $dispatch('input', selected);
                                        }
                                    });" wire:ignore>
                                        <label for="appointment_category_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Services
                                        </label>

                                        <select x-ref="select" name="appointment_category_id"
                                            id="appointment_category_id" multiple
                                            wire:model.live="appointment_category_id"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                            @foreach ($this->getAppointmentCat as $category)
                                                {{-- @if($category->doctorschedules->isNotEmpty()) --}}
                                                <option value="{{ $category->id }}">
                                                    {{ ucwords($category->appoint_cat_name) }}
                                                </option>
                                                {{-- @endif --}}
                                            @endforeach
                                        </select>

                                        @error('appointment_category_id')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    @if (count($appointment_category_id) > 0)
                                        <div
                                            class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                                            <h3 class="font-medium text-amber-800 dark:text-amber-200 mb-2">Selected
                                                Services</h3>
                                            <ul class="list-disc pl-5 space-y-1 text-amber-700 dark:text-amber-300">
                                                @foreach ($this->getAppointmentCat->whereIn('id', $appointment_category_id) as $service)
                                                    <li>{{ $service->appoint_cat_name }} -
                                                        ₱{{ number_format($service->price, 2) }}</li>
                                                @endforeach
                                            </ul>
                                            @if ($amount > 0)
                                                <p class="mt-2 font-medium text-amber-800 dark:text-amber-200">
                                                    Total: ₱{{ number_format($amount, 2) }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Step 3: Payment -->
                            @if ($currentStep == 3)
                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        <label for="payment_method"
                                            class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Payment
                                            Method</label>
                                        <select id="payment_method" name="payment_method"
                                            wire:model.live="payment_method"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                            <option value="">Select</option>
                                            <option value="Over The Counter">Over The Counter</option>
                                            <option value="E-Wallets">E-Wallets</option>
                                        </select>
                                        @error('payment_method')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    @if ($payment_method === 'E-Wallets' || $this->isEWalletMethod)
                                        <div class="border-t border-gray-200 dark:border-neutral-700 pt-6">
                                            <h3 class="text-lg font-medium text-gray-800 dark:text-neutral-200 mb-4">
                                                Select Payment Option</h3>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                                <!-- GCash -->
                                                <label
                                                    class="relative block p-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm cursor-pointer transition hover:shadow-md">
                                                    <input type="radio" name="e_wallet_method" value="gcash"
                                                        wire:click="$set('payment_method', 'gcash')"
                                                        class="absolute top-4 right-4 w-5 h-5 text-amber-600 border-gray-300 rounded-full focus:ring-amber-500 dark:bg-neutral-800 dark:border-neutral-600">
                                                    <img src="{{ asset('imgs/gcash.png') }}"
                                                        class="w-20 h-20 mx-auto mb-3 object-contain" />
                                                    <p
                                                        class="text-center text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                                        Pay using GCash</p>
                                                    <p class="text-center text-xs text-gray-500 dark:text-neutral-400">
                                                        Pay with your GCash wallet</p>
                                                </label>

                                                <!-- Card -->
                                                <label
                                                    class="relative block p-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm cursor-pointer transition hover:shadow-md">
                                                    <input type="radio" name="e_wallet_method" value="card"
                                                        wire:click="$set('payment_method', 'card')"
                                                        class="absolute top-4 right-4 w-5 h-5 text-amber-600 border-gray-300 rounded-full focus:ring-amber-500 dark:bg-neutral-800 dark:border-neutral-600">
                                                    <img src="{{ asset('imgs/card.png') }}"
                                                        class="w-20 h-20 mx-auto mb-3 object-contain" />
                                                    <p
                                                        class="text-center text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                                        Pay using Card</p>
                                                    <p class="text-center text-xs text-gray-500 dark:text-neutral-400">
                                                        Pay via credit or debit card</p>
                                                </label>

                                                <!-- PayMaya -->
                                                <label
                                                    class="relative block p-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm cursor-pointer transition hover:shadow-md">
                                                    <input type="radio" name="e_wallet_method" value="paymaya"
                                                        wire:click="$set('payment_method', 'paymaya')"
                                                        class="absolute top-4 right-4 w-5 h-5 text-amber-600 border-gray-300 rounded-full focus:ring-amber-500 dark:bg-neutral-800 dark:border-neutral-600">
                                                    <img src="{{ asset('imgs/paymaya.png') }}"
                                                        class="w-20 h-20 mx-auto mb-3 object-contain" />
                                                    <p
                                                        class="text-center text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                                        Pay using PayMaya</p>
                                                    <p class="text-center text-xs text-gray-500 dark:text-neutral-400">
                                                        Pay with your PayMaya wallet</p>
                                                </label>

                                                <!-- GrabPay -->
                                                <label
                                                    class="relative block p-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm cursor-pointer transition hover:shadow-md">
                                                    <input type="radio" name="e_wallet_method" value="grab_pay"
                                                        wire:click="$set('payment_method', 'grab_pay')"
                                                        class="absolute top-4 right-4 w-5 h-5 text-amber-600 border-gray-300 rounded-full focus:ring-amber-500 dark:bg-neutral-800 dark:border-neutral-600">
                                                    <img
                                                        src="{{ asset('imgs/grabpay.png') }}"class="w-20 h-20 mx-auto mb-3 object-contain" />
                                                    <p
                                                        class="text-center text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                                        Pay using GrabPay</p>
                                                    <p class="text-center text-xs text-gray-500 dark:text-neutral-400">
                                                        Pay through GrabPay wallet</p>
                                                </label>
                                            </div>

                                            @error('payment_method')
                                                <p class="mt-4 text-sm text-red-500">{{ $message }}</p>
                                            @enderror

                                            @if ($payment_method === 'card')
                                                <div
                                                    class="mt-6 p-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm">
                                                    <h3 class="font-medium text-gray-800 dark:text-neutral-200 mb-4">
                                                        Card Details</h3>

                                                    <div class="space-y-4">
                                                        <div>
                                                            <label for="card_name"
                                                                class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Name
                                                                on Card</label>
                                                            <input type="text" wire:model.blur="card_name"
                                                                id="card_name"
                                                                class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100"
                                                                placeholder="John Doe">
                                                            @error('card_name')
                                                                <p class="mt-1 text-sm text-red-500">{{ $message }}
                                                                </p>
                                                            @enderror
                                                        </div>

                                                        <div>
                                                            <label for="card_number"
                                                                class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Card
                                                                Number</label>
                                                            <input type="text" wire:model.blur="card_number"
                                                                id="card_number"
                                                                class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100"
                                                                placeholder="1234 5678 9012 3456">
                                                            @error('card_number')
                                                                <p class="mt-1 text-sm text-red-500">{{ $message }}
                                                                </p>
                                                            @enderror
                                                        </div>

                                                        <div class="grid grid-cols-3 gap-4">
                                                            <div>
                                                                <label for="expiration_month"
                                                                    class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Exp.
                                                                    Month</label>
                                                                <select wire:model.blur="expiration_month"
                                                                    id="expiration_month"
                                                                    class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                                                    <option value="">Month</option>
                                                                    @for ($i = 1; $i <= 12; $i++)
                                                                        <option value="{{ $i }}">
                                                                            {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                                                        </option>
                                                                    @endfor
                                                                </select>
                                                                @error('expiration_month')
                                                                    <p class="mt-1 text-sm text-red-500">
                                                                        {{ $message }}</p>
                                                                @enderror
                                                            </div>

                                                            <div>
                                                                <label for="expiration_year"
                                                                    class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Exp.
                                                                    Year</label>
                                                                <select wire:model.blur="expiration_year"
                                                                    id="expiration_year"
                                                                    class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                                                    <option value="">Year</option>
                                                                    @for ($i = date('Y'); $i <= date('Y') + 10; $i++)
                                                                        <option value="{{ $i }}">
                                                                            {{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                                @error('expiration_year')
                                                                    <p class="mt-1 text-sm text-red-600">
                                                                        {{ $message }}</p>
                                                                @enderror
                                                            </div>

                                                            <div>
                                                                <label for="cvv"
                                                                    class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">CVV</label>
                                                                <input type="text" wire:model.blur="cvv"
                                                                    id="cvv"
                                                                    class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100"
                                                                    placeholder="123">
                                                                @error('cvv')
                                                                    <p class="mt-1 text-sm text-red-600">
                                                                        {{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Step 4: Review -->
                            @if ($currentStep == 4)
                                <div
                                    class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-neutral-700">
                                    <div class="p-6 sm:p-8">
                                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">
                                            Review Your Appointment
                                        </h3>

                                        <div class="space-y-6">
                                            <!-- Pet Information -->
                                            <div class="bg-gray-50 dark:bg-neutral-700/30 p-4 rounded-lg">
                                                <h4
                                                    class="text-md font-semibold text-gray-800 dark:text-neutral-200 mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">
                                                    Pet Information
                                                </h4>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <p class="text-sm text-gray-600 dark:text-neutral-400">Name</p>
                                                        <p class="font-medium text-gray-800 dark:text-neutral-100">
                                                            {{ $pet_name }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600 dark:text-neutral-400">Type</p>
                                                        <p class="font-medium text-gray-800 dark:text-neutral-100">
                                                            {{ $pet_type }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600 dark:text-neutral-400">Breed
                                                        </p>
                                                        <p class="font-medium text-gray-800 dark:text-neutral-100">
                                                            {{ $pet_breed }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600 dark:text-neutral-400">Gender
                                                        </p>
                                                        <p class="font-medium text-gray-800 dark:text-neutral-100">
                                                            {{ $pet_gender }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600 dark:text-neutral-400">Age</p>
                                                        <p class="font-medium text-gray-800 dark:text-neutral-100">
                                                            {{ $pet_age }} {{ $pet_age_unit }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600 dark:text-neutral-400">Weight
                                                        </p>
                                                        <p class="font-medium text-gray-800 dark:text-neutral-100">
                                                            {{ $pet_weight }} kg</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                                                            Vaccinated</p>
                                                        <p class="font-medium text-gray-800 dark:text-neutral-100">
                                                            {{ $isPetVaccinated ? 'Yes' : 'No' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Appointment Details -->
                                            <div class="bg-gray-50 dark:bg-neutral-700/30 p-4 rounded-lg">
                                                <h4
                                                    class="text-md font-semibold text-gray-800 dark:text-neutral-200 mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">
                                                    Appointment Details
                                                </h4>
                                                <div class="space-y-4">
                                                    <div>
                                                        <p class="text-sm text-gray-600 dark:text-neutral-400">Services
                                                        </p>
                                                        <ul class="mt-1 space-y-1">
                                                            @foreach ($this->getAppointmentCat->whereIn('id', $appointment_category_id) as $service)
                                                                <li class="flex justify-between">
                                                                    <span
                                                                        class="font-medium text-gray-800 dark:text-neutral-100">{{ $service->appoint_cat_name }}</span>
                                                                    <span
                                                                        class="text-gray-600 dark:text-neutral-300">₱{{ number_format($service->price, 2) }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>

                                                    <div class="pt-2 border-t border-gray-200 dark:border-neutral-600">
                                                        <p class="text-sm text-gray-600 dark:text-neutral-400">Payment
                                                            Method</p>
                                                        <p class="font-medium text-gray-800 dark:text-neutral-100">
                                                            {{ $payment_method }}</p>
                                                    </div>

                                                    @if ($payment_method === 'card')
                                                        <div
                                                            class="pt-2 border-t border-gray-200 dark:border-neutral-600">
                                                            <p class="text-sm text-gray-600 dark:text-neutral-400">Card
                                                                Details</p>
                                                            <div class="mt-1 grid grid-cols-2 gap-4">
                                                                <div>
                                                                    <p
                                                                        class="text-xs text-gray-500 dark:text-neutral-400">
                                                                        Name</p>
                                                                    <p
                                                                        class="text-sm font-medium text-gray-800 dark:text-neutral-100">
                                                                        {{ $card_name }}</p>
                                                                </div>
                                                                <div>
                                                                    <p
                                                                        class="text-xs text-gray-500 dark:text-neutral-400">
                                                                        Card Number</p>
                                                                    <p
                                                                        class="text-sm font-medium text-gray-800 dark:text-neutral-100">
                                                                        **** **** **** {{ substr($card_number, -4) }}
                                                                    </p>
                                                                </div>
                                                                <div>
                                                                    <p
                                                                        class="text-xs text-gray-500 dark:text-neutral-400">
                                                                        Expiration</p>
                                                                    <p
                                                                        class="text-sm font-medium text-gray-800 dark:text-neutral-100">
                                                                        {{ $expiration_month }}/{{ $expiration_year }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($amount > 0)
                                                        <div
                                                            class="pt-2 border-t border-gray-200 dark:border-neutral-600">
                                                            <div class="flex justify-between items-center">
                                                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                                                    Total Amount</p>
                                                                <p
                                                                    class="text-lg font-bold text-amber-600 dark:text-amber-400">
                                                                    ₱{{ number_format($amount, 2) }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-600">
                                            <p class="text-sm text-gray-500 dark:text-neutral-400 text-center">
                                                Our team will contact you shortly to confirm your veterinary
                                                appointment.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Navigation Buttons -->
                            <div class="mt-8 flex justify-between">
                                @if ($currentStep > 1)
                                    <button type="button" wire:click="previousStep"
                                        class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800">
                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="16"
                                            height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
                                        </svg>
                                        Previous
                                    </button>
                                @else
                                    <div></div> <!-- Empty div for spacing -->
                                @endif

                                @if ($currentStep < 4)
                                    <button type="button" wire:click="nextStep"
                                        class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-amber-500 text-white hover:bg-amber-600 focus:outline-none focus:bg-amber-600">
                                        Next
                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="16"
                                            height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z" />
                                        </svg>
                                    </button>
                                @else
                                    <button type="submit" wire:target="submit" wire:loading.attr="disabled"
                                        class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-amber-500 text-white hover:bg-amber-600 focus:outline-none focus:bg-amber-600">
                                        <span wire:loading.flex wire:target="submit" class="items-center">
                                            <svg class="animate-spin h-4 w-4 mr-2 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                        </span>
                                        Confirm & Book Appointment
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Fully Booked Message -->
                    <div
                        class="max-w-xl mx-auto mt-10 text-center bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-100 p-6 rounded-xl border border-yellow-300 dark:border-yellow-700">
                        <h2 class="text-2xl font-semibold mb-2">Fully Booked</h2>
                        <p>Sorry, all appointment slots are currently full. Please try again later.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
