<?php

namespace App\Helpers;

use App\Models\Ecommerce\Cart;
use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Ecommerce\FreeShippingCity;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CheckOutHelper
{

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Calculate the total shipping cost.
     */
    public static function calculateTotalShipping($checkoutItems, $selectedCity)
    {
        $sel_checkout_items = (array) session()->get('selected_checkout_items', []);
        $totalShipping = 0;
        $cityCode  = static::getCitiesCode();

        // Check if free shipping is available for the selected city
        $hasFreeShipping = static::getFreeShippingCities($selectedCity);

        if ($hasFreeShipping) {
            return 0;
        }

        $hasShippingItems = false;
        $baseShippingCost = 0;
        $totalWeightCost = 0;
        $hasPaidShippingItem = false;

        foreach ($checkoutItems as $item) {
            $requiresShipping = $item->product->prod_requires_shipping;
            // $productUnit = $item->product->prod_unit;
            // $weight = $item->product->prod_weight;
            // $quantity = $item->quantity;
            $itemShippingCost = $item->product->shipping_cost;

            if ($requiresShipping) {
                $hasShippingItems = true;

                // Only set base shipping cost once from the first item that HAS shipping cost
                if ($baseShippingCost === 0 && $itemShippingCost > 0) {
                    $baseShippingCost = $itemShippingCost;
                    $hasPaidShippingItem = true;
                }
                //para sa distance
                // if (isset($cityCode[$selectedCity])) {
                //     $cost = $baseShippingCost * $cityCode[$selectedCity];
                // }


                // Accumulate weight costs from all items
                $totalWeightCost += static::calculateWeightCost($item);
            }
        }

        // If any item requires shipping, add base cost + accumulated weight costs
        if ($hasShippingItems) {
            $totalShipping = $baseShippingCost + $totalWeightCost;
        }

        return $totalShipping;
    }


    /**
     * Check if free shipping is available for the selected city
     */
    protected static function getFreeShippingCities($selectedCity)
    {
        if (!$selectedCity) {
            return false;
        }

        // Get all active free shipping cities
        $freeShippingCities = FreeShippingCity::where('is_active', true)->get();

        foreach ($freeShippingCities as $freeShippingCity) {
            $cityCodes = $freeShippingCity->city_code;

            // Check if the selected city code exists in the free shipping cities array
            if (is_array($cityCodes) && in_array($selectedCity, $cityCodes)) {
                return true;
            }
        }

        return false;
    }

    protected static function calculateWeightCost($item)
    {
        // Calculate weight costs for ALL items that require shipping
        $productUnit = $item->product->prod_unit;
        $weight = $item->product->prod_weight;
        $quantity = $item->quantity;

        $cost_weight_kg = 0;
        $cost_weight_g = 0;
        $dimentions = 0;

        if ($productUnit == 'kg') {
            $totalWeight = $weight * $quantity;
            $cost_weight_kg = $totalWeight * 15;
        }

        if ($productUnit == 'g') {
            $weightInKg = $weight / 1000;
            $totalWeight = $weightInKg * $quantity;
            $cost_weight_g = $totalWeight * 15;
        }

        if ($productUnit == 'has_dimensions') {
            $productLentgth = $item->product->prod_length;
            $productWidth = $item->product->prod_width;
            $productHeight = $item->product->prod_height;
            $totalWeight = ($productLentgth * $productWidth * $productHeight) / 3500;
            $dimentional_weight = $totalWeight * $quantity;
            $dimentions = $dimentional_weight * 15;
        }

        return $cost_weight_kg + $cost_weight_g + $dimentions;
    }


    protected static function getCitiesCode(): array
    {
        // city code and how much ang eh multiply sa shipping cost 
        $cityCode = [
            '064501' => 4, // BACOLOD CITY - Victorias → EB Magalona → Silay → Talisay → Bacolod
            '064502' => 5, // BAGO CITY - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago
            '064503' => 7, // BINALBAGAN - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Valladolid → Binalbagan
            '064504' => 2, // CADIZ CITY - Victorias → Manapla → Cadiz
            '064505' => 3, // CALATRAVA - Victorias → Manapla → Cadiz → Calatrava
            '064506' => 6, // CANDONI - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Valladolid → Candoni
            '064507' => 5, // CAUAYAN - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Cauayan
            '064508' => 1, // ENRIQUE B. MAGALONA - Victorias → EB Magalona
            '064509' => 4, // ESCALANTE CITY - Victorias → Manapla → Cadiz → Sagay → Escalante
            '064510' => 6, // HIMAMAYLAN CITY - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Himamaylan
            '064511' => 6, // HINIGARAN - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Hinigaran
            '064512' => 8, // HINOBA-AN - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Hinigaran → Sipalay → Hinobaan
            '064513' => 6, // ILOG - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Ilog
            '064514' => 4, // ISABELA - Victorias → Manapla → Cadiz → Sagay → Isabela
            '064515' => 7, // KABANKALAN CITY - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Hinigaran → Kabankalan
            '064516' => 5, // LA CARLOTA CITY - Victorias → EB Magalona → Silay → Talisay → Bacolod → La Carlota
            '064517' => 4, // LA CASTELLANA - Victorias → EB Magalona → Silay → Talisay → Bacolod → La Castellana
            '064518' => 1, // MANAPLA - Victorias → Manapla
            '064519' => 4, // MOISES PADILLA - Victorias → EB Magalona → Silay → Talisay → Bacolod → Moises Padilla
            '064520' => 4, // MURCIA - Victorias → EB Magalona → Silay → Talisay → Bacolod → Murcia
            '064521' => 5, // PONTEVEDRA - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Pontevedra
            '064522' => 5, // PULUPANDAN - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Pulupandan
            '064523' => 3, // SAGAY CITY - Victorias → Manapla → Cadiz → Sagay
            '064524' => 5, // SAN CARLOS CITY - Victorias → Manapla → Cadiz → Sagay → Escalante → San Carlos
            '064525' => 5, // SAN ENRIQUE - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → San Enrique
            '064526' => 2, // SILAY CITY - Victorias → EB Magalona → Silay
            '064527' => 7, // SIPALAY CITY - Victorias → EB Magalona → Silay → Talisay → Bacolod → Bago → Hinigaran → Sipalay
            '064528' => 3, // TALISAY CITY - Victorias → EB Magalona → Silay → Talisay
            '064529' => 4, // TOBOSO - Victorias → Manapla → Cadiz → Sagay → Toboso
            '064530' => 4, // VALLADOLID - Victorias → EB Magalona → Silay → Talisay → Bacolod → Valladolid
            '064531' => 1, // VICTORIAS CITY - Starting point
            '064532' => 3, // SALVADOR BENEDICTO - Victorias → EB Magalona → Silay → Salvador Benedicto
        ];

        return $cityCode;
    }
    /**
     * Get the primary image
     */
    public function getPrimaryImage($cartItem)
    {
        if ($cartItem->product->images->isNotEmpty()) {
            $cartItem->product->primary_image = $cartItem->product->images
                ->where('is_primary', true)
                ->first() ?? $cartItem->product->images->first();
        } else {
            $cartItem->product->primary_image = null;
        }

        $this->getVariant($cartItem);
    }
    // get variant sizes
    public function getVariant($cartItem)
    {
        // Attach variant info to each cart item
        if ($cartItem->size && $cartItem->product->prod_unit === 'diff_size') {
            $cartItem->variant = $cartItem->product->images
                ->where('sizes', $cartItem->size)
                ->first();
        }
    }

    /**
     *get available stock sa mga product na my sizes or variant
     */
    public function getAvailableStock($product, $size = null)
    {
        $availableQuantity = $product->prod_quantity;

        if ($size && $product->prod_unit === 'diff_size') {
            $variant = $product->images()
                ->where('sizes', $size)
                ->first();

            $availableQuantity = $variant ? $variant->quantity : $product->prod_quantity;
        }

        return $availableQuantity;
    }

    // Generate  random order number
    public function generateOrderNumber()
    {
        do {
            $random_int = str(rand(50, 10000));
            $random_char = str()->random(10);
            $order_num = strtoupper($random_int . $random_char);
        } while (
            Order::where('order_num', $order_num)->exists()
        );
        return $order_num;
    }

    // get order items para subtract ang quantity after successful checkout
    public function getOrderItems($orders, $checkoutItems, $isDiscounted)
    {

        $cartQuantities = session()->get('cart_quantities', []);
        foreach ($checkoutItems as $item) {
            $product = $item->product;
            // $finalPrice = $this->getDiscountedPrice($product) ?? $product->prod_price;
            $quantity = isset($cartQuantities[$item->id]) ? $cartQuantities[$item->id] : $item->quantity;
            // Deduct quantity from product stock
            //$product->prod_quantity -= $quantity;
            $product->decrement('prod_quantity', $quantity);
            //$product->save();

            $orders->orderItems()->create([
                'product_id' => $item->product->id,
                'quantity' => $quantity,
                'price' => $isDiscounted[$item->id],
                'size' => $item->size,

            ]);
            //dd($item);
            if ($item->size && $product->prod_unit === 'diff_size') {
                $variant = $product->images()
                    ->where('sizes', $item->size)
                    ->first();
                if ($variant) {
                    //$variant->quantity -= $quantity;
                    $variant->decrement('quantity', $quantity);
                    //$variant->save();
                }
            }

            Cart::where('id', $item->id)
                ->where('user_id', Auth::id())
                ->delete();
        }
    }

    // cart mode decrease quantity
    public function cartModeDecreaseQuantity($id = null)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->first();

        $currentQuantity = session()->get("cart_quantities.{$cartItem->id}", $cartItem->quantity);

        if ($cartItem && $currentQuantity > 1) {
            // $cartItem->quantity -= 1;

            // $cartItem->save();
            $cartQuantities = session()->get('cart_quantities', []);
            $cartQuantities[$cartItem->id] = $currentQuantity - 1;
            session()->put('cart_quantities', $cartQuantities);
        }
    }



    // public function cartModeDecreaseQuantity($id = null)
    // {
    //     $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->first();

    //     if (!$cartItem) {
    //         return true; // Item doesn't exist, consider it removed
    //     }

    //     $currentQuantity = session()->get("cart_quantities.{$cartItem->id}", $cartItem->quantity);

    //     if ($currentQuantity > 1) {
    //         // Decrease quantity
    //         $cartQuantities = session()->get('cart_quantities', []);
    //         $cartQuantities[$cartItem->id] = $currentQuantity - 1;
    //         session()->put('cart_quantities', $cartQuantities);
    //         return false; // Don't remove item
    //     } else {
    //        
    //         return true; // Remove item
    //     }
    // }


    // cart mode increase quantity
    public function cartModeIncreaseQuantity($id = null)
    {
        $cartItem = Cart::with(['product.images'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if ($cartItem) {
            $product = $cartItem->product;

            // Check variant stock if size exists
            $availableQuantity = $this->getAvailableStock($product, $cartItem->size);

            // Get current quantity from session or use cart item quantity
            $currentQuantity = session()->get("cart_quantities.{$cartItem->id}", $cartItem->quantity);

            if ($currentQuantity < $availableQuantity) {
                $cartQuantities = session()->get('cart_quantities', []);
                $cartQuantities[$cartItem->id] = $currentQuantity + 1;
                session()->put('cart_quantities', $cartQuantities);
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    // buy now mode decrease quantity
    public function buyNowModeDecreaseQuantity($id = null, $checkoutItems)
    {
        if (isset($checkoutItems[0]) && $checkoutItems[0]->quantity > 1) {
            $checkoutItems[0]->quantity -= 1;
            session()->put('buy_now_quantity', $checkoutItems[0]->quantity);
        }
    }

    // public function buyNowModeDecreaseQuantity($id = null, $checkoutItems)
    // {
    //     if (isset($checkoutItems[0]) && $checkoutItems[0]->quantity > 1) {
    //         $checkoutItems[0]->quantity -= 1;
    //         session()->put('buy_now_quantity', $checkoutItems[0]->quantity);
    //         return false; // Don't remove item
    //     } else {
    //         // Quantity is 1 or less, remove the item
    //         return true; // Remove item
    //     }
    // }

    // buy now mode increase quantity
    public function buyNowModeIncreaseQuantity($id = null, $checkoutItems)
    {
        if (isset($checkoutItems[0])) {
            $product = $checkoutItems[0]->product;
            $currentQuantity = $checkoutItems[0]->quantity;

            // Check available stock based on variant or product
            $availableQuantity = $this->getAvailableStock($product, $checkoutItems[0]->size);

            if ($currentQuantity < $availableQuantity) {
                $checkoutItems[0]->quantity += 1;
                session()->put('buy_now_quantity', $checkoutItems[0]->quantity);
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    // get cart checkout items
    public function getNormalCheckOutItems()
    {
        $selectedItemIds = (array) session()->get('selected_checkout_items', []);
        $cartQuantities = session()->get('cart_quantities', []);

        $checkoutItems = Cart::with(['product' => function ($query) {
            $query->with(['productDiscounts' => function ($subQuery) {
                $subQuery->ActiveProdDiscount(); // scope ara sa productDiscount model
            }]);
        }])
            ->where('user_id', Auth::id())
            ->whereIn('id', $selectedItemIds)
            ->get()
            ->each(function ($cartItem) use ($cartQuantities) {

                if (isset($cartQuantities[$cartItem->id])) {
                    $cartItem->quantity = $cartQuantities[$cartItem->id];
                }

                $this->getPrimaryImage($cartItem);
            });

        return $checkoutItems;
    }

    // get buynow item
    // public function getBuynowItem()
    // {
    //     $singleProductId = session('buy_now_product');
    //     $product_id = $singleProductId['product_id'] ?? $singleProductId;
    //     // if (!session('buy_now_mode')) {
    //     //     return collect([]);
    //     // }
    //     $product = Product::with(['productDiscounts' => function ($query) {
    //         $query->ActiveProdDiscount(); // scope ara sa productDiscount model
    //     }, 'images'])->find($product_id);

    //     // session()->forget('buy_now_product');
    //     // session()->forget('buy_now_mode');

    //     if ($product) {
    //         if ($product->images->isNotEmpty()) {
    //             $product->primary_image = $product->images
    //                 ->where('is_primary', true)
    //                 ->first() ?? $product->images->first();
    //         } else {
    //             $product->primary_image = null;
    //         }
    //         $quantity = session()->get('buy_now_quantity', 1);
    //         $size = $singleProductId['size'];

    //         $variant = null;
    //         if (isset($singleProductId['variant_id'])) {
    //             $variant = $product->images->find($singleProductId['variant_id']);
    //         }

    //         return collect([
    //             (object)[
    //                 'product' => $product,
    //                 'quantity' => $quantity,
    //                 'id' => $singleProductId['variant_id'] ?? null,
    //                 'size' => $size,
    //                 'variant' => $variant, // This is crucial for the frontend
    //                 'price' => $variant ? $variant->price : $product->prod_price,
    //             ]
    //         ]);
    //     }
    // }


    public function getBuynowItem()
    {
        $singleProductId = session('buy_now_product');

        // Handle buy now para sa variant (if product my size)
        if (is_array($singleProductId)) {
            $product_id = $singleProductId['product_id'] ?? $singleProductId;
            $size = $singleProductId['size'] ?? null;
            $variant_id = $singleProductId['variant_id'] ?? null;
        } else {
            // Handle normal checkout without variant
            $product_id = $singleProductId;
            $size = null;
            $variant_id = null;
        }

        if (!$product_id) {
            return collect([]);
        }

        $product = Product::with(['productDiscounts' => function ($query) {
            $query->ActiveProdDiscount(); // scope ara sa productDiscount model
        }, 'images'])->find($product_id);

        if (!$product) {
            return collect([]);
        }

        if ($product->images->isNotEmpty()) {
            $product->primary_image = $product->images
                ->where('is_primary', true)
                ->first() ?? $product->images->first();
        } else {
            $product->primary_image = null;
        }

        $quantity = session()->get('buy_now_quantity', 1);

        $variant = null;
        if ($variant_id) {
            $variant = $product->images->find($variant_id);
        }

        return collect([
            (object)[
                'product' => $product,
                'quantity' => $quantity,
                'id' => $variant_id,
                'size' => $size,
                'variant' => $variant,
                'price' => $variant ? $variant->price : $product->prod_price,
            ]
        ]);
    }
}
