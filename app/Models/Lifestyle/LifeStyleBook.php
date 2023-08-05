<?php

namespace App\Models\Lifestyle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class LifeStyleBook extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_lifestyle_bookings';

    protected $fillable = [
        'lifestyle_id',
        'lifestyle_inventory_id',
        'lifestyle_rate_id',
        'lifestyle_discount_id',
        'lifestyle_children_details',
        'lifestyle_children_ages',
        'lifestyle_adult_details',
        'lifestyle_children_count',
        'lifestyle_adult_count',
        'booking_date',
        'booking_status',
        'user_id',
        'lifestyle_booking_id'
    ];

    public $timestamps = false;
}
