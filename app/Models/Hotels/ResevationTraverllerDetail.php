<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ResevationTraverllerDetail extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_travellerdetails';

    protected $fillable = [
        'resevation_no',
        'first_name',
        'last_name',
        'type',
    ];

    public $timestamps = false;
}
