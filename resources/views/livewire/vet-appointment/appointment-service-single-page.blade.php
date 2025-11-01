<div
    class="space-y-8 p-8 rounded-2xl shadow-lg bg-white dark:bg-neutral-800/50 border border-gray-100 dark:border-neutral-700 backdrop-blur-sm mx-auto max-w-7xl">
    @foreach ($this->getSingleService as $single_service)
        <div
            class="space-y-6 p-6 rounded-xl bg-gradient-to-br from-gray-50 to-white dark:from-neutral-800 dark:to-neutral-900 border border-gray-200 dark:border-neutral-700 shadow-sm hover:shadow-md transition-all duration-300">

            <!-- Service Header -->
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <!-- Service Name with Icon -->
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $single_service->appoint_cat_name }}
                        </h2>
                    </div>

                    <!-- Service Description -->
                    <div
                        class="text-gray-700 dark:text-gray-300 text-base lg:text-lg leading-relaxed whitespace-pre-wrap bg-white dark:bg-neutral-800 p-4 rounded-lg border border-gray-200 dark:border-neutral-600 prose prose-sm max-w-none">
                        {!! nl2br(e($single_service->appoint_cat_description)) !!}
                    </div>
                </div>
            </div>

            <!-- Doctor's Schedule Section -->
            @php
                // Get schedules for this service's doctor from the getDoctorShedules computed property
$serviceDoctorSchedules = $this->getDoctorShedules->where('doctor_id', $single_service->doctor_id);
            @endphp

            @if ($serviceDoctorSchedules->isNotEmpty())
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-neutral-600">
                    <!-- Doctor Header -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-white">
                            Dr. {{ ucwords($single_service->doctor->name) }}'s Schedule
                        </h3>
                    </div>

                    <!-- Days Grid - Responsive -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-7 gap-4">
                        @php
                            $allDays = [];
                            foreach ($serviceDoctorSchedules as $sched) {
                                $days = is_array($sched->days) ? $sched->days : [$sched->days];
                                foreach ($days as $day) {
                                    if (!isset($allDays[$day])) {
                                        $allDays[$day] = [];
                                    }
                                    $allDays[$day][] = [
                                        'start_time' => $sched->start_time,
                                        'end_time' => $sched->end_time,
                                    ];
                                }
                            }

                            // Define day order and ensure all days are in the correct order
                            $dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            $orderedDays = [];
                            foreach ($dayOrder as $day) {
                                if (isset($allDays[$day])) {
                                    $orderedDays[$day] = $allDays[$day];
                                }
                            }
                        @endphp

                        @foreach ($orderedDays as $day => $schedules)
                            <div
                                class="bg-gradient-to-br from-white to-gray-50 dark:from-neutral-800 dark:to-neutral-700 border-2 border-gray-200 dark:border-neutral-600 rounded-xl p-4 shadow-sm hover:shadow-lg hover:border-amber-300 dark:hover:border-amber-600 transition-all duration-300 group">
                                <!-- Day Header -->
                                <div class="text-center mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">
                                    <div
                                        class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Day
                                    </div>
                                    <div
                                        class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                        {{ ucfirst($day) }}
                                    </div>
                                </div>

                                <!-- Schedule Times -->
                                <div class="space-y-2">
                                    <div class="text-center">
                                        <div
                                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                                            Available Hours
                                        </div>
                                        @foreach ($schedules as $schedule)
                                            <div
                                                class="text-sm font-medium text-gray-700 dark:text-gray-300 bg-amber-50 dark:bg-amber-900/20 rounded-lg py-2 px-3 mb-2 border border-amber-200 dark:border-amber-800">
                                                <div class="flex items-center justify-center gap-2">
                                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>
                                                        {{ date('h:i A', strtotime($schedule['start_time'])) }} -
                                                        {{ date('h:i A', strtotime($schedule['end_time'])) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-neutral-600">
                    <div
                        class="text-center p-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-yellow-700 dark:text-yellow-400">
                            No current schedule available for Dr. {{ $single_service->doctor->name ?? 'this doctor' }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
