<div> {{-- Open --}}

    <!-- Main Product Container -->
    <div class="bg-white dark:bg-[#262626] p-6 rounded-lg shadow-md mt-10">
        <!-- Product Image & Details Grid -->
        <div class="grid md:grid-cols-2 gap-6" wire:ignore>
            <!-- Product Image -->
            <div class="relative">
                <img class="w-full h-[200px] md:h-[300px] lg:h-[350px] object-cover rounded-xl"
                    src="{{ asset(Storage::url($primary_image->url)) }}" alt="{{ $product->prod_slug }}">
            </div>

            <!-- Product Details -->
            <div>
                <h1 class="text-2xl font-bold text-black dark:text-white">
                    {{ ucwords($product->prod_name) }}
                </h1>
                <p class="text-black dark:text-white mt-2">
                    {{ ucfirst($product->prod_short_description) }}
                </p>

                <p class="text-sm font-semibold text-gray-800 dark:text-neutral-300">

                    @if ($product->discounted_price !== null && $product->prod_unit !== 'diff_size')
                        <del class="text-gray-500 dark:text-neutral-400">
                            ₱{{ number_format($product->prod_price, 2) . ' ' }}
                        </del>
                        <span class="text-sm font-semibold text-gray-800 dark:text-neutral-300">
                            ₱{{ number_format($product->discounted_price, 2) }}
                        </span>
                        <span class="text-green-600 font-semibold ml-2">
                            {{ $product->discount_label }}
                        </span>
                    @else
                        @if ($product->prod_unit !== 'diff_size')
                            ₱{{ number_format($product->prod_price, 2) }}
                        @endif
                    @endif

                </p>

                @if ($product->prod_unit !== 'diff_size')
                    <p
                        class="mt-2 
                         {{ $product->prod_quantity > 10 ? 
                         'text-green-600 dark:text-green-400' : 
                         'text-orange-500 dark:text-orange-500' }}">

                        @if ($product->prod_quantity > 10)
                            In Stock
                        @elseif ($product->prod_quantity > 1 && $product->prod_quantity <= 10)
                            Low in Stock ({{ (int) $product->prod_quantity }})
                        @else
                          {{-- do nothing if out of stock --}}
                        @endif
                    </p>
                @endif

                <!-- Add to Cart & Buy Now Buttons -->
                <div class="flex items-center space-x-4 mt-4">
                    @if ($product->prod_quantity > 0)
                        @if ($product->prod_unit !== 'diff_size')
                            <livewire:ecommerce.add-to-cart-form :product_id="$product->id"
                                wire:key="add-to-cart-{{ $product->id }}" />
                        @else
                            <livewire:ecommerce.product-variant :product_id="$product->id" />
                        @endif
                        @if ($product->prod_unit !== 'diff_size')
                            <x-button wire:click="buyNow({{ $product->id }})"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                                Buy Now
                            </x-button>
                        @endif
                    @else
                        <p class="text-red-600 dark:text-red-400">{{ __('Out of stock') }}</p>
                    @endif

                </div>

                <!-- Category -->
                <p class="text-black dark:text-white mt-2">
                    <strong>Category:
                    </strong>{{ ucwords($product->productCategories->pluck('prod_cat_name')->join(', ')) }}
                </p>
            </div>
        </div>

        <!-- Image Slider (if multiple images) -->
        @if ($product && $product->images->count() > 1)
            <div data-hs-carousel='{
                "loadingClasses": "opacity-0",
                "dotsItemClasses": "hs-carousel-active:bg-blue-700 hs-carousel-active:border-blue-700 size-3 border border-gray-400 rounded-full cursor-pointer dark:border-neutral-600 dark:hs-carousel-active:bg-blue-400 dark:hs-carousel-active:border-blue-400",
                "slidesQty": { "xs": 1, "lg": 3 },
                "isDraggable": false,
                "passiveListeners": false
            }'
                wire:ignore class="relative mt-10">
                <!-- Carousel Container -->
                <div class="hs-carousel w-full overflow-hidden bg-white dark:bg-[#1f1f1f] rounded-lg shadow-lg">
                    <!-- Carousel Slides -->
                    <div class="relative min-h-56 -mx-1">
                        <div
                            class="hs-carousel-body absolute top-0 bottom-0 start-0 flex flex-nowrap opacity-0 cursor-grab transition-transform duration-700 hs-carousel-dragging:transition-none hs-carousel-dragging:cursor-grabbing">
                            @foreach ($product->images as $image)
                                <div class="hs-carousel-slide px-1">
                                    <div
                                        class="flex justify-center items-center w-full h-full bg-gray-100 dark:bg-neutral-900 p-3 rounded-md">
                                        <img loading="lazy" src="{{ asset(Storage::url($image->url)) }}"
                                            alt="{{ $product->prod_slug }}"
                                            class="max-h-40 object-contain rounded-md shadow-md border dark:border-neutral-700 bg-white dark:bg-neutral-900 p-2" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Carousel Navigation Buttons -->
                <button type="button"
                    class="hs-carousel-prev hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute inset-y-0 start-0 flex items-center justify-center w-11.5 h-full text-gray-800 hover:bg-gray-800/10 focus:outline-none focus:bg-gray-800/10 dark:text-white dark:hover:text-blue-400 rounded-s-lg">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path d="m15 18-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button type="button"
                    class="hs-carousel-next hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute inset-y-0 end-0 flex items-center justify-center w-11.5 h-full text-gray-800 hover:bg-gray-800/10 focus:outline-none focus:bg-gray-800/10 dark:text-white dark:hover:text-blue-400 rounded-e-lg">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path d="m9 18 6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <!-- Carousel Dots -->
                <div class="hs-carousel-pagination flex justify-center absolute bottom-3 inset-x-0 gap-x-2"></div>
            </div>
        @endif

        <!-- Tabs Section -->
        <div class="mt-6 border-t border-gray-400 dark:border-gray-700 pt-4">
            <!-- Tabs Navigation -->
            <div class="flex space-x-6 text-black dark:text-white font-medium">
                <a href="#" wire:click.prevent="$set('activeTab', 'description')"
                    :class="{
                        'border-b-2 border-black dark:border-white pb-2': $wire
                            .activeTab === 'description',
                        'text-gray-500 dark:text-gray-400': $wire
                            .activeTab !== 'description'
                    }">
                    Description
                </a>
                <a href="#" wire:click.prevent="$set('activeTab', 'reviews')"
                    :class="{
                        'border-b-2 border-black dark:border-white whitespace-nowrap pb-2': $wire
                            .activeTab === 'reviews',
                        'text-gray-500 dark:text-gray-400 whitespace-nowrap': $wire
                            .activeTab !== 'reviews'
                    }">
                    Reviews (<span>{{ $count_reviews }}</span>)
                </a>
                @if (Auth::check())
                    <a href="#" wire:click.prevent="$set('activeTab', 'post_reviews')"
                        :class="{
                            'border-b-2 border-black dark:border-white pb-2': $wire
                                .activeTab === 'post_reviews',
                            'text-gray-500 dark:text-gray-400': $wire
                                .activeTab !== 'post_reviews'
                        }">
                        Post a Review
                    </a>
                @endif
            </div>

            <!-- Description Tab Content -->
            @if ($activeTab == 'description')
                <div>
                    <div x-data="{
                        expanded: true,
                        fullText: @js($product->prod_description ?? ''),
                        shortText: @js(Str::limit($product->prod_description ?? '', 300, '...'))
                    }" x-init class="mt-2 text-gray-800 dark:text-white">
                        <div :class="expanded ? '' : 'line-clamp-4'" class="text-gray-800 dark:text-white"
                            x-html="expanded ? fullText : shortText"></div>
                        <template x-if="fullText && shortText && fullText.length > shortText.length">
                            <div class="mt-2">
                                <button class="text-blue-500 underline" @click="expanded = !expanded">
                                    <span x-text="expanded ? 'Show Less' : 'Show More'"></span>
                                </button>
                            </div>
                        </template>
                    </div>

                    @if (isset($relatedProducts) && $relatedProducts->isNotEmpty())
                        <!-- Related Products Section -->
                        {{-- @if ($relatedProducts->isNotEmpty()) --}}
                        <div class="col-span-full text-center py-10">
                            <p class="text-gray-500 dark:text-neutral-400 text-lg">
                                {{ __('----You may also like----') }}
                            </p>
                        </div>
                        {{-- @endif --}}

                        <!-- Related Products Grid -->
                        <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto scroll-visible"
                            wire:ignore>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4">
                                @forelse($relatedProducts as $product)
                                    <div wire:key="product-{{ $product->id }}"
                                        class="group flex flex-col border border-gray-200 hover:border-transparent hover:shadow-lg focus:outline-none focus:border-transparent focus:shadow-lg transition duration-300 rounded-xl p-4 dark:border-neutral-700 dark:hover:border-transparent dark:hover:shadow-black/40 dark:focus:border-transparent dark:focus:shadow-black/40">
                                        <!-- Stock & Discount Info -->
                                        <p
                                            class="text-xs flex justify-between items-center gap-x-2 text-green-500 dark:text-green-400 mb-2">
                                            @if ($product->discounted_price !== null)
                                                {{ ucwords($product->productDiscounts->first()?->discount_name) }}
                                            @endif
                                            <span
                                                class="{{ $product->prod_quantity > 10 ? 'text-green-500' : 'text-red-500' }}">
                                                {{ $product->prod_quantity > 10 ? 'In Stock' : 'Low in Stock ' . ($product->prod_unit == 'kg' ? (float) $product->prod_quantity . ' kg left' : (int) $product->prod_quantity . ' left') }}
                                            </span>
                                        </p>

                                        <!-- Product Image & Info -->
                                        <div class="aspect-w-16 aspect-h-11">
                                            <a wire:navigate
                                                href="{{ route('page.singleProd', ['prod_slug' => $product->prod_slug]) }}">

                                                <img class="w-full h-[180px] md:h-[200px] lg:h-[250px] object-cover rounded-xl"
                                                    src="{{ $product->primary_image ? asset(Storage::url($product->primary_image->url)) : asset('default-image.jpg') }}"
                                                    alt="{{ $product->prod_slug }}">
                                            </a>
                                        </div>

                                        <!-- Product Name & Price -->
                                        <div class="flex flex-col flex-grow mt-4">
                                            <h5
                                                class="text-md text-gray-800 dark:text-neutral-300 dark:group-hover:text-white">
                                                {{ ucwords($product->prod_name) }}
                                                @if ($product->prod_unit == 'kg')
                                                    - {{ $product->prod_weight }}{{ $product->prod_unit }}
                                                @endif
                                                @if ($product->prod_unit == 'g')
                                                    - {{ $product->prod_weight }}{{ $product->prod_unit }}
                                                @endif
                                            </h5>
                                            <p class="mt-5 text-gray-600 dark:text-neutral-400"></p>
                                            <div
                                                class="flex flex-wrap items-center justify-between gap-x-4 overflow-hidden max-w-full mt-2">
                                                <p class="text-sm font-semibold text-gray-800 dark:text-neutral-300">
                                                    @if ($product->discounted_price !== null)
                                                        <del class="text-gray-500 dark:text-neutral-400">
                                                            ₱{{ number_format($product->prod_price, 2) }}
                                                        </del>
                                                        <span
                                                            class="text-sm font-semibold text-gray-800 dark:text-neutral-300">
                                                            ₱{{ number_format($product->discounted_price, 2) }}
                                                        </span>
                                                        <span
                                                            class="text-green-600 font-semibold ml-2">{{ $product->discount_label }}</span>
                                                    @else
                                                        ₱{{ number_format($product->prod_price, 2) }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Placeholder for Buttons or Actions -->
                                        <div class="mt-auto flex items-center gap-x-3">
                                            <!-- You can add buttons here if needed -->
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center py-10">
                                        <p class="text-gray-500 dark:text-neutral-400 text-lg">No Related Products
                                            Available.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif


                </div>
            @endif

            <!-- Reviews Tab Content -->
            @if ($activeTab == 'reviews')
                <div wire:ignore.self>
                    @livewire('ecommerce.get-prod-reviews', ['product_id' => $product->id])
                </div>
            @endif

            <!-- Post Review Tab Content -->
            @if ($activeTab == 'post_reviews')
                <div wire:ignore.self>
                    @livewire('ecommerce.product-reviews-form', ['product_id' => $product->id])
                </div>
            @endif




        </div>
    </div>



</div> {{-- Close --}}
