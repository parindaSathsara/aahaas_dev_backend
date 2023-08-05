<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HotelDetail extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_details';

    protected $fillable = [
        'hotel_id',
        'driver_accomadation',
        'lift_status',
        'vehicle_approchable',
        'ac_status',
        'covid_safe',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public $timestamps = false;
}
