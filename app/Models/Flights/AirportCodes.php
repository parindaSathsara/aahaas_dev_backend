<?php

namespace App\Models\Flights;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AirportCodes extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_airport_codes';

    protected $fillable = [
        'code_id',
        'city_name',
        'country',
        'iata_code',
    ];

    
    public $timestamps = false;
}
