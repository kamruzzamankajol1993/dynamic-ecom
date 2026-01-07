<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'old_customer_id',
        'reward_points',
        'user_id',
        'discount_in_percent',
        'slug',
        'source',
        'type',
        'name',
        'email',
        'phone',
        'secondary_phone',
        'password',
        'address',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    /**
 * Get all of the orders for the Customer.
 */
public function orders()
{
    return $this->hasMany(Order::class);
}

  /**
     * Get all of the reward point logs for the Customer.
     */
    public function rewardPointLogs()
    {
        return $this->hasMany(RewardPoint::class);
    }

    /**
     * Hash the password before saving.
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

     public function getAddressAttribute($value)
    {
        // If the 'address' column in the customers table has a value, return it first.
        if ($value) {
            return $value;
        }

        // Otherwise, try to find the default address from the related addresses table.
        $defaultAddress = $this->addresses()->where('is_default', true)->first();

        // If a default address is found, return its 'address' field.
        if ($defaultAddress) {
            return $defaultAddress->address;
        }
        
        // As a final fallback, return the first available address if no default is set.
        $firstAddress = $this->addresses()->first();
        if ($firstAddress) {
            return $firstAddress->address;
        }

        // If no address is found at all, return null.
        return null;
    }
}
