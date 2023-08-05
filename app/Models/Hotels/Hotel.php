<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Hotel extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel';

    protected $fillable = [
        'hotel_name',
        'hotel_description',
        'hotel_level',
        'category1',
        'longtitude',
        'latitude',
        'provider',
        'hotel_address',
        'trip_advisor_link',
        'hotel_image',
        'country',
        'city',
        'micro_location',
        'hotel_status',
        'startdate',
        'enddate',
        'vendor_id',
        'updated_by'
    ];

    public $timestamps = false;

    public function createNewHotel($hotel_name, $hotel_des, $hotel_level, $hotel_cat, $long, $lat, $provider, $address, $trip_ad_link, $hotel_img, $country, $city, $micro_location, $status, $start_date, $end_date, $vendor)
    {
        try {

            Hotel::create([
                'hotel_name' => $hotel_name,
                'hotel_description' => $hotel_des,
                'hotel_level' => $hotel_level,
                'category1' => $hotel_cat,
                'longtitude' => $long,
                'latitude' => $lat,
                'provider' => $provider,
                'hotel_address' => $address,
                'trip_advisor_link' => $trip_ad_link,
                'hotel_image' => implode('|', $hotel_img),
                'country' => $country,
                'city' => $city,
                'micro_location' => $micro_location,
                'hotel_status' => $status,
                'startdate' => $start_date,
                'enddate' => $end_date,
                'vendor_id' => $vendor,
                'updated_by' => "user",
            ]);

            return response([
                'status' => 200,
                'response' => 'Hotel created success'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
