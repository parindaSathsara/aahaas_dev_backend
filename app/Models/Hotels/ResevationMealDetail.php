<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ResevationMealDetail extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_mealdetail';

    protected $fillable = [
        'resevation_no',
        'meal_plan',
        'date',
        'adult_count',
        'child_count',
        'special_request',
        'unit_price'
    ];

    public $timestamps = false;
}
