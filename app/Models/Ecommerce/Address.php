<?php

namespace App\Models\Ecommerce;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'country',
        'street',
        'city',
        //'state',
        'zip',
        'address_type',
        'user_id',
        'province',
        'city',
        'barangay',
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function userAddress():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
