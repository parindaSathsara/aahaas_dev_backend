<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HotelsPreBookings extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotels_pre_booking';

    protected $fillable = [
        'rate_key',
        'hotel_name',
        'holderFirstName',
        'holderLastName',
        'hotelRoomTypes',
        'roomId',
        'type',
        'name',
        'surname',
        'remarks',
        'cartStatus',
        'userID',
        'cart_image',
        'checkingDate',
        'checkoutDate',
        'paxCount',
        'noOfAdults',
        'noOfChilds',
        'cusTitle',
        'totalFare',
        'provider',
        'ref_id',
        'booking_total'
    ];

    public $timestamps = false;
}
