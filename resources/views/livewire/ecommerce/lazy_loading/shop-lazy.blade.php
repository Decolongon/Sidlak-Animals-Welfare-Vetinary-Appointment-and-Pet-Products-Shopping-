<div>
    <!-- Sorting Menu Skeleton -->
    <div class="flex items-center gap-4 mt-10 ml-6">
        <!-- Sorting Dropdown Skeleton -->
        <div class="hs-dropdown relative inline-flex">
            <div
                class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-gray-100 text-gray-800 shadow-sm h-[42px] w-[200px] animate-pulse">
            </div>
        </div>

        <!-- Search Bar Skeleton -->
        <div class="w-80">
            <div class="relative">
                <div class="py-3 ps-10 pe-4 block w-full border-gray-200 rounded-lg bg-gray-100 h-[42px] animate-pulse">
                </div>
            </div>
        </div>
    </div>

    <!-- Product Grid Skeleton -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4">
            <!-- Repeat skeleton card 8 times (or however many you want to show) -->
            @for ($i = 0; $i < 12; $i++)
                <div class="group flex flex-col border border-gray-200 rounded-xl p-4 animate-pulse">
                    <!-- SKU/Stock Skeleton -->
                    <div class="flex justify-between mb-2">
                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                    </div>

                    <!-- Image Skeleton -->
                    <div class="w-full h-[180px] md:h-[200px] lg:h-[250px] bg-gray-200 rounded-xl"></div>

                    <!-- Product Info Skeleton -->
                    <div class="mt-4 space-y-2">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>

                    <!-- Price Skeleton -->
                    <div class="mt-5 h-4 bg-gray-200 rounded w-1/3"></div>

                    <!-- Add to Cart Button Skeleton -->
                    <div class="mt-auto pt-4">
                        <div class="h-10 bg-gray-200 rounded-lg w-full"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
