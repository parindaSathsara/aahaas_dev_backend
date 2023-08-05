<?php

namespace App\Models\Lifestyle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class LifeStyleDiscounts extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_lifestyle_discount';

    protected $fillable = [
        'discount_id',
        'lifestyle_id',
        'lifestyle_inventory_id',
        'discount_limit',
        'offered_product',
        'direct',
        'value',
        'inventory_limit',
        'sale_start_date',
        'sale_end_date',
        'user_level',
        'created_at',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;
}
