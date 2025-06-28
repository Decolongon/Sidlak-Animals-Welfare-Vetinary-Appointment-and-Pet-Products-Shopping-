<div id="hs-offcanvas-custom-backdrop-color" class="hs-overlay hs-overlay-open:translate-x-0 hs-overlay-backdrop-open:bg-black/50 dark:hs-overlay-backdrop-open:bg-black/30 hidden translate-x-full fixed top-0 end-0 transition-all duration-300 transform h-full max-w-xs w-full z-[80] bg-white border-s dark:bg-neutral-800 dark:border-neutral-700" role="dialog" tabindex="-1" aria-labelledby="hs-offcanvas-custom-backdrop-color-label" wire:ignore.self>
  <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
      <h3 id="hs-offcanvas-custom-backdrop-color-label" class="font-bold text-gray-800 dark:text-white">
          Shopping Cart
      </h3>
      <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#hs-offcanvas-custom-backdrop-color">
          <span class="sr-only">Close</span>
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 6 6 18"></path>
              <path d="m6 6 12 12"></path>
          </svg>
      </button>
  </div>
  <div class="p-4">
      
       <!-- Select All Checkbox -->
       @if (count($carts) > 0)
        <div class="flex items-center mb-4">
          <input type="checkbox" name="selectAll" wire:model="selectAll" wire:click="toggleSelectAll" class="mr-2 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring focus:ring-indigo-200">
          <p class="text-gray-700 dark:text-white text-sm font-medium">Select All</p>
        </div>
       @endif
     

      {{-- <div class="flex flex-col gap-4"> --}}
        <div class="flex flex-col gap-4 max-h-[70vh] overflow-y-auto pr-2 pb-28">
          @forelse ($carts as $cart)
          <div class="p-4 border rounded-lg shadow dark:border-neutral-700 dark:shadow-gray-900">
              <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                      <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring focus:ring-indigo-200" name="selectedItems" wire:model="selectedItems" value="{{ $cart->id }}">
                      <img src="{{ asset(Storage::url($cart->product->images[0]->url)) }}" alt="{{ $cart->product->prod_slug }}" class="w-[40px] h-[40px] object-cover rounded-full">
                      <div>
                          <div class="font-medium text-gray-800 dark:text-neutral-200">{{ ucwords($cart->product->prod_name) }}</div>
                          <div class="text-sm text-gray-600 dark:text-neutral-400">
                           
                               @if($cart->product->discounted_price !== null)
                                {{-- <span class="original-price text-muted text-decoration-line-through">
                                    ₱{{ number_format($cart->product->prod_price, 2) }}
                                </span> --}}
                                  <del class="text-gray-500 dark:text-neutral-400">
                                    ₱{{ number_format( $cart->product->prod_price, 2).' ' }}
                                </del>
                                <span class="discounted-price text-danger">
                                    ₱{{ number_format($cart->quantity * $cart->product->discounted_price, 2) }}
                                </span>
                                <small class="text-success d-block">{{ $cart->product->discount_label }}</small>
                            @else
                                ₱{{ number_format($cart->quantity * $cart->product->prod_price, 2) }}
                            @endif
                                {{-- ₱{{ number_format($cart->quantity * $cart->product->prod_price, 2) }} --}}
                            </div>
                      </div>
                  </div>
              </div>

             <div x-data="{ loading: false }"
                x-on:livewire:start.decreaseQuantity="loading = true"
                x-on:livewire:finish.decreaseQuantity="loading = false"
                x-on:livewire:start.increaseQuantity="loading = true"
                x-on:livewire:finish.increaseQuantity="loading = false"
             
                class="mt-3 flex items-center justify-end gap-2">
                
                {{-- <span wire:loading wire:target="decreaseQuantity({{ $cart->id }}), increaseQuantity({{ $cart->id }})" class="inline-block">
                        <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-gray-700 dark:text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                </span>
                <span wire:loading.remove wire:target="decreaseQuantity({{ $cart->id }}),increaseQuantity({{ $cart->id }})"></span> --}}


                <button type="button" 
                        wire:click="decreaseQuantity('{{ $cart->id }}')"
                        x-bind:disabled="loading"
                        wire:loading.attr="disabled"
                        wire:target="decreaseQuantity,increaseQuantity"
                        class="px-3 py-1 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                    <span x-show="!loading">-</span>
                </button>

                <span class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ number_format($cart->quantity,0) }}
                </span>

                <button type="button" 
                        wire:click="increaseQuantity('{{ $cart->id }}')"
                        x-bind:disabled="loading"
                        wire:loading.attr="disabled"
                        wire:target="increaseQuantity,decreaseQuantity"
                        class="px-3 py-1 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                    <span x-show="!loading">+</span>
                </button>
            </div>
          </div>
          @empty
          <div class="text-center py-10 text-gray-500 dark:text-neutral-400 text-lg">
              Cart is Empty.
          </div>
          @endforelse
      </div>

    @if(count($carts) > 0)
        <div class="sticky bottom-8 left-0 bg-white dark:bg-neutral-800 p-4 border-t dark:border-neutral-700 z-10">
            <div class="mt-4 flex gap-3">
                <button wire:click="removeSelected" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Remove Item
                </button>
                <x-button wire:click="checkout" class="px-3 py-1.5 text-sm">
                    Checkout
                </x-button>
            </div>
        </div>
    @endif
  </div>


</div>
