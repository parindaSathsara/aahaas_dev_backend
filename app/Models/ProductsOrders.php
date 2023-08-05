<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ProductsOrders extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_products_orders';

    protected $fillable = [
        'listing_id',
        'rate_id',
        'inventory_id',
        'discount_id',
        'payment_option_id',
        'customer_id',
        'order_number',
        'order_quantity',
        'unit_price',
        'total_price',
        'discount_amount',
        'ship_to',
        'address',
        'preffered_delivery_date',
        'message_to_seller',
        'order_date',
        'order_status',
        'addressType'
    ];

    public $timestamps = false;
}
