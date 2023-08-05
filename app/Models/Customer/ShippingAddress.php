<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ShippingAddress extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'shipping_address';

    protected $fillable = [
        'contact_name',
        'mobile_number',
        'country',
        'latitude',
        'longtitude',
        'address_full',
        'user_id'
    ];

    public $timestamps = false;
}
