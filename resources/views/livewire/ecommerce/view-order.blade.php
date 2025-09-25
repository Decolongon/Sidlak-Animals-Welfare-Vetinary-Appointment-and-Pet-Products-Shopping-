<div>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Your Orders</h1>
            {{-- <p class="text-gray-600 dark:text-gray-400 mt-1">Review your purchase history</p> --}}
        </div>

        <!-- Status Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-neutral-700">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                <!-- All Tab -->
                <button wire:click="$set('statusFilter', 'all')"
                    class="@if ($statusFilter === 'all') border-blue-500 text-blue-600 dark:text-blue-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm min-w-max">
                    All
                </button>

                <!-- Pending Tab -->
                <button wire:click="$set('statusFilter', 'pending')"
                    class="@if ($statusFilter === 'pending') border-blue-500 text-blue-600 dark:text-blue-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm min-w-max">
                    Pending
                </button>

                <!-- Processing Tab -->
                <button wire:click="$set('statusFilter', 'processing')"
                    class="@if ($statusFilter === 'processing') border-amber-500 text-amber-600 dark:text-amber-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm min-w-max">
                    Processing
                </button>

                <!-- Shipped Tab -->
                <button wire:click="$set('statusFilter', 'shipped')"
                    class="@if ($statusFilter === 'shipped') border-green-500 text-green-600 dark:text-green-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm min-w-max">
                    Shipped
                </button>

                <!-- Delivered Tab -->
                <button wire:click="$set('statusFilter', 'delivered')"
                    class="@if ($statusFilter === 'delivered') border-green-500 text-green-600 dark:text-green-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm min-w-max">
                    Delivered
                </button>

                <!-- Cancelled Tab -->
                <button wire:click="$set('statusFilter', 'cancelled')"
                    class="@if ($statusFilter === 'cancelled') border-red-500 text-red-600 dark:text-red-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm min-w-max">
                    Cancelled
                </button>
            </nav>
        </div>

        <!-- Order Cards -->
        @forelse ($orders as $order)
            <div wire:key="order-{{ $order->order_num }}"
                class="mb-6 bg-white dark:bg-neutral-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-neutral-700 transition-all hover:shadow-md">
                <!-- Order Header -->
                <div
                    class="px-5 py-4 border-b border-gray-200 dark:border-neutral-700 flex justify-between items-center">
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Tracking #:
                            {{ $order->order_num }}</span>
                        <h3 class="text-lg font-medium text-gray-800 dark:text-white mt-1">
                            {{ $order->created_at->format('M j, Y') }}
                        </h3>
                      @if ($orderId == $order->id)
    <div x-data="{ counter: 10, cancelled: false, interval: null }" x-init="interval = setInterval(() => {
        if (counter > 0) {
            counter--;
        } else {
            if (!cancelled) {
                $wire.processCancelOrder({{ $order->id }});
            }
            clearInterval(interval);
        }
    }, 1000);"
    class="p-3 rounded-lg border border-amber-500/30 bg-amber-50 dark:bg-amber-950/20 shadow-sm">

        <template x-if="!cancelled">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12zM10 5a1 1 0 011 1v5a1 1 0 11-2 0V6a1 1 0 011-1zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-amber-800 dark:text-amber-200 font-medium text-sm">
                        Cancelling in <span x-text="counter" class="font-bold"></span>s
                    </p>
                </div>

                <button
                    @click="
                        cancelled = true; 
                        clearInterval(interval);
                        $wire.resetOrder();
                    "
                    class="px-2.5 py-1 text-xs font-medium text-white bg-gray-600 hover:bg-gray-700 rounded transition-colors duration-200 flex items-center gap-1 justify-center sm:justify-start">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </button>
            </div>
        </template>

        <template x-if="cancelled">
            <div class="flex items-center gap-1.5 text-green-700 dark:text-green-400 text-sm">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="font-medium">Cancellation stopped</p>
            </div>
        </template>
    </div>
@endif


                    </div>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
        @if ($order->order_status == 'pending') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
        @elseif($order->order_status == 'processing') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
        @elseif($order->order_status == 'shipped') bg-green-100 text-green-800 dark:bg-purple-900/30 dark:text-green-400
        @elseif($order->order_status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
        @elseif($order->order_status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">
                        {{ ucfirst($order->order_status) }}

                    </span>
                </div>

                <!-- Order Items -->
                <div class="divide-y divide-gray-200 dark:divide-neutral-700" x-data="{ showAll: false }">
                    @foreach ($order->orderItems as $index => $item)
                        <div class="px-5 py-3 flex justify-between items-center" wire:key="{{ $item->id }}"
                            x-show="showAll || {{ $index }} < 3" x-transition:enter.duration.500ms
                            x-transition:leave.duration.400ms>
                            <div class="flex items-center space-x-3">
                                <div
                                    class="flex-shrink-0 bg-gray-100 dark:bg-neutral-700 rounded-md overflow-hidden w-10 h-10 flex items-center justify-center">
                                    @if (isset($item->variant))
                                        <img src="{{ asset(Storage::url($item->variant->url)) }}"
                                            alt="{{ $item->product->prod_name }}" class="object-cover w-full h-full">
                                    @else
                                        @if ($item->product->primary_image && $item->product->primary_image->url)
                                            <img src="{{ asset(Storage::url($item->product->primary_image->url)) }}"
                                                alt="{{ $item->product->prod_name }}"
                                                class="object-cover w-full h-full">
                                        @endif
                                    @endif

                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-800 dark:text-white">
                                        @if (isset($item->variant))
                                            {{ $item->product->prod_name }} -
                                            {{ ucwords(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $item->variant->sizes)) }}
                                        @else
                                            {{ $item->product->prod_name }}
                                        @endif

                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        @if ($item->product->prod_unit === 'pcs')
                                            {{ number_format($item->quantity, 0) }} pcs
                                        @elseif($item->product->prod_unit == 'diff_size' && isset($item->variant))
                                            {{ number_format($item->quantity, 0) }} pcs
                                        @else
                                            {{ $item->quantity }} ×
                                            {{ number_format($item->product->prod_weight, 2) }}
                                            {{ $item->product->prod_unit }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-800 dark:text-white">
                                {{-- @if ($item->product->discounted_price !== null)
                                    {{ number_format($item->product->discounted_price,2) }}
                                @else
                                    @if (isset($item->variant))
                                        Price:₱ {{ number_format($item->variant->price,2) }}
                                    @else
                                        Price:₱ {{ number_format($item->product->prod_price,2) }}
                                    @endif
                                @endif --}}
                                Price:₱ {{ number_format($item->price, 2) }}

                                <!-- Shipping Cost -->
                                {{-- @foreach ($order->orderItems as $item) --}}
                                {{-- @if ($item->product->prod_requires_shipping === true)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Shipping:</span>
                                        <span
                                            class="text-sm font-medium dark:text-white">₱{{ number_format($item->product->shipping_price, 2) }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-green-500 dark:text-green-400">Free
                                            Shipping</span>
                                    </div>
                                @endif --}}
                                {{-- @endforeach --}}
                            </span>
                        </div>
                    @endforeach

                    @if (count($order->orderItems) > 3)
                        <div class="px-5 py-3 text-center">
                            <button @click="showAll = !showAll"
                                class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                <span x-text="showAll ? 'Show Less' : 'Show All'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-1 transition-transform"
                                    :class="{ 'rotate-180': showAll }" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Order Footer -->
                <div
                    class="px-5 py-3 bg-gray-50 dark:bg-neutral-700/30 border-t border-gray-200 dark:border-neutral-700">
                    <div class="flex justify-between items-center">
                        <div class="text-xs">
                            @if ($order->payment_status == 'completed')
                                <span class="inline-flex items-center text-green-600 dark:text-green-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Paid
                                </span>
                            @else
                                <span class="inline-flex items-center text-amber-600 dark:text-amber-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Pending payment
                                </span>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="space-y-1">
                                <!-- Subtotal -->
                                {{-- <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Subtotal:</span>
                                    <span class="text-sm font-medium">₱{{ number_format($order->total, 2) }}</span>
                                </div> --}}

                                <!-- Shipping Cost -->

                                @if ($order->shipping_price > 0)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Shipping:</span>
                                        <span
                                            class="text-sm font-medium dark:text-white">₱{{ number_format($order->shipping_price, 2) }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-green-500 dark:text-green-400">Free
                                            Shipping</span>
                                    </div>
                                @endif


                                <!-- Divider -->
                                <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>

                                <!-- Grand Total -->
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-800 dark:text-white">Total:</span>
                                    <span class="font-bold text-gray-900 dark:text-white">
                                        ₱{{ number_format($order->total, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Actions -->
                    <div class="mt-3 flex justify-end space-x-2">
                        {{-- <button type="button"
                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-neutral-600 shadow-xs text-xs font-medium rounded text-gray-700 dark:text-gray-200 bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none"
                            aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-ai-invoice-modal"
                            data-hs-overlay="#hs-ai-invoice-modal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" width="16" height="16"
                                fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zm.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51z" />
                                <path
                                    d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z" />
                            </svg>
                            Details
                        </button> --}}

                        @if ($order->order_status == 'pending' && $order->payment_status !== 'completed')
                            <button type="button" wire:confirm="Are you sure you want to cancel this order?"
                                wire:click="cancelOrder({{ $order->id }})"
                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 dark:text-red-400 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Cancel
                            </button>
                        @endif
                        @if ($order->order_status == 'delivered')
                            <a href="{{ route('page.shop') }}" wire:navigate
                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/30 hover:bg-amber-200 dark:hover:bg-amber-900/50 focus:outline-none">
                                Shop Again</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">
                    @if ($statusFilter === 'all')
                        No orders found
                    @else
                        No {{ $statusFilter }} orders
                    @endif
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if ($statusFilter === 'all')
                        Your order history is empty
                    @else
                        You don't have any {{ $statusFilter }} orders
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Modal (keep your existing modal code) -->
    <div id="hs-ai-invoice-modal"
        class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none"
        role="dialog" tabindex="-1" aria-labelledby="hs-ai-invoice-modal-label">
        <!-- Your existing modal content -->
    </div>

</div>
