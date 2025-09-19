<div>
    {{-- Buy Now Mode Indicator --}}
    @if ($isBuyNowMode)
        <div
            class="ml-3 p-3 bg-orange-50 border border-orange-200 rounded-lg mb-4 dark:bg-orange-900/20 dark:border-orange-800">
            <h3 class="text-sm font-semibold text-orange-800 dark:text-orange-200">Buy Now Mode</h3>
            <div class="mt-1 text-sm text-orange-700 dark:text-orange-300">
                <p>You're checking out a single item. Other cart items will not be included in this order.</p>
            </div>
        </div>
    @endif

    {{-- Back to Shop Button --}}
    <div class="mb-4 ml-3 mt-4">
        <a wire:navigate href="{{ route('page.shop') }}"
            class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
            Back to Shop
        </a>
    </div>

    <div class="p-4 md:p-8 mx-auto my-6 md:my-12 max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            {{-- Product Summary --}}
            <div class="order-2 lg:order-1">
                <h2 class="mb-4 text-xl md:text-2xl font-bold text-gray-800 dark:text-neutral-200">
                    {{ __('Product Summary') }}</h2>
                <div
                    class="bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70 overflow-hidden">
                    <div class="p-4 md:p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead class="bg-gray-50 dark:bg-neutral-800">
                                    <tr>
                                        <th scope="col"
                                            class="px-3 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-neutral-400 uppercase">
                                            Product</th>
                                        <th scope="col"
                                            class="px-3 py-3 text-right text-xs md:text-sm font-medium text-gray-500 dark:text-neutral-400 uppercase">
                                            Price</th>
                                        <th scope="col"
                                            class="px-3 py-3 text-right text-xs md:text-sm font-medium text-gray-500 dark:text-neutral-400 uppercase">
                                            Qty</th>
                                        <th scope="col"
                                            class="px-3 py-3 text-right text-xs md:text-sm font-medium text-gray-500 dark:text-neutral-400 uppercase">
                                            Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                    @foreach ($checkoutItems as $item)
                                        <tr wire:key="item-{{ $item->id }}">
                                            <td class="px-3 py-4 text-sm text-gray-800 dark:text-neutral-200">
                                                <div class="flex items-center">
                                                    <div wire:ignore
                                                        class="w-12 h-12 md:w-16 md:h-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 dark:border-neutral-700">
                                                        @if (isset($item->variant))
                                                            <img src="{{ Storage::url($item->variant->url) }}"
                                                                alt="{{ $item->product->prod_name }}"
                                                                class="w-full h-full object-cover">
                                                        @else
                                                            @if ($item->product->primary_image && $item->product->primary_image->url)
                                                                <img src="{{ Storage::url($item->product->primary_image->url) }}"
                                                                    alt="{{ $item->product->prod_name }}"
                                                                    class="w-full h-full object-cover">
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="ml-3" wire:ignore>
                                                        <h3
                                                            class="text-sm md:text-base font-medium text-gray-900 dark:text-neutral-200 line-clamp-2">
                                                            {{ $item->product->prod_name }}
                                                            @if (isset($item->variant))
                                                                -   {{ ucwords(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $item->variant->sizes)) }}
                                                            @endif
                                                            @if ($item->product->prod_unit === 'kg' || $item->product->prod_unit === 'g')
                                                                - {{ $item->product->prod_weight }}
                                                                {{ $item->product->prod_unit }}
                                                            @endif
                                                        </h3>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-800 dark:text-neutral-200 text-right whitespace-nowrap">
                                                @if ($item->product->discounted_price !== null && $item->product->prod_unit !== 'diff_size')
                                                    <div class="flex flex-col items-end">
                                                        <del class="text-xs text-gray-500 dark:text-neutral-400">
                                                            ₱{{ number_format($item->product->prod_price, 2) }}
                                                        </del>
                                                        <span class="text-green-600 dark:text-green-400 text-sm">
                                                            ₱{{ number_format($item->product->discounted_price, 2) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div wire:ignore class="text-sm">
                                                        @if (isset($item->variant))
                                                            ₱{{ number_format($item->variant->price, 2) }}
                                                        @else
                                                            ₱{{ number_format($item->product->prod_price, 2) }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-800 dark:text-neutral-200 text-right">
                                                <div class="flex items-center justify-end space-x-1 md:space-x-2">
                                                    <button type="button"
                                                        wire:click="decreaseQuantity({{ $item->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="px-2 py-1 text-xs md:text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">-</button>
                                                    <p class="text-center w-4 md:w-6 text-sm">
                                                        {{ number_format($item->quantity, 0) }}</p>
                                                    <button type="button"
                                                        wire:click="increaseQuantity({{ $item->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="px-2 py-1 text-xs md:text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">+</button>
                                                </div>
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-800 dark:text-neutral-200 text-right whitespace-nowrap">
                                                @if ($item->product->discounted_price)
                                                    <span class="text-green-600 dark:text-green-400 text-sm">
                                                        ₱{{ number_format($item->product->discounted_price * $item->quantity, 2) }}
                                                    </span>
                                                @else
                                                    <div class="text-sm">
                                                        @if (isset($item->variant))
                                                            ₱{{ number_format($item->variant->price * $item->quantity, 2) }}
                                                        @else
                                                            ₱{{ number_format($item->product->prod_price * $item->quantity, 2) }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Checkout Form --}}
            <div class="order-1 lg:order-2">
                <div class="sticky top-4">
                    <h3 class="text-lg md:text-xl font-semibold text-gray-700 dark:text-neutral-400 mb-4">Personal
                        Information</h3>
                    <form wire:key="checkout-form-{{ auth()->id() }}" wire:submit="placeOrder" class="space-y-4">
                        <div class="space-y-3">
                            <label for="input-username" class="sr-only">Username</label>
                            <input type="text" id="input-username" value="{{ auth()->user()->name }}"
                                class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500"
                                placeholder="Username" readonly>

                            <label for="input-email" class="sr-only">Email</label>
                            <input type="email" id="input-email" value="{{ auth()->user()->email }}"
                                class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500"
                                placeholder="you@site.com" readonly>
                        </div>

                        <div>
                            <h3 class="text-lg md:text-xl font-semibold text-gray-700 dark:text-neutral-400 mb-3">
                                Shipping Address</h3>
                            <div class="space-y-3">
                                <div class="relative" x-data="{ open: false }">
                                    <div @click="open = !open"
                                        class="border rounded-lg px-4 py-3 cursor-pointer bg-white dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 flex justify-between items-center">
                                        <span>{{ $selectedCity ? $cities->firstWhere('code', $selectedCity)?->name : 'Select or Search City' }}</span>
                                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>

                                    <div x-show="open" @click.outside="open = false" x-transition x-cloak
                                        class="absolute z-10 mt-1 w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg max-h-60 overflow-hidden">
                                        <div class="relative p-2 border-b border-gray-200 dark:border-neutral-700">
                                            <input type="text" wire:model.live="searchCity"
                                                placeholder="Search City"
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
                                                <li class="px-4 py-2 text-gray-500 dark:text-neutral-400 text-center">
                                                    No City found</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>

                                @if ($selectedCity)
                                    <div class="relative" x-data="{ open: false }">
                                        <div @click="open = !open"
                                            class="border rounded-lg px-4 py-3 cursor-pointer bg-white dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 flex justify-between items-center">
                                            <span>{{ $selectedBarangay ? $barangays->firstWhere('name', $selectedBarangay)?->name : 'Select or Search Barangay' }}</span>
                                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>

                                        <div x-show="open" @click.outside="open = false" x-transition x-cloak
                                            class="absolute z-10 mt-1 w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg max-h-60 overflow-hidden">
                                            <div class="relative p-2 border-b border-gray-200 dark:border-neutral-700">
                                                <input type="text" wire:model.live="searchBrgy"
                                                    placeholder="Search Barangay"
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
                                                    <li
                                                        class="px-4 py-2 text-gray-500 dark:text-neutral-400 text-center">
                                                        No Barangay Found</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex flex-col items-start pt-2 gap-2">
                                    <div class="flex items-start">
                                        <input type="checkbox" id="same_as_billing" name="same_as_billing"
                                            wire:model.live="same_as_billing"
                                            class="shrink-0 mt-1 me-2 border-gray-200 rounded-sm text-blue-600 dark:bg-neutral-800 dark:border-neutral-700">
                                        <label for="same_as_billing"
                                            class="text-sm text-gray-700 dark:text-neutral-400 cursor-pointer">Is
                                            Billing
                                            Address same as Shipping Address?</label>
                                    </div>

                                    <div class="flex items-start">
                                        <input type="checkbox" id="is_notes" name="is_notes"
                                            wire:model.live="is_notes"
                                            class="shrink-0 mt-1 me-2 border-gray-200 rounded-sm text-blue-600 dark:bg-neutral-800 dark:border-neutral-700">
                                        <label for="is_notes"
                                            class="text-sm text-gray-700 dark:text-neutral-400 cursor-pointer">Wants to
                                            write notes?
                                        </label>
                                    </div>
                                </div>


                                @if ($is_notes)
                                    {{-- <div class="max-w-sm space-y-3"> --}}
                                    <textarea
                                        wire:model="notes"
                                        class="py-2 px-3 sm:py-3 sm:px-4 block w-full bg-gray-100 border-transparent rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                        rows="3" placeholder="Type your notes here..."></textarea>
                                    {{-- </div> --}}
                                @endif

                                @if ($same_as_billing == false)
                                    {{-- billing address city --}}
                                    <div class="relative" x-data="{ open: false }">
                                        <div @click="open = !open"
                                            class="border rounded-lg px-4 py-3 cursor-pointer bg-white dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 flex justify-between items-center">
                                            <span>{{ $billing_city ? $bil_cities->firstWhere('code', $billing_city)?->name : 'Select or Search City' }}</span>
                                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>

                                        <div x-show="open" @click.outside="open = false" x-transition x-cloak
                                            class="absolute z-10 mt-1 w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg max-h-60 overflow-hidden">
                                            <div class="relative p-2 border-b border-gray-200 dark:border-neutral-700">
                                                <input type="text" wire:model.live="billingSearchCity"
                                                    placeholder="Search City"
                                                    class="w-full px-3 py-2 border rounded-md focus:outline-none bg-white dark:bg-neutral-900 text-gray-700 dark:text-neutral-200">
                                                <button type="button"
                                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-neutral-400 dark:hover:text-neutral-200"
                                                    @click="$wire.set('billingSearchCity', '')">&times;</button>
                                            </div>

                                            <ul class="max-h-48 overflow-y-auto py-1">
                                                @forelse ($bil_cities as $city)
                                                    <li class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-800 cursor-pointer text-gray-700 dark:text-neutral-200"
                                                        wire:click="bilSelectCity('{{ $city->code }}'); open=false">
                                                        {{ $city->name }}
                                                    </li>
                                                @empty
                                                    <li
                                                        class="px-4 py-2 text-gray-500 dark:text-neutral-400 text-center">
                                                        No City found</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>

                                    @if ($billing_city)
                                        {{-- barangay billing address --}}
                                        <div class="relative" x-data="{ open: false }">
                                            <div @click="open = !open"
                                                class="border rounded-lg px-4 py-3 cursor-pointer bg-white dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 flex justify-between items-center">
                                                <span>{{ $billing_brgy ? $bil_barangays->firstWhere('name', $billing_brgy)?->name : 'Select or Search Barangay' }}</span>
                                                <svg class="w-4 h-4 transition-transform"
                                                    :class="{ 'rotate-180': open }" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>

                                            <div x-show="open" @click.outside="open = false" x-transition x-cloak
                                                class="absolute z-10 mt-1 w-full bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg max-h-60 overflow-hidden">
                                                <div
                                                    class="relative p-2 border-b border-gray-200 dark:border-neutral-700">
                                                    <input type="text" wire:model.live="billingSearchBrgy"
                                                        placeholder="Search Barangay"
                                                        class="w-full px-3 py-2 border rounded-md focus:outline-none bg-white dark:bg-neutral-900 text-gray-700 dark:text-neutral-200">
                                                    <button type="button"
                                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-neutral-400 dark:hover:text-neutral-200"
                                                        @click="$wire.set('billingSearchBrgy', '')">&times;</button>
                                                </div>

                                                <ul class="max-h-48 overflow-y-auto py-1">
                                                    @forelse ($bil_barangays as $brgy)
                                                        <li class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-800 cursor-pointer text-gray-700 dark:text-neutral-200"
                                                            wire:click="bilSelectBrgy('{{ $brgy->name }}'); open=false">
                                                            {{ $brgy->name }}
                                                        </li>
                                                    @empty
                                                        <li
                                                            class="px-4 py-2 text-gray-500 dark:text-neutral-400 text-center">
                                                            No Barangay Found</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Payment Method Section --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-neutral-400 mb-3">Payment Method
                            </h3>
                            <div x-data="{ paymentMethod: @entangle('payment_method') }">
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="radio" id="payment-cod" name="shipping_method"
                                        wire:model="shipping_method" value="COD" class="hidden peer/cod"
                                        @change="paymentMethod = 'cod'" :checked="paymentMethod === 'cod'">
                                    <label for="payment-cod"
                                        class="flex items-center justify-center p-3 w-full bg-white border border-gray-200 rounded-lg text-xs sm:text-sm cursor-pointer transition-colors duration-200 peer-checked/cod:border-amber-500 peer-checked/cod:text-amber-600 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:peer-checked/cod:text-amber-400">
                                        Cash on Delivery
                                    </label>

                                    <input type="radio" id="payment-ewallet" name="shipping_method"
                                        wire:model="shipping_method" value="e-wallet" class="hidden peer/ewallet"
                                        @change="paymentMethod = 'ewallet'" :checked="paymentMethod === 'ewallet'">
                                    <label for="payment-ewallet"
                                        class="flex items-center justify-center p-3 w-full bg-white border border-gray-200 rounded-lg text-xs sm:text-sm cursor-pointer transition-colors duration-200 peer-checked/ewallet:border-amber-500 peer-checked/ewallet:text-amber-600 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:peer-checked/ewallet:text-amber-400">
                                        E-Wallets
                                    </label>
                                </div>

                                <div class="mt-4 space-y-3" x-show="paymentMethod === 'ewallet'" x-cloak>
                                    <div class="grid grid-cols-2 gap-3">
                                        @foreach ([['id' => 'payment-gcash', 'value' => 'gcash', 'img' => 'imgs/gcash.png', 'label' => 'GCash'], ['id' => 'payment-card', 'value' => 'card', 'img' => 'imgs/card.png', 'label' => 'Visa/Mastercard'], ['id' => 'payment-paymaya', 'value' => 'paymaya', 'img' => 'imgs/paymaya.png', 'label' => 'Paymaya'], ['id' => 'payment-grabpay', 'value' => 'grab_pay', 'img' => 'imgs/grabpay.png', 'label' => 'GrabPay']] as $payment)
                                            <div
                                                class="relative flex items-center p-2 border rounded-lg bg-white dark:bg-neutral-800 dark:border-neutral-700">
                                                <input id="{{ $payment['id'] }}" name="payment_method"
                                                    type="radio" value="{{ $payment['value'] }}"
                                                    wire:model.live="shipping_method"
                                                    class="border-gray-200 rounded-full text-amber-600"
                                                    :disabled="paymentMethod !== 'ewallet'">
                                                <label for="{{ $payment['id'] }}"
                                                    class="ml-2 flex items-center cursor-pointer">
                                                    <img src="{{ asset($payment['img']) }}" class="w-6 h-6 mr-1">
                                                    <span
                                                        class="text-xs text-gray-700 dark:text-neutral-300">{{ $payment['label'] }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Details --}}
                        @if ($shipping_method === 'card')
                            <div
                                class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                <h3 class="font-medium text-sm text-gray-800 mb-3 dark:text-neutral-200">Card Details
                                </h3>

                                <div class="space-y-3">
                                    <input type="text" wire:model.blur="card_name" id="card_name"
                                        class="block w-full px-3 py-2.5 text-sm border-gray-200 rounded-lg focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                        placeholder="Name on Card">
                                    @error('card_name')
                                        <span class="text-xs text-red-500">{{ $message }}</span>
                                    @enderror

                                    <input type="text" wire:model.blur="card_number" id="card_number"
                                        class="block w-full px-3 py-2.5 text-sm border-gray-200 rounded-lg focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                        placeholder="1234 5678 9012 3456">
                                    @error('card_number')
                                        <span class="text-xs text-red-500">{{ $message }}</span>
                                    @enderror

                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <span class="block mb-1 text-xs text-gray-600 dark:text-neutral-500">Exp
                                                Month</span>
                                            <select wire:model.blur="expiration_month" id="expiration_month"
                                                class="block w-full px-2 py-2 text-xs border-gray-200 rounded-lg focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                                <option value="">Month</option>
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}">
                                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                                @endfor
                                            </select>
                                            @error('expiration_month')
                                                <span class="text-xs text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <span
                                                class="block mb-1 text-xs text-gray-600 dark:text-neutral-500">Year</span>
                                            <select wire:model.blur="expiration_year" id="expiration_year"
                                                class="block w-full px-2 py-2 text-xs border-gray-200 rounded-lg focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                                <option value="">Year</option>
                                                @for ($i = date('Y'); $i <= date('Y') + 10; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            @error('expiration_year')
                                                <span class="text-xs text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <span
                                                class="block mb-1 text-xs text-gray-600 dark:text-neutral-500">CVV</span>
                                            <input type="text" wire:model.blur="cvv" id="cvv"
                                                class="block w-full px-2 py-2 text-xs border-gray-200 rounded-lg focus:border-amber-500 focus:ring-amber-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                                placeholder="123">
                                            @error('cvv')
                                                <span class="text-xs text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Payment Summary --}}
                        <div
                            class="bg-white dark:bg-neutral-900 p-4 rounded-lg border border-gray-200 dark:border-neutral-700">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-neutral-400 mb-3">Payment Summary
                            </h3>

                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-neutral-400">Product Subtotal:</span>
                                    <span
                                        class="text-sm text-gray-800 dark:text-neutral-200">₱{{ number_format($itemsTotal, 2) }}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-neutral-400">Shipping:</span>
                                    <span
                                        class="text-sm {{ $totalShipping > 0 ? 'text-gray-800 dark:text-neutral-200' : 'text-green-500' }}">
                                        {{ $totalShipping > 0 ? '₱' . number_format($totalShipping, 2) : 'Free Shipping' }}
                                    </span>
                                </div>

                                <div class="border-t border-gray-200 dark:border-neutral-700 pt-2 mt-2">
                                    <div class="flex justify-between">
                                        <span class="font-semibold text-gray-800 dark:text-neutral-200">Total:</span>
                                        <span
                                            class="font-semibold text-gray-800 dark:text-neutral-200">₱{{ number_format($subtotal, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Error Messages --}}
                        @if ($errors->any())
                            <div
                                class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg dark:bg-red-900/20 dark:border-red-800 dark:text-red-400">
                                <ul class="list-disc list-inside text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        {{-- @foreach ($cities as $city)
                        {{$city->name}} -- {{$city->code}}
                            
                        @endforeach --}}

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-4">
                            <button type="button" wire:click.prevent="cancelOrder"
                                class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 transition-colors">Cancel
                                Order</button>
                            <x-button type="submit" wire:target="placeOrder" class="flex-1 justify-center"
                                wire:loading.attr="disabled">
                                <span wire:loading.flex wire:target="placeOrder" class="items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z">
                                        </path>
                                    </svg>
                                </span>
                                Place Order
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    [x-cloak] {
        display: none !important;
    }
</style> --}}
