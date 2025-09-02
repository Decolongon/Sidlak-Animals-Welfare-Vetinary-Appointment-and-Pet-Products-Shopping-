<?php

namespace App\Models\Ecommerce;

use App\Models\Ecommerce\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    //protected $table = 'product_images';
    protected $fillable = [
        'product_id',
        // 'product_img',
        'url',
        'sizes',
        'is_primary',
        'quantity',
        'price',
    ];
     /**
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'is_primary' => 'boolean',
    ];




    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
