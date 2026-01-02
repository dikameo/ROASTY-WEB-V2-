<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Profile extends Model
{
    use HasUuids;

    /**
     * Standard Laravel profiles table with UUID ID
     * Foreign key: user_id references users(id)
     */
    protected $table = 'profiles';
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'role',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

