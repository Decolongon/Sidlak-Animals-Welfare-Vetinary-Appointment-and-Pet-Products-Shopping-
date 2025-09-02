<?php

namespace App\Helpers;

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


    /**
     * Undocumented function
     *
     * @param [type] $product
     * @return void
     * para sa checkout
     */
     public function getDiscountedPrice($product)
    {
        $product->discounted_price = null;
        $product->discount_amount = null;
        $product->discount_label = null;

        // Get the first active discount
        $discount = $product->productDiscounts()
                ->ActiveProdDiscount()
                ->first();

        if (!$discount || !$discount->pivot) {
            return;
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

        return $product->discounted_price;
       
    }
}
