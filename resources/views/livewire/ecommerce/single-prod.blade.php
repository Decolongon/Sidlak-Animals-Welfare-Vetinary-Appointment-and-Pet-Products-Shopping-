<div x-data="{
    activeImage: '{{ asset(Storage::url($primary_image->url)) }}',
    currentIndex: 0,
    images: [
        @foreach ($product->images as $image)
            '{{ asset(Storage::url($image->url)) }}', @endforeach
    ],
    setActiveImage(url, index) {
        this.activeImage = url;
        this.currentIndex = index;
    },
    nextImage() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.activeImage = this.images[this.currentIndex];
    },
    prevImage() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.activeImage = this.images[this.currentIndex];
    }
}"> {{-- Open --}}

    <!-- Main Product Container -->
    <div class="bg-white dark:bg-[#262626] p-6 rounded-lg shadow-md mt-10">
        <!-- Product Image & Details Grid -->
        <div class="grid md:grid-cols-2 gap-6" wire:ignore>
            <!-- Product Image -->
            <div class="relative">
                <img id="main-product-image" class="w-full h-[200px] md:h-[300px] lg:h-[350px] object-cover rounded-xl"
                    :src="activeImage" alt="{{ $product->prod_slug }}">

                <!-- Navigation Arrows -->
                @if ($product && $product->images->count() > 1)
                    <div class="absolute inset-0 flex items-center justify-between p-4">
                        <button @click="prevImage()"
                            class="bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button @click="nextImage()"
                            class="bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div>
                <h1 class="text-2xl text-black dark:text-white">
                    {{ ucwords($product->prod_name) }}
                    @if($product->prod_unit == 'has_dimensions')
                       <br>Dimensions: {{$product->prod_length}} x {{$product->prod_width}} x {{$product->prod_height}} cm
                    @endif
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
                         {{ $product->prod_quantity > 10 ? 'text-green-600 dark:text-green-400' : 'text-orange-500 dark:text-orange-500' }}">

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
                    @if ($product->prod_quantity > 0 || $product->prod_unit === 'diff_size')
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


        <!-- Image Gallery (if multiple images) -->
        @if ($product && $product->images->count() > 1)
            <div class="mt-4">
                <div class="flex space-x-2 overflow-x-auto pb-2">
                    @foreach ($product->images as $index => $image)
                        <div class="flex-shrink-0">
                            <img src="{{ asset(Storage::url($image->url)) }}" alt="{{ $product->prod_slug }}"
                                class="h-20 w-20 object-cover rounded-md cursor-pointer border-2 transition-all duration-200"
                                :class="currentIndex === {{ $index }} ? 'border-amber-500' :
                                    'border-transparent hover:border-amber-500'"
                                @click="setActiveImage('{{ asset(Storage::url($image->url)) }}', {{ $index }})" />
                        </div>
                    @endforeach
                </div>
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

                    @if (isset($this->getRelatedProduct) && $this->getRelatedProduct->isNotEmpty())
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
                                @forelse($this->getRelatedProduct as $product)
                                    <div wire:key="product-{{ $product->id }}"
                                        class="group flex flex-col border border-gray-200 hover:border-transparent hover:shadow-lg focus:outline-none focus:border-transparent focus:shadow-lg transition duration-300 rounded-xl p-4 dark:border-neutral-700 dark:hover:border-transparent dark:hover:shadow-black/40 dark:focus:border-transparent dark:focus:shadow-black/40">
                                        <!-- Stock & Discount Info -->
                                        <p
                                            class="text-xs flex justify-between items-center gap-x-2 text-green-500 dark:text-green-400 mb-2">
                                            @if ($product->discounted_price !== null && $product->prod_unit !== 'diff_size')
                                                {{ ucwords($product->productDiscounts->first()?->discount_name) }}
                                            @endif

                                            @if ($product->prod_unit !== 'diff_size')
                                                @if ($product->prod_quantity < 1)
                                                    <span class="text-red-500">Out of Stock</span>
                                                @elseif ($product->prod_quantity <= 10)
                                                    <span class="text-orange-500">Low in Stock
                                                        {{ (int) $product->prod_quantity }}
                                                        left
                                                    </span>
                                                @else
                                                    <span class="text-green-500">In Stock</span>
                                                @endif
                                            @endif
                                        </p>

                                        <!-- Product Image & Info -->
                                        <div class="aspect-w-16 aspect-h-11">
                                            <a wire:navigate.hover
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
                                                        @if ($product->prod_unit !== 'diff_size')
                                                            ₱{{ number_format($product->prod_price, 2) }}
                                                        @endif
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
