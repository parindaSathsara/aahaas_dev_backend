<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HotelTravellerDetail extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_travellerdetails';

    protected $fillable = [
        'resevation_no',
        'first_name',
        'last_name',
        'type'
    ];

    public $timestamps = false;

    //create a new hotel traveller details
    public function createNewHotelTravellerDetail($bookingRef, $fName, $sName, $type)
    {
        try {
            HotelTravellerDetail::create([
                'resevation_no' => $bookingRef,
                'first_name' => $fName,
                'last_name' => $sName,
                'type' => $type
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
