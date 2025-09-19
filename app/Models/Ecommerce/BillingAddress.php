<?php

namespace App\Models\Ecommerce;

use App\Models\Ecommerce\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingAddress extends Model
{
    protected $fillable = [
        'bil_city',
        'order_id',
        'bil_barangay',
        'bil_country',
        'bil_province',
        'bil_complete_address',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}
