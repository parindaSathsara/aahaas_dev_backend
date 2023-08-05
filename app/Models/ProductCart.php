<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ProductCart extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tble_cart';

    protected $fillable = [
        'prod_title',
        'cat_type_id',
        'delivery_date',
        'quantity',
        'unit_price',
        'child_count',
        'adult_count',
        'user_id',
        'listing_id'
    ];

    public $timestamps = false;
}
