<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'Users';

    protected $fillable = [
        'fullname',
        'avatar',
        'username',
        'password',
        'email',
        'phone',
        'security_code',
        'google_id',
        'admin_role',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

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
            'admin_role' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attributes['fullname'] ?? null,
            set: fn (?string $value) => ['fullname' => $value],
        );
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
