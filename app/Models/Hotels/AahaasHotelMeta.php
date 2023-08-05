<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class AahaasHotelMeta extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'aahaas_hotel_meta';

    protected $fillable = [
        'hotel_code',
        'hotel_name',
        'hotel_description',
        'country',
        'country_code',
        'coordinates',
        'category',
        'boards',
        'address',
        'postal_code',
        'city',
        'email',
        'phone_booking',
        'phone_management',
        'phone_fax',
        'phone_hotel',
        'rooms',
        'terminals',
        'interest_points',
        'wild_cards',
        'last_update',
        'web',
        'class',
        'ranking',
        'trip_advisor',
        'facilities',
        'images',
        'provider',
        'rating',
        'trip_advisor_rating',
        'hotel_level',
        'micro_location',
        'driver_acc',
        'lift_status',
        'vehicle_approach',
        'account_status'
    ];

    public $timestamps = false;

    //create aahaas hotel dataset
    public function pushAahaasHotelMeta($dataset)
    {

        try {

            AahaasHotelMeta::create([
                'hotel_code' => $dataset->HotelIDHOTEL,
                'hotel_name' => $dataset->hotel_name,
                'hotel_description' => $dataset->hotel_description,
                'country' => $dataset->country,
                'country_code' => '-',
                'coordinates' => $dataset->latitude . ',' . $dataset->longtitude,
                'category' => $dataset->CategoryType,
                'boards' => $dataset->meal_plan,
                'address' => $dataset->hotel_address,
                'postal_code' => '-',
                'city' => $dataset->city,
                'email' => '-',
                'phone_booking' => '-',
                'phone_management' => '-',
                'phone_fax' => '-',
                'phone_hotel' => '-',
                'rooms' => $dataset->room_type,
                'terminals' => '-',
                'interest_points' => '-',
                'wild_cards' => '-',
                'last_update' => '-',
                'web' => '-',
                'class' => $dataset->hotel_level,
                'ranking' => '-',
                'trip_advisor' => $dataset->trip_advisor_link,
                'facilities' => '-',
                'images' => $dataset->hotel_image,
                'provider' => 'aahaas',
                'rating' => '-',
                'trip_advisor_rating' => '-',
                'hotel_level' => $dataset->hotel_level,
                'micro_location' => $dataset->micro_location,
                'driver_acc' => '-',
                'lift_status' => '-',
                'vehicle_approach' => '-',
                'account_status' => '-'
            ]);

            return response([
                'status' => 200,
                'data_response' => 'Data Insereted'
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //update meta row
    public function updateAahaasHotels($dataset)
    {
        try {

            DB::table('aahaas_hotel_meta')->where('hotel_code', $dataset->HotelIDHOTEL)->update([
                'hotel_code' => $dataset->HotelIDHOTEL,
                'hotel_name' => $dataset->hotel_name,
                'hotel_description' => $dataset->hotel_description,
                'country' => $dataset->country,
                'country_code' => '-',
                'coordinates' => $dataset->latitude . ',' . $dataset->longtitude,
                'category' => $dataset->CategoryType,
                'boards' => $dataset->meal_plan,
                'address' => $dataset->hotel_address,
                'postal_code' => '-',
                'city' => $dataset->city,
                'email' => '-',
                'phone_booking' => '-',
                'phone_management' => '-',
                'phone_fax' => '-',
                'phone_hotel' => '-',
                'rooms' => $dataset->room_type,
                'terminals' => '-',
                'interest_points' => '-',
                'wild_cards' => '-',
                'last_update' => '-',
                'web' => '-',
                'class' => $dataset->hotel_level,
                'ranking' => '-',
                'trip_advisor' => $dataset->trip_advisor_link,
                'facilities' => '-',
                'images' => $dataset->hotel_image,
                'provider' => 'aahaas',
                'rating' => '-',
                'trip_advisor_rating' => '-',
                'hotel_level' => $dataset->hotel_level,
                'micro_location' => $dataset->micro_location,
                'driver_acc' => '-',
                'lift_status' => '-',
                'vehicle_approach' => '-',
                'account_status' => '-'
            ]);

            return response([
                'status' => 200,
                'data_response' => 'Data Updated'
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
