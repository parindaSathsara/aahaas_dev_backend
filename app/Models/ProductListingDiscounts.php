<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ProductListingDiscounts extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_listing_discount';

    protected $fillable = [
        'discount_id',
        'listing_id',
        'inventory_id',
        'discount_type_id',
        'discount_min_order_qty',
        'discount_max_order_qty',
        'discount_amount',
        'discount_percentage',
        'offer_product',
        'offer_product_inventory',
        'offer_product_qty',
        'discount_blackout_dates',
        'discount_blackout_days',
        'offer_product_title'
    ];

    public $timestamps = false;
}
