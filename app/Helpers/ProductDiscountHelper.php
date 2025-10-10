<?php

namespace App\Helpers;

use App\Models\Ecommerce\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Ecommerce\ProductDiscount;

class ProductDiscountHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * 
     *
     * @param [type] $products
     * @return void
     */
    public function calculateDiscountedPrice($products): void
    {
        foreach ($products as $product) {
            $product->discounted_price = null;
            $product->discount_amount = null;
            $product->discount_label = null;

            $discount = $product->productDiscounts()
                ->ActiveProdDiscount()
                ->first();

            if (!$discount || !$discount->pivot) {
                continue;
            }


            $type = $discount->pivot->discount_type;
            $value = floatval($discount->pivot->discounted_price);

            if ($type === 'fixed') {
                $product->discount_amount = $value;
                $product->discounted_price = $product->prod_price - $value;
                $product->discount_label = ' ₱' . number_format($value, 0) . ' off';
            }
            if ($type === 'percent') {
                $discountValue = $product->prod_price * ($value / 100);
                $product->discount_amount = $discountValue;
                $product->discounted_price = $product->prod_price - $discountValue;
                $product->discount_label = number_format($value, 0) . ' % off';
            }
        }
        // return $product->discounted_price;
    }


    public function calculateDiscountedPriceCart($cartItems): void
    {
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;

            // Skip if product doesn't exist
            if (!$product) {
                continue;
            }

            $product->discounted_price = null;
            $product->discount_amount = null;
            $product->discount_label = null;

            // Get the discount for this product
            $discount = $product->productDiscounts()
                ->ActiveProdDiscount()
                ->first();

            // Check if discount exists and has all required properties
            if (!$discount || !$discount->pivot || !$discount->start_at || !$discount->end_at) {
                continue;
            }

            // Check if cart item was created during discount period
            if (!$cartItem->created_at->between($discount->start_at, $discount->end_at)) {
                continue;
            }


            $type = $discount->pivot->discount_type;
            $value = floatval($discount->pivot->discounted_price);

            if ($type === 'fixed') {
                $product->discount_amount = $value;
                $product->discounted_price = $product->prod_price - $value;
                $product->discount_label = ' ₱' . number_format($value, 0) . ' off';
            }
            if ($type === 'percent') {
                $discountValue = $product->prod_price * ($value / 100);
                $product->discount_amount = $discountValue;
                $product->discounted_price = $product->prod_price - $discountValue;
                $product->discount_label = number_format($value, 0) . ' % off';
            }
        }
    }
    /**
     * Undocumented function
     *
     * @param [type] $product
     * @return void
     * para sa checkout
     */
    // public function getDiscountedPrice($product)
    // {
    //     $product->discounted_price = null;
    //     $product->discount_amount = null;
    //     $product->discount_label = null;

    //     $discount_start = ProductDiscount::get();

    //     // Get the first active discount
    //     $discount = $product->productDiscounts()
    //         ->ActiveProdDiscount()
    //         ->first();

    //     if (!$discount || !$discount->pivot) {
    //         return;
    //     }


    //     $type = $discount->pivot->discount_type;
    //     $value = floatval($discount->pivot->discounted_price);

    //     if ($type === 'fixed') {
    //         $product->discount_amount = $value;
    //         $product->discounted_price = $product->prod_price - $value;
    //         $product->discount_label = '₱' . number_format($value, 0) . ' off';
    //     }
    //     if ($type === 'percent') {
    //         $discountValue = $product->prod_price * ($value / 100);
    //         $product->discount_amount = $discountValue;
    //         $product->discounted_price = $product->prod_price - $discountValue;
    //         $product->discount_label = number_format($value, 0) . '% off';
    //     }

    //     return $product->discounted_price;
    // }





    public function getDiscountedPrice($product, $cartItem = null)
    {
        $product->discounted_price = null;
        $product->discount_amount = null;
        $product->discount_label = null;

        // Get the first active discount
        $discount = $product->productDiscounts()
            ->ActiveProdDiscount()
            ->first();

        if (!$discount || !$discount->pivot) {
            return $product->prod_price; // Return original price if no discount
        }

        // If cart item is provided, check if it was created during discount period
        if ($cartItem && ! session()->get('buy_now_product')) {
            if ($cartItem->created_at < $discount->start_at || $cartItem->created_at > $discount->end_at) {
                return $product->prod_price; // Return original price if cart item was created outside discount period
            }
        }

        $type = $discount->pivot->discount_type;
        $value = floatval($discount->pivot->discounted_price);

        if ($type === 'fixed') {
            $product->discount_amount = $value;
            $product->discounted_price = $product->prod_price - $value;
            $product->discount_label = '₱' . number_format($value, 0) . ' off';
        }
        if ($type === 'percent') {
            $discountValue = $product->prod_price * ($value / 100);
            $product->discount_amount = $discountValue;
            $product->discounted_price = $product->prod_price - $discountValue;
            $product->discount_label = number_format($value, 0) . '% off';
        }

        return $product->discounted_price ?? $product->prod_price;
    }
}
