<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ProductListing extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_product_listing';

    protected $fillable = [
        'listing_title',
        'listing_description',
        'sub_description',
        'cash_onDelivery',
        'discount_status',
        'product_images',
        'lisiting_status',
        'seo_tags',
        'seller_id',
        'sku',
        'unit',
        'brand_id',
        'product_status',
        'cancellationDay',
        'created_at',
        'updated_at',
        'updated_by',
        'variationStatus',
        'terms_conditions',
        'refund_policy',
        'delivery_policy',
    ];

    public $timestamps = false;
}
