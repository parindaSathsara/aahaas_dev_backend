<?php

namespace App\Models\Sabre;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class FlightResevation extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_flight_resevation';

    protected $fillable = [
        'booking_ref',
        'confirm_ref',
        'reservation_name',
        'contact_info',
        'flight_code',
        'flight_no',
        'flight_title',
        'departure_fromCode',
        'departure_fromTitle',
        'arrival_toCode',
        'arrival_toTitle',
        'departure_time',
        'arrival_time',
        'total_duration',
        'dep_terminal',
        'arr_terminal',
        'baggage_details',
        'flight_class',
        'booking_status',
        'user_id',
        'created_at',
        'hidden_stopCode',
        'hidden_stopTitle',
        'hidden_stopDeparture',
        'hidden_stopArrival',
        'order_id'
    ];

    public $timestamps = false;
}
