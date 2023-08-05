<?php

namespace App\Models\Lifestyle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class LifeStyleRates extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_lifestyle_rates';

    protected $fillable = [
        'lifestyle_rate_id',
        'lifestyle_id',
        'lifestyle_inventory_id',
        'booking_start_date',
        'booking_end_date',
        'travel_start_date',
        'travel_end_date',
        'attraction_category',
        'meal_plan' ,
        'market' ,
        'currency',
        'adult_rate',
        'child_rate',
        'student_rate',
        'senior_rate',
        'military_rate',
        'other_rate',
        'child_foc_age',
        'child_age',
        'adult_age',
        'cwb_age',
        'cnb_age',
        'payment_policy' ,
        'book_by_days' ,
        'cancellation_days' ,
        'cancel_policy' ,
        'stop_sales_Dates' ,
        'blackout_days' ,
        'blackout_dates' ,
        'created_at' ,
        'updated_at'  ,
        'updated_by',
    ];

    public $timestamps = false;
}
