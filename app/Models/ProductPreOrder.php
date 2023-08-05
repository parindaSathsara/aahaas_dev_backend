<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ProductPreOrder extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_essentials_preorder';

    protected $fillable = [
        'essential_pre_order_id',
        'essential_listing_id',
        'essential_inventory_id',
        'customer_id',
        'cart_id',
        'rate_id',
        'address',
        'city',
        'preffered_date',
        'quantity',
        'status',
        'updated_status',
        'addressType',
        'deliveryRateID',
        'deliveryRate'
    ];

    public $timestamps = false;
}
