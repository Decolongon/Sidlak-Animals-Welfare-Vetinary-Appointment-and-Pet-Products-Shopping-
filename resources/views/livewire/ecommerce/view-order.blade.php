<div>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Your Orders</h1>
            <!-- Order Status Update Notification -->
            @if (session()->has('order_status_updated'))
                <div
                    class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900/30 dark:border-green-800 dark:text-green-400">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ session('order_status_updated') }}</span>
                    </div>
                </div>
            @endif

            @if (session()->has('success'))
                <div
                    class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900/30 dark:border-green-800 dark:text-green-400">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
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
        @forelse ($this->getOrders as $order)
            <div wire:key="order-{{ $order->order_num }}"
                class="mb-6 bg-white dark:bg-neutral-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-neutral-700 transition-all hover:shadow-md">
                <!-- Order Header -->
                <div
                    class="px-5 py-4 border-b border-gray-200 dark:border-neutral-700 flex justify-between items-center">
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Tracking #:
                            {{ $order->order_num }}
                        </span>
                        <br>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Shipping address:
                            @foreach ($order->shippingAddresses as $ship)
                                {{ str()->limit(ucwords(strtolower($ship->complete_address)), 40, '...') }}
                            @endforeach
                        </span>
                        <h3 class="text-xs font-medium text-gray-800 dark:text-white mt-1">
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
                                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12zM10 5a1 1 0 011 1v5a1 1 0 11-2 0V6a1 1 0 011-1zm0 10a1 1 0 100-2 1 1 0 000 2z"
                                                    clip-rule="evenodd" />
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
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancel
                                        </button>
                                    </div>
                                </template>

                                <template x-if="cancelled">
                                    <div class="flex items-center gap-1.5 text-green-700 dark:text-green-400 text-sm">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <p class="font-medium">Cancellation stopped</p>
                                    </div>
                                </template>
                            </div>
                        @endif


                    </div>
                    <div class="text-right">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
        @if ($order->order_status == 'pending') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
        @elseif($order->order_status == 'processing') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
        @elseif($order->order_status == 'shipped') bg-green-100 text-green-800 dark:bg-purple-900/30 dark:text-green-400
        @elseif($order->order_status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
        @elseif($order->order_status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">
                            {{ ucfirst($order->order_status) }}
                        </span>

                        <!-- Shipping Method moved here -->
                        <h3 class="text-xs font-medium text-gray-800 dark:text-white mt-2">
                            @if ($order->shipping_method === 'COD')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                    💵 Cash on Delivery
                                </span>
                            @elseif($order->shipping_method === 'gcash')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    📱 GCash
                                </span>
                            @elseif($order->shipping_method === 'paymaya')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                    💳 Maya
                                </span>
                            @elseif($order->shipping_method === 'grabpay')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    🚗 GrabPay
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ ucfirst($order->shipping_method) }}
                                </span>
                            @endif
                        </h3>
                    </div>
                </div>

                <!-- Order Items  scrollable -->
                <div class="divide-y divide-gray-200 dark:divide-neutral-700">
                    <div class="max-h-60 overflow-y-auto">
                        @foreach ($order->orderItems as $index => $item)
                            <div class="px-5 py-3 flex justify-between items-center" wire:key="{{ $item->id }}">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex-shrink-0 bg-gray-100 dark:bg-neutral-700 rounded-md overflow-hidden w-10 h-10 flex items-center justify-center">
                                        @if (isset($item->variant))
                                            <img src="{{ asset(Storage::url($item->variant->url)) }}"
                                                alt="{{ $item->product->prod_name }}"
                                                class="object-cover w-full h-full">
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
                                            @if ($item->product->prod_unit === 'pcs' || $item->product->prod_unit === 'has_dimensions')
                                                {{ number_format($item->quantity, 0) }} pcs
                                            @elseif($item->product->prod_unit === 'diff_size' && isset($item->variant))
                                                {{ number_format($item->quantity, 0) }} pcs
                                            @else
                                                {{ number_format($item->quantity, 0) }} ×
                                                {{ number_format($item->product->prod_weight, 2) }}
                                                {{ $item->product->prod_unit }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-800 dark:text-white">
                                    Price:₱ {{ number_format($item->price, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
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

                        @if ($order->order_status == 'shipped')
                            <button type="button" wire:confirm="Are you sure that this package has been delivered?"
                                wire:click="toDelivered({{ $order->id }})"
                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30 hover:bg-green-200 dark:hover:bg-green-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Mark as Delivered
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
