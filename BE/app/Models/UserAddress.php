<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    /**
     * Address model for storing user addresses
     */
    protected $table = 'user_addresses';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'address',
        'province',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'accuracy',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

