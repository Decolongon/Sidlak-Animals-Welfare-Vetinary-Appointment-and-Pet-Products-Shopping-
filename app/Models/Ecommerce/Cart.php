<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $table = 'carts';

    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'session_id',
        'size',
        
    ];

   
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDiscountedPrice($product)
    {
        $product->discounted_price = null;
        $product->discount_amount = null;
        $product->discount_label = null;

        // Get the first active discount
        $discount = $product->productDiscounts->first();

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
