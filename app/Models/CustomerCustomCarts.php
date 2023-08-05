<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CustomerCustomCarts extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_customer_carts';

    protected $fillable = [
        'customer_cart_id',
        'main_category_id',
        'cart_id',
        'listing_pre_id',
        'lifestyle_pre_id',
        'hotels_pre_id',
        'cart_status',
        'cart_added_date',
        'education_pre_id',
        'customer_id',
        'order_preffered_date'
    ];

    public $timestamps = false;
}
