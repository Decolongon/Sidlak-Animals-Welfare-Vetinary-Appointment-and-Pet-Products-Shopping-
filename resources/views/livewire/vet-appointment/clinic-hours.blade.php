<div>
    {{-- The best athlete wants his opponent at his best. --}}
      <div x-data="{ showSchedule: false }" class="mb-8">
        <div class="flex flex-col items-center">
            <!-- Enhanced Button -->
            <button @click="showSchedule = !showSchedule"
                class="mb-4 px-6 py-3 bg-amber-400 hover:bg-amber-500 text-black font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2"
                :aria-expanded="showSchedule">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>View Clinic Hours</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-300" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" :class="{ 'rotate-180': showSchedule }">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Schedule Card  -->
            <div x-show="showSchedule" x-transition:enter.duration.500ms x-transition:leave.duration.400ms
                class="p-5 w-full max-w-md bg-white dark:bg-neutral-800 rounded-xl shadow-md border border-gray-200 dark:border-neutral-700">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">Clinic Hours</h2>
                        <p class="text-sm text-gray-500 dark:text-neutral-400">Veterinary service availability</p>
                    </div>

                    <!-- Status Badge -->
                    <span
                        class="px-3 py-1 rounded-full text-xs font-semibold 
            {{ $schedules['schedules']?->vet_schedule_open <= now() && $schedules['schedules']?->vet_schedule_close >= now()
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                        {{ $schedules['schedules']?->vet_schedule_open <= now() && $schedules['schedules']?->vet_schedule_close >= now()
                            ? 'OPEN NOW'
                            : 'CLOSED' }}
                    </span>
                </div>

                @if ($schedules['schedules']?->vet_schedule_open <= now() && $schedules['schedules']?->vet_schedule_close >= now())
                    <!-- Schedule Card (only shown when open) -->
                    <div class="bg-gray-50 dark:bg-neutral-700/50 p-4 rounded-lg mb-3">
                        <div class="grid grid-cols-1 gap-3">
                            <!-- Opening Time -->
                            <div class="flex items-center">
                                <div class="mr-3 p-2 rounded-lg bg-blue-100 dark:bg-blue-900/50">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Opens at</p>
                                    <p class="font-semibold text-gray-800 dark:text-white">
                                        {{ $schedules['schedules']?->vet_schedule_open?->format('g:i A') ?? '--:-- --' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Closing Time -->
                            <div class="flex items-center">
                                <div class="mr-3 p-2 rounded-lg bg-blue-100 dark:bg-blue-900/50">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Closes at</p>
                                    <p class="font-semibold text-gray-800 dark:text-white">
                                        {{ $schedules['schedules']?->vet_schedule_close?->format('g:i A') ?? '--:-- --' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info (only shown when open) -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-neutral-300">
                            Today: {{ $schedules['schedules']?->vet_schedule_open?->format('l, M d') ?? '' }}
                        </p>
                        {{-- <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                <span class="font-medium">Dr. {{ ucwords($schedules['schedules']?->user?->name) ?? 'Not specified' }}</span>
            </p> --}}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
