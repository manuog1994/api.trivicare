<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;


class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'orders_count',
        'newsletter',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

   

    // Relacion uno a muchos con Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Relacion uno a muchos con Review
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relacion uno a muchos con UserProfile
    public function user_profile()
    {
        return $this->hasMany(UserProfile::class);
    }

    // Relacion muchos a muchos con tags
    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag');
    }
}

