<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ProductListingRates extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_product_listing_rates';

    protected $fillable = [
        'rate_id',
        'inventory_id',
        'active_start_date',
        'active_end_date',
        'mrp',
        'qty',
        'selling_rate',
        'wholesale_rate',
        'purchase_price',
        'min_order_qty',
        'max_order_qty'
    ];

    public $timestamps = false;
}
