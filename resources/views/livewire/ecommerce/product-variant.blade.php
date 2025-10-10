<div>
    <div x-data="{
        showModal: false,
        selectedSize: '',
        quantity: 1,
        resetModal() {
            this.selectedSize = '';
            this.quantity = 1;
        }
    }" class="relative">
        <!-- Button to open modal -->
        <x-button @click="showModal = true" wire:loading.attr="disabled"
            class="flex items-center justify-center gap-2 whitespace-nowrap text-xs sm:text-sm">
            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l1 5h13l1-5h2M5 8l1 9h12l1-9M9 20h.01M15 20h.01"></path>
            </svg>
           Add to Cart
        </x-button>

        <!-- Modal Backdrop -->
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 bg-gray-500 bg-opacity-75 transition-opacity"
            @click.self="showModal = false; resetModal()">
        </div>

        <!-- Modal Container -->
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-cloak
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <div class="relative w-full max-w-md max-h-full">
                <!-- Modal Content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Select Options
                        </h3>
                        <button @click="showModal = false; resetModal()" type="button" wire:click="resetModal"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-4 space-y-4">
                        <!-- Size Selection -->
                        <div>
                            <p class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select
                                Size</p>
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                @foreach ($product->images as $variant)
                                    <div class="flex flex-col items-center">
                                        <!-- Product Image -->
                                        <div class="w-16 h-16 mb-1 overflow-hidden rounded-md">
                                            <img src="{{ asset(Storage::url($variant->url)) }}"
                                                alt="{{ ucwords($variant->sizes) }}" class="object-cover w-full h-full">
                                        </div>
                                        <!-- Size Button -->
                                        <button wire:click="selectSize('{{ $variant->id }}')"
                                            @click="selectedSize = selectedSize === '{{ $variant->id }}' ? '' : '{{ $variant->id }}'"
                                            :disabled="{{ $variant->quantity }} === 0" type="button"
                                            class="w-full px-2 py-1 text-xs border rounded-md focus:outline-none transition-colors"
                                            :class="{
                                                'bg-amber-500 text-white border-amber-500': selectedSize === '{{ $variant->id }}',
                                                'bg-gray-50 border-gray-300 text-gray-900 hover:bg-gray-100': selectedSize !== '{{ $variant->id }}',
                                                'opacity-50 cursor-not-allowed': {{ $variant->quantity }} === 0
                                            }">
                                            {{ ucwords(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $variant->sizes)) }}
                                            <span class="text-xs" :class="{
                                                'text-gray-400': {{ $variant->quantity }} > 0,
                                                'text-red-500': {{ $variant->quantity }} === 0
                                            }">
                                                ({{ $variant->quantity }})
                                            </span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex flex-col p-4 space-y-3 border-t border-gray-200 rounded-b dark:border-gray-600">
                        @if ($price !== 0)
                            <div class="w-full text-center py-2">
                                <span class="text-lg font-bold text-amber-600 dark:text-amber-400">
                                    ₱ {{ number_format($price, 2) }}
                                </span>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row gap-2">
                            <!-- Cancel Button -->
                            <button @click="showModal = false; resetModal()" type="button" wire:click="resetModal"
                                class="flex-1 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                                Cancel
                            </button>
                            
                            <!-- Buy Now Button -->
                            <button @click="showModal = false; resetModal()" 
                                :disabled="!selectedSize" 
                                type="button"
                                wire:click="buyNowWithSize"
                                wire:loading.attr="disabled"
                                class="flex-1 text-white bg-blue-600  hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-ble-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-ble-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <div wire:loading.remove wire:target="buyNowWithSize">
                                    Buy Now
                                </div>
                                <div wire:loading wire:target="buyNowWithSize" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </div>
                            </button>
                            
                            <!-- Add to Cart Button -->
                            <button @click="showModal = false; resetModal()" 
                                :disabled="!selectedSize" 
                                type="button"
                                wire:click="addToCart"
                                wire:loading.attr="disabled"
                                class="flex-1 text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <div wire:loading.remove wire:target="addToCart">
                                    Add to Cart
                                </div>
                                <div wire:loading wire:target="addToCart" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Adding...
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>