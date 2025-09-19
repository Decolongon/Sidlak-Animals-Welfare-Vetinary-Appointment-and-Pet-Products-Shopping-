<x-action-section>
    <x-slot name="title">
        {{ __('Address Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account address information. Current Address: ' . $this->getCompleteAddress()) }}
    </x-slot>

    <x-slot name="content">
        @if (session()->has('message'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('message') }}
            </div>
        @endif
        <form wire:submit.prevent="updateAddress">
            <div class="col-span-6 sm:col-span-4">
                <x-label for="city" value="{{ __('City') }}" />

                <div class="relative" x-data="{ open: false }">
                    <div @click="open = !open"
                        class="border rounded-lg px-4 py-3 cursor-pointer bg-white dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 flex justify-between items-center">
                        <span>{{ $selectedCity ? $cities->firstWhere('code', $selectedCity)?->name : 'Select or Search City' }}</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>

                    <div x-show="open" @click.outside="open = false; $wire.set('searchCity', '')" x-transition
                        class="absolute z-10 mt-1 w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg max-h-60 overflow-hidden">
                        <div class="relative p-2 border-b border-gray-200 dark:border-neutral-700">
                            <input type="text" wire:model.live="searchCity" placeholder="Search City"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none bg-white dark:bg-neutral-900 text-gray-700 dark:text-neutral-200">
                            <button type="button"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-neutral-400 dark:hover:text-neutral-200"
                                @click="$wire.set('searchCity', '')">&times;</button>
                        </div>

                        <ul class="max-h-48 overflow-y-auto py-1">
                            @forelse ($cities as $city)
                                <li class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-800 cursor-pointer text-gray-700 dark:text-neutral-200"
                                    wire:click="selectCity('{{ $city->code }}'); open=false">
                                    {{ $city->name }}
                                </li>
                            @empty
                                <li class="px-4 py-2 text-gray-500 dark:text-neutral-400 text-center">No City found</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <x-input-error for="selectedCity" class="mt-2" />
            </div>

            @if ($selectedCity)
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <x-label for="barangay" value="{{ __('Barangay') }}" />

                    <div class="relative" x-data="{ open: false }">
                        <div @click="open = !open"
                            class="border rounded-lg px-4 py-3 cursor-pointer bg-white dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 flex justify-between items-center">
                            <span>{{ $selectedBarangay ? $barangays->firstWhere('name', $selectedBarangay)?->name : 'Select or Search Barangay' }}</span>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <div x-show="open" @click.outside="open = false; $wire.set('searchBrgy', '')" x-transition
                            class="absolute z-10 mt-1 w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg max-h-60 overflow-hidden">
                            <div class="relative p-2 border-b border-gray-200 dark:border-neutral-700">
                                <input type="text" wire:model.live="searchBrgy" placeholder="Search Barangay"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none bg-white dark:bg-neutral-900 text-gray-700 dark:text-neutral-200">
                                <button type="button"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-neutral-400 dark:hover:text-neutral-200"
                                    @click="$wire.set('searchBrgy', '')">&times;</button>
                            </div>

                            <ul class="max-h-48 overflow-y-auto py-1">
                                @forelse ($barangays as $brgy)
                                    <li class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-800 cursor-pointer text-gray-700 dark:text-neutral-200"
                                        wire:click="selectBrgy('{{ $brgy->name }}'); open=false">
                                        {{ $brgy->name }}
                                    </li>
                                @empty
                                    <li class="px-4 py-2 text-gray-500 dark:text-neutral-400 text-center">No Barangay
                                        Found</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <x-input-error for="selectedBarangay" class="mt-2" />
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <x-action-message class="mr-3" on="saved">
                    {{ __('Saved.') }}
                </x-action-message>

                <x-button type="submit" wire:loading.attr="disabled">
                    {{ __('Save') }}
                </x-button>
            </div>
        </form>
    </x-slot>
</x-action-section>
