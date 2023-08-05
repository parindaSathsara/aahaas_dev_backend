<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ListingInventory extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_listing_inventory';

    protected $fillable = [
        'variation_type1',
        'variation_type2',
        'variation_type3',
        'variation_type4',
        'variant_type1',
        'variant_type2',
        'variant_type3',
        'variant_type4',
        'variant_images',
        'listing_id',
        'created_at',
        'updated_at',
        'status'
    ];

    public $timestamps = false;
}
