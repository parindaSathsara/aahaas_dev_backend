<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HotelRoomDetails extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_roomdetails';

    protected $fillable = [
        'resevation_no',
        'room_code',
        'adult_count',
        'child_count',
        'adult_rate',
        'child_withbed_rate',
        'child_nobed_rate'
    ];

    public $timestamps = false;


    //create a new hotel room details
    public function createNewHotelRoomDetail($bookingRef, $room_code, $noOfAD, $noOfCH)
    {
        try {
            HotelRoomDetails::create([
                'resevation_no' => $bookingRef,
                'room_code' => $room_code,
                'adult_count' => $noOfAD,
                'child_count' => $noOfCH
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
