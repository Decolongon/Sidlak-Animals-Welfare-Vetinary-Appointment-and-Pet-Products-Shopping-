<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;

class FreeshippingCity extends Model
{
    protected $fillable = [
        'city_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'city_code' => 'array',
    ];
}
