<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens, HasUuids;

    /**
     * Standard Laravel users table
     * Using string keyType for compatibility with polymorphic varchar columns
     * (model_has_roles.model_id, model_has_permissions.model_id, personal_access_tokens.tokenable_id)
     */
    protected $table = 'users';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the columns that should be treated as incrementing IDs
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    /**
     * Guard name for spatie/laravel-permission
     */
    protected $guard_name = 'sanctum';

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function createdProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->profile?->role === 'admin' || $this->hasRole('admin');
    }
}


