<div>   {{-- open. --}}
    {{-- Do your work, then step back. --}}
<div class="p-8 mx-auto my-12 max-w-7xl">
    <div class="grid grid-cols-2 mx-auto gap-x-8">
        <div>
             <h2 class="mb-6 text-2xl font-bold text-gray-800 dark:text-neutral-200">{{ __('Selected Items') }}</h2>
             <div class="flex flex-col mb-6 bg-white border shadow-sm lg:mb-10 rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70">
                 <div class="p-4 md:p-10">
                     <div class="flex flex-col">
                         <div class="-m-1.5 overflow-x-auto">
                           <div class="p-1.5 min-w-full inline-block align-middle">
                             <div class="overflow-hidden">
                               <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                 <thead>
                                   <tr>
                                     <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-start dark:text-neutral-500">Product Image</th>
                                     <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-start dark:text-neutral-500">Product Name</th>
                                     <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-start dark:text-neutral-500">Quantity</th>
                                     <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-end dark:text-neutral-500">Product Price</th>
                                   </tr>
                                 </thead>
                                 <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                     @forelse ($checkoutItems as $order)
                                     <tr>
                                         <td class="px-6 py-4 text-sm text-gray-800 whitespace-nowrap dark:text-neutral-200">
                                             <div class="flex-shrink-0 relative overflow-hidden h-[70px] sm:w-[70px] sm:h-[70px] w-full rounded-full">
                                                 <img class="absolute top-0 object-cover size-full start-0" src="{{ Storage::url($order->product->images[0]->url) }}" alt="{{ $order->product->prod_slug }}">
                                             </div>
                                         </td>
                                         <td class="px-6 py-4 text-sm font-medium text-gray-800 whitespace-nowrap dark:text-neutral-200">
                                             <div class="flex flex-col">
                                                 <h5 class="font-bold"></h5>
                                                 <span class="text-xs italic text-gray-500"> {{$order->product->prod_name}}</span>
                                             </div>
                                         </td>
                                         <td class="px-6 py-4 text-sm text-gray-800 whitespace-nowrap dark:text-neutral-200">{{$order->quantity}} {{ $order->product->prod_unit == 'kg' ? 'kg' : 'pcs' }}</td>
                                         <td class="px-6 py-4 text-sm text-gray-800 whitespace-nowrap dark:text-neutral-200">{{$order->product->prod_price}}</td>
                                     </tr>
                                     @empty
                                     <tr>
                                         <td colspan="4" class="px-6 py-4 text-sm text-gray-800 dark:text-neutral-200">
                                             Empty
                                         </td>
                                     </tr>
                                     @endforelse
 
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
            <form wire:submit.prevent="placeOrder">
                <div class="max-w-full space-y-4">
                    <label for="input-username" class="sr-only">Username</label>
                    <input type="text" id="input-username"  value="{{ auth()->user()->name }}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500" placeholder="Username" readonly>
                    {{-- himuon readonly ang email and username and display the login username and password --}}
                    <label for="input-email" class="sr-only">Email</label>
                    <input type="email" id="input-email" value="{{ auth()->user()->email }}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500" placeholder="you@site.com" readonly>
                </div>



                <div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-neutral-400">Shipping Address</h3>
                    <div class="space-y-3">
                        {{-- <label class="flex items-center text-gray-600 dark:text-neutral-400">
                            <input type="checkbox" wire:model="same_as_billing" class="mr-2">
                            Same as billing address
                        </label> --}}
      
                <select wire:model.live="selectedCity" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                    
                    <option value="">-- Select City --</option>
                    
                    @foreach($cities as $city)
                        <option wire:key="{{ 'city-' . $city->id }}" value="{{ $city->city_code }}">{{ $city->name }}</option>
                    @endforeach
                </select>

                {{-- Barangay Dropdown --}}
                @if ($selectedCity)
                <select wire:model="selectedBarangay" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                        <option value="">-- Select Barangay --</option>
                        @foreach($barangays as $barangay)
                            <option wire:key="{{ 'barangay-' . $barangay->id }}" value="{{ $barangay->barangay_code }}">{{ $barangay->name }}</option>
                        @endforeach
                    </select> 
                @endif
    

{{--                 
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
                            placeholder="ZIP Code Example: 6119" > --}}

                            
                            <label class="flex items-center p-3 w-full bg-white border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                <input type="checkbox" wire:model ="same_as_billing"
                                    class="shrink-0 mt-0.5 me-2 border-gray-200 rounded-sm text-blue-600 dark:bg-neutral-800 dark:border-neutral-700">
                                <span class="text-sm text-gray-500 dark:text-neutral-400">Is Billing Address same as Shipping Address?</span>
                            </label>
                            

                    </div>
                </div>
                

                    <label class="mb-1 text-gray-700 dark:text-neutral-400 mt-2">Payment Method:</label>
                    
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
                    
                            <!-- E-Wallets -->
                            <label class="flex p-3 w-full bg-white border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                <input type="radio" wire:model="shipping_method"
                                        value="e-wallet"
                                       class="shrink-0 mt-0.5 border-gray-200 rounded-sm text-blue-600 dark:bg-neutral-800 dark:border-neutral-700"
                                       @change="paymentMethod = 'ewallet'"
                                       :checked="paymentMethod === 'ewallet'">
                                <span class="text-sm text-gray-500 ms-3 dark:text-neutral-400">E-wallets</span>
                            </label>
                        </div>
                    
                        <!-- Online Payment Options (Show Only If 'E-wallets' is Selected) -->
                        <div class="mt-2 space-y-3" x-show="paymentMethod === 'ewallet'" x-cloak>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <!-- GCash -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5 mt-1">
                                        <input id="payment-gcash"
                                            name="payment_method"
                                            type="radio" value="gcash"
                                            wire:model.live="shipping_method"
                                            class="border-gray-200 rounded-full text-amber-600">
                                    </div>
                                    <label for="payment-gcash" class="ms-3">
                                        <img src="{{asset('imgs/gcash.png')}}" class="w-auto h-8 mb-2">
                                    </label>
                                </div>
                    
                                <!-- Card -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5 mt-1">
                                        <input id="payment-card" name="payment_method"
                                            type="radio" value="card"
                                            wire:model.live="shipping_method"
                                            class="border-gray-200 rounded-full text-amber-600">
                                    </div>
                                    <label for="payment-card" class="ms-3">
                                        <img src="{{asset('imgs/card.png')}}" class="w-auto h-8 mb-2">
                                    </label>
                                </div>
                    
                                <!-- PayMaya -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5 mt-1">
                                        <input id="payment-paymaya" name="payment_method"
                                            type="radio" value="paymaya"   
                                            wire:model.live="shipping_method"   
                                            class="border-gray-200 rounded-full text-amber-600">
                                    </div>
                                    <label for="payment-paymaya" class="ms-3">
                                        <img src="{{asset('imgs/paymaya.png')}}" class="w-auto h-8 mb-2">
                                    </label>
                                </div>
                    
                                <!-- GrabPay -->
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5 mt-1">
                                        <input id="payment-grabpay"
                                           name="payment_method"
                                           type="radio"
                                           value="grab_pay" 
                                           wire:model.live="shipping_method"  
                                           class="border-gray-200 rounded-full text-amber-600">
                                    </div>
                                    <label for="payment-grabpay" class="ms-3">
                                        <img src="{{asset('imgs/grabpay.png')}}" class="w-auto h-8 mb-2">
                                    </label>
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

                    <div class="mt-4">
                        <label for="order_notes" class="block text-sm font-medium text-gray-700 dark:text-neutral-400 mb-1">
                            Notes (optional)
                        </label>
                        <textarea id="order_notes" name="order_notes"
                            rows="4" wire:model="notes"
                            class="py-2.5 sm:py-3 px-4 block w-full border border-gray-200 dark:border-neutral-700 rounded-lg sm:text-sm 
                                   focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 
                                   text-gray-700 dark:text-neutral-300 dark:placeholder-neutral-500"
                            placeholder="e.g., Please call me before delivery, or Leave at front desk..."></textarea>
                    </div>
                   
                    <div class="flex gap-6 mt-6 mb-2">
                        {{-- Shipping --}}
                        <div class="flex items-center w-1/2 relative">
                            <label for="shipping" class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">Shipping:</label>
                            <input
                                type="text"
                                name="shipping_price"
                                id="shipping"
                                value="{{ $totalShipping > 0 ? number_format($totalShipping, 2) : null }}"
                                readonly
                                class="w-2/3 py-2.5 px-4 border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-900 text-gray-700 dark:text-neutral-300 rounded-lg text-sm text-right"
                               
                            >
                            <!-- Display "Enjoy Free Shipping" when the value is 0 -->
                            <span class="absolute inset-0 flex items-center justify-end pr-4 text-green-500" 
                                style="visibility: {{ $totalShipping > 0 ? 'hidden' : 'visible' }};">
                                Enjoy Free Shipping
                            </span>
                        </div>
                        
                    
                        {{-- Subtotal --}}
                        <div class="flex items-center w-1/2">
                            <label for="subtotal" class="w-1/3 text-sm font-medium text-gray-700 dark:text-gray-300">Subtotal:</label>
                            <input
                                type="text"
                                id="subtotal"
                                value="{{ number_format($subtotal, 2) }}"
                                readonly
                                class="w-2/3 py-2.5 px-4 border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-900 text-gray-700 dark:text-neutral-300 rounded-lg text-sm text-right"
                                placeholder="Subtotal"
                            >
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
                                        
                    <x-button type="submit" wire:target="placeOrder" class="mt-2"  wire:loading.attr="disabled">
                        <span wire:loading.flex wire:target="placeOrder"  class="items-center">
                            <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                           
                        </span>
                        Place Order
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>  {{-- close --}}
