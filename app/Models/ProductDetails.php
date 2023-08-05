<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use function PHPSTORM_META\map;

class ProductDetails extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_product_details';

    protected $fillable = [
        'product_detail_id',
        'listing_id',
        'category1',
        'category2',
        'category3',
        'search_tags',
        'group_tags',
        'priority',
        'delivery_type',
        'payment_options'
    ];

    public $timestamps = false;

}
