<div class="space-y-6 p-6 rounded-lg shadow-md bg-white dark:bg-transparent">
    {{-- Nothing in the world is as soft and yielding as water. --}}
    @foreach ($this->getSingleService as $single_service)
        <div class="space-y-4">
            <!-- Service Name -->
            <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $single_service->appoint_cat_name }}
            </div>

            <!-- Service Description -->
            <p class="text-gray-700 dark:text-gray-300 text-base">
                {{ $single_service->appoint_cat_description }}
            </p>

            <!-- Doctor's Name and Availability -->
            <p class="text-xl font-medium text-gray-900 dark:text-white mt-4">
               When is Dr. {{ ucwords($single_service->doctor->name) }} available?
            </p>

            <!-- Calendar View for Days -->
            <div class="grid grid-cols-7 gap-4 mt-4 text-center">
                @foreach ($single_service->doctorschedules as $sched)
                    {{-- @php
                        $days = is_array($sched->days) ? $sched->days : [$sched->days];
                    @endphp --}}

                    <!-- Loop through each day in the schedule -->
                    @foreach (is_array($sched->days) ? $sched->days : [$sched->days] as $day)
                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                            <!-- Day Name -->
                            <div class="font-semibold text-gray-800 dark:text-white">
                                {{ ucfirst($day) }}
                            </div>
                            
                            <!-- Schedule Times -->
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                <div class="font-semibold text-gray-800 dark:text-white">Time:</div>
                                <p>{{ date('h:i A', strtotime($sched->start_time)) }} - {{ date('h:i A', strtotime($sched->end_time)) }}</p>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    @endforeach
</div>
