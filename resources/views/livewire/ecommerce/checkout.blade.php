<div>   {{-- open. --}}
    {{-- Do your work, then step back. --}}
<div class="p-8 mx-auto my-12 max-w-7xl">
    <div class="grid grid-cols-2 mx-auto gap-x-8">
    <div>
        <h2 class="mb-6 text-2xl font-bold text-gray-800 dark:text-neutral-200">{{ __('Product Summary') }}</h2>
        <div class="flex flex-col mb-6 bg-white border shadow-sm lg:mb-10 rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="p-2 md:p-6">
                <div class="flex flex-col">
                    <div class="mb-8">
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full align-middle sm:px-6 lg:px-8">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                    <thead class="bg-gray-50 dark:bg-neutral-800">
                                        <tr>
                                            <th scope="col" class="px-3 py-3 text-left text-sm font-medium text-gray-500 dark:text-neutral-400 uppercase">Product</th>
                                            <th scope="col" class="px-3 py-3 text-right text-sm font-medium text-gray-500 dark:text-neutral-400 uppercase">Price</th>
                                            <th scope="col" class="px-3 py-3 text-right text-sm font-medium text-gray-500 dark:text-neutral-400 uppercase">Quantity</th>
                                            <th scope="col" class="px-3 py-3 text-right text-sm font-medium text-gray-500 dark:text-neutral-400 uppercase">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                        @foreach($checkoutItems as $item)
                                        <tr  wire:key="item-{{ $item->id }}">
                                            <td class="px-3 py-4 text-sm text-gray-800 dark:text-neutral-200">
                                                <div class="flex items-center">
                                                    <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 dark:border-neutral-700">
                                                        <img src="{{ Storage::url($item->product->images[0]->url) }}"
                                                            alt="{{ $item->product->prod_name }}"
                                                            class="w-full h-full object-cover">
                                                    </div>
                                                    <div class="ml-4">
                                                        <h3 class="font-medium text-gray-900 dark:text-neutral-200">{{ $item->product->prod_name }}</h3>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-800 dark:text-neutral-200 text-right">
                                                ₱{{ number_format($item->product->prod_price, 2) }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-800 dark:text-neutral-200 text-right">
                                                <div class="flex items-center justify-end space-x-2" >
                                                    <button type="button" wire:click="decreaseQuantity({{ $item->id }})" wire:loading.attr="disabled"
                                                        class="px-3 py-1 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                                                        -
                                                    </button>

                                                    <p class="text-center w-6">{{ number_format($item->quantity, 0) }}</p>

                                                    <button type="button" wire:click="increaseQuantity({{ $item->id }})" wire:loading.attr="disabled"
                                                        class="px-3 py-1 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                                                        +
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-800 dark:text-neutral-200 text-right">
                                                ₱{{ number_format($item->product->prod_price * $item->quantity, 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <a class="inline-flex items-center gap-x-1.5 text-sm text-gray-600 decoration-2 hover:underline dark:text-amber-400" href="#">
                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>

            </a> --}}
    </div>
        <div>
             <h3 class="text-xl mt-3 font-semibold text-gray-700 dark:text-neutral-400">Personal Information</h3>
            <form wire:submit.prevent="placeOrder">
                <div class="max-w-full space-y-4">
                    <label for="input-username" class="sr-only">Username</label>
                    <input type="text" id="input-username"  value="{{ auth()->user()->name }}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500" placeholder="Username" readonly>
                    {{-- himuon readonly ang email and username and display the login username and password --}}
                    <label for="input-email" class="sr-only">Email</label>
                    <input type="email" id="input-email" value="{{ auth()->user()->email }}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500" placeholder="you@site.com" readonly>
                </div>



                <div>
                    <h3 class="text-xl mt-3 font-semibold text-gray-700 dark:text-neutral-400">Shipping Address</h3>
                    <div class="space-y-3">
                        {{-- <label class="flex items-center text-gray-600 dark:text-neutral-400">
                            <input type="checkbox" wire:model="same_as_billing" class="mr-2">
                            Same as billing address
                        </label> --}}

                        <label for="shipping_street" class="sr-only">Street</label>
                        <input type="text" id="shipping_street" wire:model="shipping_street"
                            class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm
                                   focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900
                                   dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500"
                            placeholder="Street: Example: 123 Main Street" >

                        <label for="shipping_city" class="sr-only">City</label>
                        <input type="text" id="shipping_city" wire:model = "shipping_city"
                            class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm
                                   focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900
                                   dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500"
                            placeholder="City Example: Victorias City" >

                        <label for="shipping_state" class="sr-only">State</label>
                        <input type="text" id="shipping_state" wire:model="shipping_state"
                            class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm
                                   focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900
                                   dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500"
                            placeholder="State" >

                        <label for="shipping_zip" class="sr-only">ZIP Code</label>
                        <input type="text" id="shipping_zip"  wire:model="shipping_zip"
                            class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm
                                   focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900
                                   dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500"
                            placeholder="ZIP Code Example: 6119" >

                        <div class="flex  items-start">
                            <input type="checkbox" wire:model="same_as_billing"
                                class="shrink-0 mt-1 me-2 border-gray-200 rounded-sm text-blue-600 dark:bg-neutral-800 dark:border-neutral-700">
                            <span class="text-xs mt-1 text-black-500 dark:text-neutral-400">Is Billing Address same as Shipping Address?</span>
                        </div>
                    </div>
                </div>

                <div>
                     <h3 class="text-lg mt-2 mb-2 font-semibold text-gray-700 dark:text-neutral-400">Payment Method</h3>
                    <div x-data="{ paymentMethod: @entangle('payment_method').defer }">
                        <!-- Payment Method Selection -->
                        <div class="grid sm:grid-cols-2 gap-2">
                            <!-- Cash on Delivery -->
                            <label class="flex p-3 w-full bg-white border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                <input type="radio" wire:model="shipping_method"
                                        value="COD"
                                       class="shrink-0 mt-0.5 border-gray-200 rounded-sm text-blue-600 dark:bg-neutral-800 dark:border-neutral-700"
                                       @change="paymentMethod = 'cod'"
                                       :checked="payment_method === 'cod'">
                                <span class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Cash on Delivery</span>
                            </label>

                            <label class="flex p-3 w-full bg-white border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                    <input type="radio" wire:model="shipping_method"
                                        value="e-wallet"
                                        class="shrink-0 mt-0.5 border-gray-200 rounded-sm text-blue-600 dark:bg-neutral-800 dark:border-neutral-700"
                                        @change="paymentMethod = 'ewallet'"
                                        :checked="paymentMethod === 'ewallet'">
                                    <span class="text-sm text-gray-500 ms-3 dark:text-neutral-400">E-Wallets</span>
                                </label>
                            </div>

                            <div class="mt-5 space-y-3" x-show="paymentMethod === 'ewallet'" x-cloak>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-5 mt-1">
                                            <input
                                                id="payment-gcash"
                                                name="payment_method"
                                                type="radio"
                                                value="gcash"
                                                wire:model.live="shipping_method"
                                                class="border-gray-200 rounded-full text-amber-600"
                                                :disabled="paymentMethod !== 'ewallet'"  >
                                        </div>
                                        <label for="payment-gcash" class="ms-3">
                                            <img src="{{asset('imgs/gcash.png')}}" class="w-auto h-8 mb-2">
                                        </label>
                                        <span class="text-sm items-center ml-2 mt-1 text-black-500 dark:text-neutral-400">GCash</span>
                                    </div>

                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-5 mt-1">
                                            <input
                                                id="payment-card"
                                                name="payment_method"
                                                type="radio"
                                                value="card"
                                                wire:model.live="shipping_method"
                                                class="border-gray-200 rounded-full text-amber-600"
                                                :disabled="paymentMethod !== 'ewallet'"  >
                                        </div>
                                        <label for="payment-card" class="ms-3">
                                            <img src="{{asset('imgs/card.png')}}" class="w-auto h-8 mb-2">
                                        </label>
                                        <span class="text-sm items-center ml-2 mt-1 text-black-500 dark:text-neutral-400">Visa/Mastercard</span>
                                    </div>

                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-5 mt-1">
                                            <input
                                                id="payment-paymaya"
                                                name="payment_method"
                                                type="radio"
                                                value="paymaya"
                                                wire:model.live="shipping_method"
                                                class="border-gray-200 rounded-full text-amber-600"
                                                :disabled="paymentMethod !== 'ewallet'"  >
                                        </div>
                                        <label for="payment-paymaya" class="ms-3">
                                            <img src="{{asset('imgs/paymaya.png')}}" class="w-auto h-8 mb-2">
                                        </label>
                                        <span class="text-sm items-center ml-2 mt-1 text-black-500 dark:text-neutral-400">Paymaya</span>
                                    </div>

                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-5 mt-1">
                                            <input
                                                id="payment-grabpay"
                                                name="payment_method"
                                                type="radio"
                                                value="grab_pay"
                                                wire:model.live="shipping_method"
                                                class="border-gray-200 rounded-full text-amber-600"
                                                :disabled="paymentMethod !== 'ewallet'"  >
                                        </div>
                                        <label for="payment-grabpay" class="ms-3">
                                            <img src="{{asset('imgs/grabpay.png')}}" class="w-auto h-8 mb-2">
                                        </label>
                                        <span class="text-sm items-center ml-2 mt-1 text-black-500 dark:text-neutral-400">GrabPay</span>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($shipping_method === 'card')
                    <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm lg:mb-5 rounded-xl md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                        <h3 class="font-medium inline-block text-sm text-gray-800 mt-2.5 mb-3 dark:text-neutral-200">{{'Card Details'}}</h3>

                        <div class="mb-3">
                        <input type="text" wire:model.blur="card_name" id="card_name" class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg focus:border-amber-500 focus:ring-amber-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600 bg-slate-100" placeholder="Name on Card">
                        @error('card_name') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                        <input type="text" wire:model.blur="card_number" id="card_number" class="block w-full px-3 py-3 text-sm border-gray-200 rounded-lg shadow-sm pe-11 focus:border-amber-500 focus:ring-amber-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="1234 5678 9012 3456">
                        @error('card_number') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <span class="block mb-2 text-sm text-gray-600 dark:text-neutral-500">{{'Expiration Month'}}</span>
                                <select wire:model.blur="expiration_month"  id="expiration_month" class="block w-full px-3 py-2 text-sm border-gray-200 rounded-lg shadow-sm pe-9 focus:border-amber-500 focus:ring-amber-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <option value="">{{ __('Select Month') }}</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </select>
                                @error('expiration_month') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <span class="block mb-2 text-sm text-gray-600 dark:text-neutral-500"> {{'Year'}}</span>
                                <select wire:model.blur="expiration_year" id="expiration_year"
                                        class="block w-full px-3 py-2 text-sm border-gray-200 rounded-lg shadow-sm pe-9 focus:border-amber-500 focus:ring-amber-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <option value="">{{'Year'}}</option>
                                    @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('expiration_year') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <span class="block mb-2 text-sm text-gray-600 dark:text-neutral-500"> {{'CVV'}}</span>
                                <input type="text" wire:model.blur="cvv" id="cvv" class="block w-full px-3 py-2 text-sm border-gray-200 rounded-lg shadow-sm pe-11 focus:border-amber-500 focus:ring-amber-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                    placeholder="123">
                                @error('cvv') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <div class="gap-6 mt-6 mb-2">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-neutral-400 mb-3">Payment Summary</h3>

                    {{-- Product Subtotal --}}
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Product Subtotal:</span>
                        <span class="text-sm text-gray-700 dark:text-neutral-300">₱{{ number_format($itemsTotal, 2) }}</span>
                    </div>

                    {{-- Shipping --}}
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Shipping Subtotal:</span>
                        <span class="text-sm text-green-500">
                            {{ $totalShipping > 0 ? '₱' . number_format($totalShipping, 2) : 'Free Shipping' }}
                        </span>
                    </div>

                    {{-- Total --}}
                    <div class="flex items-center justify-between mt-4 pt-2 border-t border-gray-200 dark:border-neutral-700">
                        <span class="text-md font-semibold text-gray-800 dark:text-neutral-200">Total:</span>
                        <span class="text-md font-semibold text-gray-800 dark:text-neutral-200">₱{{ number_format($subtotal, 2) }}</span>
                    </div>
                </div>

                    @if ($errors->any())
                        <div class="text-red-600">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex justify-end mt-6 mb-2">
                        <x-button type="submit" wire:target="placeOrder" class="w-full sm:w-auto" wire:loading.attr="disabled">
                            <span wire:loading.flex wire:target="placeOrder" class="items-center">
                                <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </span>
                            Place Order
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>  {{-- close --}}