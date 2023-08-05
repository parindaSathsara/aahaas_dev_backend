<?php

namespace App\Models\Sabre;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class FlightPayment extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_flight_payment';

    protected $fillable = [
        'booking_ref',
        'confirmation_ref',
        'payment_amount',
        'payment_status',
        'user_id',
        'created_at'
    ];

    public $timestamps = false;
}
