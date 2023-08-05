<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HotelDiscount extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_discount';

    protected $fillable = [
        'hotel_id',
        'discount_type',
        'room_category',
        'offered_product',
        'user_level',
        'discount_limit',
        'offer_type',
        'offer_value',
        'sale_start_date',
        'sale_end_date',
        'created_at',
        'updated_at',
        'updated_by'
    ];
    
    public $timestamps = false;
}
