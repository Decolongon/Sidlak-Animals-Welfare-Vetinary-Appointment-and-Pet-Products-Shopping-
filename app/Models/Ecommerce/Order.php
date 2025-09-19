<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use App\Models\Ecommerce\Address;
use App\Models\Ecommerce\OrderItem;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ecommerce\BillingAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
       // 'product_id',
        'total',
        'order_status',
        'payment_status',
        'shipping_address_id',
        'billing_address_id',
        'is_billing_same_as_shipping',
        'notes',
        'shipping_price',
        'shipping_method',
        'payment_intent_id',
        'order_num'

    ];
    
    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_billing_same_as_shipping' => 'boolean',
    ];

    protected function scopeOrderStatus(Builder $query, string $orderStatus): void
    {
        $query->where('order_status', $orderStatus);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // public function shippingAddress() : BelongsTo
    // {
    //     return $this->belongsTo(Address::class, 'shipping_address_id');
    // }

    public function billingAddress() : HasMany
    {
        return $this->hasMany(BillingAddress::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName()
    {
        return 'order_num';
    }   
}