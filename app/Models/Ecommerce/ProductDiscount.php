<?php

namespace App\Models\Ecommerce;

use App\Models\Ecommerce\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductDiscount extends Model
{
    protected $fillable = [
       'discount_name',
       'desc_discount',
       'discount_slug',
       'banner',
       'start_at',
       'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function products(): BelongsToMany
    {
       return $this->belongsToMany(Product::class,'discount_details','product_discount_id','product_id')
        ->withPivot('discount_code','discount_type','discounted_price')
        ->withTimestamps();
    }

    public function getRouteKeyName()
    {
        return 'discount_slug';
    }

}
