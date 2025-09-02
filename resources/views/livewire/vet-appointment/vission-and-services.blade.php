<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}

    <!-- Icon Blocks -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto">
            <!-- Grid -->
            <div class="grid gap-12">
                <div>
                    <h2 class="text-3xl text-gray-800 font-bold lg:text-4xl dark:text-white">
                        Our vision
                    </h2>
                    <p class="mt-3 text-gray-800 dark:text-neutral-400">
                        We’re here to keep your pets healthy and happy. From regular checkups to urgent care, our
                        veterinary clinic makes it easy to book appointments and get trusted, compassionate care. Let us
                        be your partner in giving your pets the love and care they deserve.
                    </p>
                </div>

                <div class="space-y-6 lg:space-y-10">
                    <!-- Icon Block -->
                    <div class="flex gap-x-5 sm:gap-x-8">
                        <svg class="shrink-0 mt-2 size-6 text-gray-800 dark:text-white" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z" />
                            <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2" />
                            <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2" />
                            <path d="M10 6h4" />
                            <path d="M10 10h4" />
                            <path d="M10 14h4" />
                            <path d="M10 18h4" />
                        </svg>
                        <div class="grow">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-neutral-200">
                                Quality veterinary care
                            </h3>
                            <p class="mt-1 text-gray-600 dark:text-neutral-400">
                                Our experienced vets provide gentle, expert care for your furry friends, no matter the
                                need.
                            </p>
                        </div>
                    </div>
                    <!-- End Icon Block -->

                    <!-- Icon Block -->
                    <div class="flex gap-x-5 sm:gap-x-8">
                        <svg class="shrink-0 mt-2 size-6 text-gray-800 dark:text-white"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <div class="grow">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-neutral-200">
                                Caring for your pet like family
                            </h3>
                            <p class="mt-1 text-gray-600 dark:text-neutral-400">
                                We build lasting relationships with pets and owners through compassion and trust.
                            </p>
                        </div>
                    </div>
                    <!-- End Icon Block -->

                    <!-- Icon Block -->
                    <div class="flex gap-x-5 sm:gap-x-8">
                        <svg class="shrink-0 mt-2 size-6 text-gray-800 dark:text-white"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M7 10v12" />
                            <path
                                d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2h0a3.13 3.13 0 0 1 3 3.88Z" />
                        </svg>
                        <div class="grow">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-neutral-200">
                                Easy booking
                            </h3>
                            <p class="mt-1 text-gray-600 dark:text-neutral-400">
                                Book appointments online in minutes. We make every step clear and stress-free for pet
                                owners.

                            </p>
                        </div>
                    </div>
                    <!-- End Icon Block -->
                </div>
            </div>
            <!-- End Grid -->
        </div>
    </div>
    <!-- End Icon Blocks -->

    <!-- Card Blog -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Title -->
        <div class="max-w-2xl text-center mx-auto mb-10 lg:mb-14">
            <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">Services</h2>
            <p class="mt-1 text-gray-600 dark:text-neutral-400">We care for your pets like family – discover our most
                trusted veterinary services.</p>
        </div>
        <!-- End Title -->

        <!-- Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10 mb-10 lg:mb-14">

            <!-- Card -->
            @foreach ($this->getServiceSched as $service)
                <a wire:navigate href="{{ route('service',['appoint_cat_slug' => $service->appoint_cat_slug]) }}"
                    class="group flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl hover:shadow-md focus:outline-hidden focus:shadow-md transition dark:bg-neutral-900 dark:border-neutral-800">
                    <div class="h-48 overflow-hidden">
                        <img class="w-full h-full object-cover rounded-t-xl"
                            src="{{ asset(Storage::url($service->img)) }}"
                            alt="{{ $service->appoint_cat_slug }}">
                    </div>
                    <div class="p-4 md:p-5">
                        <p class="mt-2 text-xs uppercase text-gray-600 dark:text-neutral-400">
                            {{ ucwords($service->appoint_cat_name) }} <br>
                            Assigned Doctor: {{ucwords($service->doctor->name)}}
                        </p>
                        <h3
                            class="mt-2 text-lg font-medium text-gray-800 group-hover:text-blue-600 dark:text-neutral-300 dark:group-hover:text-white">
                            {{ ucwords(Str::limit($service->appoint_cat_description,50)) }}
                        </h3>
                    </div>
                </a>
            @endforeach
            <!-- End Card -->

            {{-- <!-- Card -->
            <a
                class="group flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl hover:shadow-md focus:outline-hidden focus:shadow-md transition dark:bg-neutral-900 dark:border-neutral-800">
                <div class="h-48 overflow-hidden">
                    <img class="w-full h-full object-cover rounded-t-xl"
                        src="https://images.pexels.com/photos/7468979/pexels-photo-7468979.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2"
                        alt="Blog Image">
                </div>
                <div class="p-4 md:p-5">
                    <p class="mt-2 text-xs uppercase text-gray-600 dark:text-neutral-400">
                        Check-ups
                    </p>
                    <h3
                        class="mt-2 text-lg font-medium text-gray-800 group-hover:text-blue-600 dark:text-neutral-300 dark:group-hover:text-white">
                        Regular wellness exams keep your furry friends healthy and happy.
                    </h3>
                </div>
            </a>
            <!-- End Card -->

            <!-- Card -->
            <a
                class="group flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl hover:shadow-md focus:outline-hidden focus:shadow-md transition dark:bg-neutral-900 dark:border-neutral-800">
                <div class="h-48 overflow-hidden">
                    <img class="w-full h-full object-cover rounded-t-xl"
                        src="https://images.pexels.com/photos/6131566/pexels-photo-6131566.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2"
                        alt="Blog Image">
                </div>
                <div class="p-4 md:p-5">
                    <p class="mt-2 text-xs uppercase text-gray-600 dark:text-neutral-400">
                        Grooming
                    </p>
                    <h3
                        class="mt-2 text-lg font-medium text-gray-800 group-hover:text-blue-600 dark:text-neutral-300 dark:group-hover:text-white">
                        Keep your pet clean, comfortable, and camera-ready with expert grooming.
                    </h3>
                </div>
            </a> --}}
            <!-- End Card -->

            <!-- Card -->
            @if (!Auth::user())
                <div class="text-center mb-8 col-span-full">
                    <div
                        class="inline-block bg-white border border-gray-200 shadow-2xs rounded-full dark:bg-neutral-900 dark:border-neutral-800">
                        <div class="py-3 px-4 flex items-center gap-x-2">
                            <p class="text-gray-600 dark:text-neutral-400">
                                Want to book an appointment?You must login first
                            </p>
                            <a class="inline-flex items-center gap-x-1.5 text-blue-600 decoration-2 hover:underline focus:outline-hidden focus:underline font-medium dark:text-blue-500"
                                href="{{ route('filament.auth.auth.login') }}">
                                click here
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Card -->
            @endif


        </div>
        <!-- End Grid -->

    </div>
</div>
