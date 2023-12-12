<?php

namespace App\Models\HotelsMeta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelPreBooking extends Model
{
    use HasFactory;

    //
    protected $table = 'hotel_prebooking';

    // public $timestamps = false;
    protected $fillable = [
        'prebooking_id',
        'hotel_id',
        'bookingdataset',
        'provider',
        'user_id',
        'status',
    ];
}
