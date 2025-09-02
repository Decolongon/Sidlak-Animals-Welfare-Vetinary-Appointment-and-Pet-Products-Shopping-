<?php

namespace App\Helpers;

use App\Models\Ecommerce\Cart;
use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Product;
use Illuminate\Support\Facades\Auth;

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
    public static function calculateTotalShipping($checkoutItems)
    {
        $totalShipping = 0;

        foreach ($checkoutItems as $item) {
            $requiresShipping = $item->product->prod_requires_shipping;
            $cost = $item->product->shipping_cost;

            if ($requiresShipping) {
                $totalShipping += $cost;
            }
        }

        return $totalShipping;
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
            $product->prod_quantity -= $quantity;
            $product->save();

            $orders->orderItems()->create([
                'product_id' => $item->product->id,
                'quantity' => $quantity,
                'price' => $isDiscounted,
                'size' => $item->size,

            ]);
            //dd($item);
            if ($item->size && $product->prod_unit === 'diff_size') {
                $variant = $product->images()
                    ->where('sizes', $item->size)
                    ->first();
                if ($variant) {
                    $variant->quantity -= $quantity;
                    $variant->save();
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
    public function getCheckOutItems()
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
                // Attach variant info to each cart item
                if ($cartItem->size && $cartItem->product->prod_unit === 'diff_size') {
                    $cartItem->variant = $cartItem->product->images
                        ->where('sizes', $cartItem->size)
                        ->first();
                }
                $this->getPrimaryImage($cartItem);
            });

        return $checkoutItems;
    }

    // get buynow item
    public function getBuynowItem()
    {
        $singleProductId = session('buy_now_product');
        if (!session('buy_now_mode')) {
            return collect([]);
        }
        $product = Product::with(['productDiscounts' => function ($query) {
            $query->ActiveProdDiscount(); // scope ara sa productDiscount model
        }, 'images'])->find($singleProductId);

        // session()->forget('buy_now_product');
        // session()->forget('buy_now_mode');

        if ($product) {
            if ($product->images->isNotEmpty()) {
                $product->primary_image = $product->images
                    ->where('is_primary', true)
                    ->first() ?? $product->images->first();
            } else {
                $product->primary_image = null;
            }
            $quantity = session()->get('buy_now_quantity', 1);

            return collect([
                (object)[
                    'product' => $product,
                    'quantity' => $quantity,
                    'id' => null,
                    'size' => null,
                ]
            ]);
        }
    }
}
