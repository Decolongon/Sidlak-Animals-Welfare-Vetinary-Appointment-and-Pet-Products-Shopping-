<div>

    @livewire('vet-appointment.vission-and-services')

    <livewire:vet-appointment.clinic-hours :schedules="$schedules" />

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
                    @if (session()->has('message'))
                        {{-- <div class="flex items-center gap-4 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-6 -mb-6"> --}}
                        {{-- <div class="bg-green-100 border border-green-400 px-4 py-3 rounded relative" role="alert"> --}}
                        <p class="text-green-700">{{ session('message') }}</p>
                        {{-- </div> --}}
                        {{-- </div> --}}
                    @endif
                </h2>
                @if ($schedules['schedules'] && $schedules['isOpen'] && !empty($this->getAllAppointmentsForDropdown))
                    {{-- @if ($schedules['schedules'] && $schedules['appointmentsCount'] < $schedules['schedules']->num_customers && !empty($this->getAllAppointmentsForDropdown)) --}}
                    <div class="flex items-start gap-4 mb-6">
                        <!-- Auto-fill Dropdown -->
                        <div class="relative flex-1 max-w-xs" x-data="{ open: false }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                Select Previous Pet
                            </label>
                            <div @click="open = !open"
                                class="w-full px-3 py-2.5 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-gray-900 dark:text-neutral-200 cursor-pointer flex justify-between items-center hover:border-gray-400 dark:hover:border-neutral-500 transition-colors text-sm">
                                <span
                                    class="{{ $selectedAppointmentId ? 'text-gray-900 dark:text-neutral-200' : 'text-gray-500 dark:text-neutral-400' }}">
                                    @if ($selectedAppointmentId && $this->getSelectedPrevAppointment)
                                        {{ $this->getSelectedPrevAppointment->pet_name }}
                                    @else
                                        Select Previous Pet
                                    @endif
                                </span>
                                <div class="flex items-center gap-1">
                                    @if ($selectedAppointmentId)
                                        <button type="button" wire:click="clearSelectedAppointment" @click.stop
                                            class="p-1 text-gray-400 hover:text-red-500 transition-colors"
                                            title="Clear selection">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>

                            <div x-show="open" @click.outside="open = false" x-transition x-cloak
                                class="absolute z-20 w-full mt-1 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg max-h-64 overflow-hidden">

                                <!-- Search Input -->
                                <div class="p-2 border-b border-gray-200 dark:border-neutral-700">
                                    <div class="relative">
                                        <input type="text" wire:model.live="searchTerm" placeholder="Search pets..."
                                            class="w-full pl-8 pr-3 py-1.5 border border-gray-300 dark:border-neutral-600 rounded-md bg-white dark:bg-neutral-700 text-gray-900 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                                        <svg class="absolute left-2.5 top-1/2 transform -translate-y-1/2 w-3.5 h-3.5 text-gray-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Loading State -->
                                <div wire:loading class="p-3 text-center text-gray-500 dark:text-neutral-400 text-sm">
                                    Loading...
                                </div>

                                <!-- Appointment List -->
                                <div wire:loading.remove class="max-h-48 overflow-y-auto py-1">
                                    @forelse($this->getAllAppointmentsForDropdown as $appointment)
                                        <div wire:key="{{ $appointment['id'] }}" class="px-3 py-2 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer border-b border-gray-100 dark:border-neutral-600 last:border-b-0 transition-colors {{ $selectedAppointmentId == $appointment['id'] ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' : '' }}"
                                            wire:click="autoFillFromAppointment({{ $appointment['id'] }}); open=false">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p
                                                        class="font-medium text-gray-800 dark:text-neutral-200 text-sm flex items-center gap-2">
                                                        {{ $appointment['pet_name'] }}
                                                        @if ($selectedAppointmentId == $appointment['id'])
                                                            <span class="text-amber-600 dark:text-amber-400">
                                                                <svg class="w-3 h-3" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                        clip-rule="evenodd"></path>
                                                                </svg>
                                                            </span>
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-gray-600 dark:text-neutral-400 mt-0.5">
                                                        {{ $appointment['pet_type'] }} • {{ $appointment['pet_breed'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-neutral-500 mt-0.5">
                                                        {{ \Carbon\Carbon::parse($appointment['created_at'])->format('M j, Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-3 py-2 text-gray-500 dark:text-neutral-400 text-center text-sm">
                                            No previous appointments found
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                @if ($schedules['schedules'] && $schedules['isOpen'])
                    {{-- @if ($schedules['schedules'] && $schedules['appointmentsCount'] < $schedules['schedules']->num_customers) --}}
                    <div>
                        <!-- Stepper -->
                        <div class="max-w-2xl mx-auto mb-8">
                            <div class="flex justify-between">
                                @foreach ([1, 2, 3, 4, 5] as $step)
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
                                                Schedule
                                            @endif
                                            @if ($step == 4)
                                                Payment
                                            @endif
                                            @if ($step == 5)
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
                                    style="width: {{ (($currentStep - 1) / 4) * 100 }}%"></div>
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
                                            <option value="">Select Pet Type</option>
                                            <option value="dog" {{ $pet_type == 'dog' ? 'selected' : '' }}>Dog
                                            </option>
                                            <option value="cat" {{ $pet_type == 'cat' ? 'selected' : '' }}>Cat
                                            </option>
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
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ $pet_gender == 'male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="female" {{ $pet_gender == 'female' ? 'selected' : '' }}>
                                                Female</option>
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

                                            <select name="pet_age_unit" id="pet_age_unit"
                                                wire:model.live="pet_age_unit"
                                                class="py-2.5 px-4 border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                                <option value="">Select Years/Months</option>
                                                <option value="years old"
                                                    {{ $pet_age_unit == 'years old' ? 'selected' : '' }}>Years</option>
                                                <option value="months"
                                                    {{ $pet_age_unit == 'months' ? 'selected' : '' }}>
                                                    Months</option>
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
                                            class="block text-sm font-medium text-gray-700 dark:text-neutral-300"> Is
                                            Pet Vaccinated?</label>
                                        <select id="isPetVaccinated" name="isPetVaccinated"
                                            wire:model="isPetVaccinated"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                            <option value="">Select</option>
                                            <option value="1"
                                                {{ $isPetVaccinated === true || $isPetVaccinated == '1' ? 'selected' : '' }}>
                                                Yes</option>
                                            <option value="0"
                                                {{ $isPetVaccinated === true || $isPetVaccinated == '0' ? 'selected' : '' }}>
                                                No</option>
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

                                        {{-- <select x-ref="select" name="appointment_category_id"
                                            id="appointment_category_id" multiple
                                            wire:model.live="appointment_category_id"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                            @foreach ($this->getAppointmentCat as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ ucwords($category->appoint_cat_name) }}
                                                </option>
                                            @endforeach
                                        </select> --}}

                                        <select name="appointment_category_id" id="appointment_category_id"
                                            wire:model.live="appointment_category_id"
                                            class="py-2.5 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:text-neutral-100">
                                            <option value="">
                                                Select Service
                                            </option>
                                            @foreach ($this->getAppointmentCat as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ ucwords($category->appoint_cat_name) }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('appointment_category_id')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    @if (!empty($appointment_category_id))
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

                            <!-- Step 3: Schedule -->
                            @if ($currentStep == 3)
                                <div class="space-y-6">
                                    <div
                                        class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mb-6">
                                        <h3 class="font-medium text-amber-800 dark:text-amber-200 mb-2">
                                            Select Your Preferred Schedule
                                        </h3>
                                        <p class="text-amber-700 dark:text-amber-300 text-sm">
                                            Choose an available date and time for your appointment.
                                        </p>
                                    </div>

                                    <!-- Calendar and Time Selection -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <!-- Calendar -->
                                        <div class="space-y-4">
                                            <h4 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                                                Select Date
                                            </h4>
                                            <div
                                                class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-4">
                                                <!-- Calendar navigation -->
                                                <div class="flex items-center justify-between mb-4">
                                                    <button type="button" wire:click="previousMonth"
                                                        class="p-2 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5 text-gray-600 dark:text-neutral-400"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                        </svg>
                                                    </button>
                                                    <h5
                                                        class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                                                        {{ $calendarMonth }}
                                                    </h5>
                                                    <button type="button" wire:click="nextMonth"
                                                        class="p-2 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5 text-gray-600 dark:text-neutral-400"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <!-- Calendar grid -->
                                                <div class="grid grid-cols-7 gap-1 mb-2">
                                                    @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                                        <div
                                                            class="text-center text-sm font-medium text-gray-500 dark:text-neutral-400 py-2">
                                                            {{ $day }}
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="grid grid-cols-7 gap-1">
                                                    @foreach ($calendarDays as $day)
                                                        @if ($day['isCurrentMonth'])
                                                            <button type="button"
                                                                wire:click="selectDate('{{ $day['date'] }}')"
                                                                @if (
                                                                    !$day['isAvailable'] ||
                                                                        (Carbon\Carbon::parse($day['date'])->isPast() && !Carbon\Carbon::parse($day['date'])->isToday())) disabled @endif
                                                                class="p-2 text-center rounded-lg transition-all duration-200
                                                                    {{ $day['isAvailable']
                                                                        ? ($selectedDate == $day['date']
                                                                            ? 'bg-amber-500 text-white'
                                                                            : 'hover:bg-amber-100 dark:hover:bg-amber-900/30 text-gray-900 dark:text-neutral-100')
                                                                        : 'bg-gray-100 dark:bg-neutral-700 text-gray-400 dark:text-neutral-500 cursor-not-allowed' }}
                                                                    {{ $day['isToday'] ? 'ring-2 ring-amber-300 dark:ring-amber-600' : '' }}">
                                                                <div class="text-sm font-medium">{{ $day['day'] }}
                                                                </div>
                                                                {{-- @if ($day['hasAppointments'])
                                                                    <div
                                                                        class="w-1 h-1 bg-amber-400 rounded-full mx-auto mt-1">
                                                                    </div>
                                                                @endif --}}
                                                            </button>
                                                        @else
                                                            <div class="p-2"></div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Time Slots -->
                                        <div class="space-y-4">
                                            <h4 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                                                Available Time Slots
                                            </h4>

                                            @if ($selectedDate)
                                                <div
                                                    class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-4">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <span class="text-sm text-gray-600 dark:text-neutral-400">
                                                            {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                                                        </span>
                                                        <span
                                                            class="text-sm font-medium text-amber-600 dark:text-amber-400">
                                                            {{ $this->countAvailableSlots }} slots available
                                                        </span>
                                                    </div>

                                                    @if ($availableSlots->count() > 0)
                                                        <div
                                                            class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-60 overflow-y-auto">
                                                            @foreach ($availableSlots as $slot)
                                                                <button type="button"
                                                                    @if (
                                                                        $slot['isBooked'] ||
                                                                            (\Carbon\Carbon::parse($selectedDate)->isToday() && \Carbon\Carbon::parse($slot['time'])->lt(now()))) disabled @endif
                                                                    wire:click="selectTimeSlot('{{ $slot['time'] }}')"
                                                                    class="p-3 text-center border rounded-lg transition-all duration-200
            {{ $selectedTime == $slot['time']
                ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300'
                : 'border-gray-200 dark:border-neutral-600 hover:border-amber-300 dark:hover:border-amber-600 text-gray-700 dark:text-neutral-300' }}
            {{ $slot['isBooked'] || (\Carbon\Carbon::parse($selectedDate)->isToday() && \Carbon\Carbon::parse($slot['time'])->lt(now())) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-amber-50 dark:hover:bg-amber-900/20' }}">
                                                                    <div class="text-sm font-medium">
                                                                        {{ $slot['display_time'] }}</div>
                                                                    @if ($slot['isBooked'])
                                                                        <div class="text-xs text-red-500 mt-1">Booked
                                                                        </div>
                                                                    @elseif(\Carbon\Carbon::parse($selectedDate)->isToday() && \Carbon\Carbon::parse($slot['time'])->lt(now()))
                                                                        <div class="text-xs text-gray-500 mt-1">Past
                                                                        </div>
                                                                    @else
                                                                        <div class="text-xs text-green-500 mt-1">
                                                                            Available</div>
                                                                    @endif
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-center py-8">
                                                            <svg class="w-12 h-12 text-gray-400 dark:text-neutral-600 mx-auto mb-3"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                            <p class="text-gray-500 dark:text-neutral-400">No available
                                                                time slots for this date.</p>
                                                            <p
                                                                class="text-sm text-gray-400 dark:text-neutral-500 mt-1">
                                                                Please select another date.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div
                                                    class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-8 text-center">
                                                    <svg class="w-16 h-16 text-gray-300 dark:text-neutral-600 mx-auto mb-4"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    <p class="text-gray-500 dark:text-neutral-400">Please select a date
                                                        to view available time slots.</p>
                                                </div>
                                            @endif

                                            @if ($selectedDate && $selectedTime)
                                                <div
                                                    class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                                    <div class="flex items-center">
                                                        <svg class="w-5 h-5 text-green-500 dark:text-green-400 mr-2"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <div>
                                                            <p class="font-medium text-green-800 dark:text-green-200">
                                                                Appointment Scheduled
                                                            </p>
                                                            <p class="text-sm text-green-700 dark:text-green-300">
                                                                {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                                                                at
                                                                {{ \Carbon\Carbon::parse($selectedTime)->format('g:i A') }}

                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @error('selectedDate')
                                        <p class="text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                    @error('selectedTime')
                                        <p class="text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Step 4: Payment -->
                            @if ($currentStep == 4)
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

                            <!-- Step 5: Review -->
                            @if ($currentStep == 5)
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

                                                    <!-- Schedule Information -->
                                                    <div class="pt-2 border-t border-gray-200 dark:border-neutral-600">
                                                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                                                            Appointment Schedule</p>
                                                        <p class="font-medium text-gray-800 dark:text-neutral-100">
                                                            {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                                                            at
                                                            {{ \Carbon\Carbon::parse($selectedTime)->format('g:i A') }}
                                                        </p>
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

                                @if ($currentStep < 5)
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
                                                    d="M4 12a8 8 0 018-8v8H4z">
                                                </path>
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
                        <h2 class="text-2xl font-semibold mb-2">Closed</h2>
                        <p>Sorry, the clinic is currently closed.</p>

                    </div>
                @endif
            </div>
        </div>
    @endif
    {{-- Sorry, the clinic is currently closed. Please check our clinic hours and try again during open hours. --}}
</div>
