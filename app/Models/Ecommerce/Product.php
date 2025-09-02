<?php

namespace App\Models\Ecommerce;

use App\Models\Ecommerce\OrderItem;
use App\Models\Ecommerce\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Ecommerce\ProductDiscount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    protected $fillable = [
        'prod_name',
        'prod_slug',
        'prod_price',
        'prod_description',
        'prod_quantity',
        'prod_sku',
        'prod_unit',
        'prod_requires_shipping',
        'prod_weight', // pila ka kilo ang eh baligya
        'prod_short_description',
        'is_visible_to_market',
        'shipping_cost',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'prod_requires_shipping' => 'boolean',
        'is_visible_to_market' => 'boolean',
    ];

    public function productCategories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class,
        'product_prod_categories','product_id','product_category_id')->withTimestamps();
    }


    // public function category(): BelongsTo
    // {
    //     return $this->belongsTo(ProductCategory::class, 'product_category_id');
    // }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function productDiscounts(): BelongsToMany
    {
       return $this->belongsToMany( ProductDiscount::class,'discount_details','product_id','product_discount_id')
        ->withPivot('discount_code','discount_type','discounted_price')
        ->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
         return 'prod_slug';
    }

    protected function scopeInstock(Builder $query): void
    {
        $query->where('prod_quantity', '>', 10)
              ->where('prod_unit', '!=', 'diff_size');
    }

    protected function scopeLowInStock(Builder $query): void
    {
        $query->where('prod_quantity', '>', 0)
              ->where('prod_quantity', '<=', 10)
              ->where('prod_unit', '!=', 'diff_size');
    }

    protected function scopeOutOfStock(Builder $query): void
    {
        $query->where('prod_quantity', '<',1)
              ->where('prod_unit', '!=', 'diff_size');
    }

    protected function scopeWithVariants(Builder $query): void
    {
        $query->where('prod_unit', 'diff_size');
    }


    public function calculateDiscountedPrice(): void
    {
        //  foreach ($products as $product) {
                $this->discounted_price = null;
                $this->discount_amount = null;
                $this->discount_label = null;

                $discount = $this->productDiscounts->first();

                if (!$discount || !$discount->pivot) {
                    return;
                }

                $type = $discount->pivot->discount_type;
                $value = floatval($discount->pivot->discounted_price);

                if ($type === 'fixed') {
                    $this->discount_amount = $value;
                    $this->discounted_price = $this->prod_price - $value;
                    $this->discount_label = ' ₱' . number_format($value, 0) . ' off';
                } 
                if ($type === 'percent') {
                    $discountValue = $this->prod_price * ($value / 100);
                    $this->discount_amount = $discountValue;
                    $this->discounted_price = $this->prod_price - $discountValue;
                    $this->discount_label = number_format($value, 0) . ' % off';
                }

               

            // }
    }

   
}