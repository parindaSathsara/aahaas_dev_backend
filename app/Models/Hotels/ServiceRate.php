<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ServiceRate extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_service_rate';

    protected $fillable = [
        'hotel_id',
        'booking_start_date',
        'booking_end_date',
        'travel_start_date',
        'travel_end_date',
        'room_category',
        'room_type',
        'meal_plan',
        'market_nationality',
        'currency',
        'service_type',
        'supplement_type',
        'compulsory',
        'adult_rate',
        'child_rate',
        'package_px_count',
        'package_rate',
        'package_add_px_rate',
        'package_child_rate',
        'child_foc_age',
        'child_age',
        'adult_age',
        'special_rate',
        'payment_policy',
        'book_by_days',
        'cancellation_days',
        'cancellation_policy',
        'stop_sales_startdate',
        'stop_sales_enddate',
        'blackoutdates',
        'blackoutdays',
        'created_at',
        'updated_at',
        'updated_by'
    ];
    
    public $timestamps = false;

}
