<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Uuids;
use App\Models\UserAddress;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'type',
        'email',
        'password',
        'phone',
        'organization_name',
        'ratings',
        'address1',
        'address2',
        'city',
        'state',
        'country',
        'pincode'
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

    /* Relations */
    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function cartIteam()
    {
        return $this->hasMany(CartItem::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function userAddress()
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    /*get full name of user*/
    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }
}
