<?php

namespace App\Models\Hotel\HotelMeta;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class HotelMeta extends Model
{
    public $api_key;
    public $secret_key;
    public $rad = 0.5;

    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'aahaas_hotel_meta';

    public $timestamps = false;

    protected $fillable = [
        'hotelCode',
        'ahs_HotelId',
        'hotelName',
        'hotelDescription',
        'country',
        'countryCode',
        'latitude',
        'longitude',
        'catgory',
        'boards',
        'address',
        'postalCode',
        'city',
        'email',
        'web',
        'class',
        'tripAdvisor',
        'facilities',
        'images',
        'rating',
        'provider',
        'microLocation',
        'driverAcc',
        'liftStatus',
        'vehicleApproach',
        'accountStatus',
    ];

    public function __construct()
    {
        // set_time_limit(0);
        $this->api_key = config('services.hotelbed.key');
        $this->secret_key = config('services.hotelbed.secret');
    }

    /***** Generating X-Signature AND API Headers code for API call *****/
    function getSignature()
    {
        $APIKEY = $this->api_key;
        $SECRETKEY = $this->secret_key;
        $current_timestamp = Carbon::now()->timestamp;
        $signature = $APIKEY . $SECRETKEY . $current_timestamp;

        $x_sig = hash('sha256', $signature, true);

        $test_key = bin2hex($x_sig);

        return $test_key;
    }

    function getHeader()
    {
        $Header = [];

        $Header['Accept'] = 'application/json';
        $Header['Api-key'] = $this->api_key;
        $Header['X-Signature'] = $this->getSignature();
        $Header['Content-Type'] = 'application/json';

        return $Header;
    }


    public function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $earthRadius = 6371;
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    //sql query
    public function sqlMethod($data)
    {
        // $query = DB::table('aahaas_hotel_meta')->whereFullText('hotelName', $data)->get();

        $q =  preg_match_all('([A-Za-z])', 'Hotel Colombo', $data);

        return $q;
    }

    //HotelBeds Data Feeding Route
    public function createHotelDetailsBeds()
    {
        try {

            // return $mainArray[] = $this->sqlMethod('Berjaya Hotel Colombo');

            ini_set('max_execution_time', 360);

            $aahaasMetaOrigin = DB::table('aahaas_hotel_meta')->select('*')->get();

            $url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?';

            $Details['fields'] = 'all';
            $Details['countryCode'] = 'LK';
            $Details['language'] = 'ENG';
            $Details['from'] = '1';
            $Details['to'] = '320';
            $Details['useSecondaryLanguage'] = 'false';

            $url .= http_build_query($Details);

            $response = Http::withHeaders($this->getHeader())->get($url)->json();

            // return $response;

            $mainArray = array();
            $array = array();

            if (count($response['hotels']) != 0) {

                foreach ($response['hotels'] as $hotel) {

                    $mainArray[] = $this->sqlMethod($hotel['name']['content']);

                    if (count($aahaasMetaOrigin) > 0) {

                        foreach ($aahaasMetaOrigin as $row) {

                            similar_text(strtolower($hotel['name']['content']), strtolower($row->hotelName), $percent);

                            similar_text((string)round((float)$hotel['coordinates']['latitude'], 4) . "," . (string)round((float)$hotel['coordinates']['longitude'], 4), $row->latitude . "," . $row->longitude, $percentDistance);

                            if ($this->getDistance($row->latitude, $row->longitude, $hotel['coordinates']['latitude'], $hotel['coordinates']['longitude']) < 0.5) {

                                // $array['origin'] = $row;
                                $array['code'] = $hotel['code'];
                                $array['ahs_Name'] = $row->hotelName;
                                $array['name'] = $hotel['name']['content'];
                                $array['latlon'] =  (string)round((float)$hotel['coordinates']['latitude'], 4) . "," . (string)round((float)$hotel['coordinates']['longitude'], 4);
                                $array['latlonOrg'] =  $row->latitude . "," . $row->longitude;
                                $array['percent'] = $percentDistance;

                                $mainArray[] = $array;
                            }
                        }
                    }
                    // $count = DB::table('aahaas_hotel_meta')->where(['hotelCode' => $hotel['code'], 'provider' => 'hotelBeds'])->count();

                    // if ($count > 0) {
                    //     DB::table('aahaas_hotel_meta')
                    //         ->where(['hotelCode' => $hotel['code'], 'provider' => 'hotelBeds'])
                    //         ->update([
                    //             'hotelName' => $hotel['name']['content'],
                    //             'hotelDescription' => $hotel['description']['content'],
                    //             'country' => 'Sri Lanka',
                    //             'countryCode' => $hotel['countryCode'],
                    //             'latitude' => round((float)$hotel['coordinates']['latitude'], 4), //number_format((float)$hotel['coordinates']['latitude'], 5, '.', '')
                    //             'longitude' => round((float)$hotel['coordinates']['longitude'], 4), //number_format((float)$hotel['coordinates']['longitude'], 5, '.', '')
                    //             'category' => $hotel['categoryCode'],
                    //             'boards' => array_key_exists('boardCodes', $response['hotels']) ? implode(',', $hotel['boardCodes']) : null,
                    //             'address' => $hotel['address']['content'],
                    //             'postalCode' => array_key_exists('postalCode', $response['hotels']) ? $hotel['postalCode'] : null,
                    //             'city' => $hotel['city']['content'],
                    //             'email' => $hotel['email'],
                    //             'web' => $hotel['web'],
                    //             'class' => array_key_exists('S2C', $response['hotels']) ? $hotel['S2C'] : null,
                    //             'tripAdvisor' => null,
                    //             'facilities' => null,
                    //             'images' => $hotel['images'][0]['path'],
                    //             'rating' => null,
                    //             'provider' => 'hotelBeds',
                    //             'microLocation' => null,
                    //             'driverAcc' => null,
                    //             'liftStatus' => null,
                    //             'vehicleApproach' => null,
                    //             'accountStatus' => null,
                    //         ]);

                    //     // return response(['status' => 200, 'message' => 'updated']);
                    // } else {

                    //     HotelMeta::create([
                    //         'hotelCode' => $hotel['code'],
                    //         'hotelName' => $hotel['name']['content'],
                    //         'hotelDescription' => $hotel['description']['content'],
                    //         'country' => 'Sri Lanka',
                    //         'countryCode' => $hotel['countryCode'],
                    //         'latitude' => round((float)$hotel['coordinates']['latitude'], 4),
                    //         'longitude' => round((float)$hotel['coordinates']['longitude'], 4),
                    //         'category' => $hotel['categoryCode'],
                    //         'boards' => array_key_exists('boardCodes', $response['hotels']) ? implode(',', $hotel['boardCodes']) : null,
                    //         'address' => $hotel['address']['content'],
                    //         'postalCode' => array_key_exists('postalCode', $response['hotels']) ? $hotel['postalCode'] : null,
                    //         'city' => $hotel['city']['content'],
                    //         'email' => $hotel['email'],
                    //         'web' => $hotel['web'],
                    //         'class' => array_key_exists('S2C', $response['hotels']) ? $hotel['S2C'] : null,
                    //         'tripAdvisor' => null,
                    //         'facilities' => null,
                    //         'images' => $hotel['images'][0]['path'],
                    //         'rating' => null,
                    //         'provider' => 'hotelBeds',
                    //         'microLocation' => null,
                    //         'driverAcc' => null,
                    //         'liftStatus' => null,
                    //         'vehicleApproach' => null,
                    //         'accountStatus' => null,
                    //     ]);
                    // }
                }

                return  $mainArray;

                foreach ($mainArray as $key) {
                    // return $key['origin'];
                    DB::table('aahaas_hotel_meta')
                        ->where(['ahs_HotelId' => $key['origin']->ahs_HotelId, 'provider' => 'hotelAhs'])
                        ->update([
                            'hotelCode' => $key['code'],
                        ]);
                }

                return response(['status' => 200, 'message' => 'created']);
            } else {
                return response(['status' => 400]);
            }

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    //HotelBeds Data Feeding Route
    public function createHotelDetailsAhs()
    {
        try {

            $Query = DB::table('tbl_hotel')
                ->join('tbl_hotel_details', 'tbl_hotel.id', '=', 'tbl_hotel_details.hotel_id')
                ->join('tbl_submaincategory', 'tbl_hotel.category1', '=', 'tbl_submaincategory.id')
                // ->limit(3)
                ->select('*')->get();

            // return $Query;
            $array = array();

            if (count($Query) != 0) {

                foreach ($Query as $hotel) {

                    $count = DB::table('aahaas_hotel_meta')->where(['ahs_HotelId' => $hotel->hotel_id, 'provider' => 'hotelAhs'])->count();

                    if ($count > 0) {
                        DB::table('aahaas_hotel_meta')
                            ->where(['hotelCode' => $hotel->hotel_id, 'provider' => 'hotelAhs'])
                            ->update([
                                'hotelName' => $hotel->hotel_name,
                                'hotelDescription' => $hotel->hotel_description,
                                'country' => $hotel->country,
                                'countryCode' => 'LK',
                                'latitude' => round((float)$hotel->latitude, 4), //number_format((float)$hotel['coordinates']['latitude'], 5, '.', '')
                                'longitude' => round((float)$hotel->longtitude, 4), //number_format((float)$hotel['coordinates']['longitude'], 5, '.', '')
                                'category' => $hotel->submaincat_type,
                                'boards' => null,
                                'address' => $hotel->hotel_address,
                                'postalCode' => null,
                                'city' => $hotel->city,
                                'email' => null,
                                'web' => null,
                                'class' => $hotel->hotel_level,
                                'tripAdvisor' => $hotel->trip_advisor_link,
                                'facilities' => null,
                                'images' => explode(',', $hotel->hotel_image)[0],
                                'rating' => null,
                                'provider' => 'hotelAhs',
                                'microLocation' => $hotel->micro_location,
                                'driverAcc' => $hotel->driver_accomadation,
                                'liftStatus' => $hotel->lift_status,
                                'vehicleApproach' => $hotel->vehicle_approchable,
                                'accountStatus' => null,
                            ]);

                        // return response(['status' => 200, 'message' => 'updated']);
                    } else {

                        HotelMeta::create([
                            'hotelCode' => null,
                            'ahs_HotelId' => $hotel->hotel_id,
                            'hotelName' => $hotel->hotel_name,
                            'hotelDescription' => $hotel->hotel_description,
                            'country' => $hotel->country,
                            'countryCode' => 'LK',
                            'latitude' => round((float)$hotel->latitude, 4), //number_format((float)$hotel['coordinates']['latitude'], 5, '.', '')
                            'longitude' => round((float)$hotel->longtitude, 4), //number_format((float)$hotel['coordinates']['longitude'], 5, '.', '')
                            'category' => $hotel->submaincat_type,
                            'boards' => null,
                            'address' => $hotel->hotel_address,
                            'postalCode' => null,
                            'city' => $hotel->city,
                            'email' => null,
                            'web' => null,
                            'class' => $hotel->hotel_level,
                            'tripAdvisor' => $hotel->trip_advisor_link,
                            'facilities' => null,
                            'images' => explode(',', $hotel->hotel_image)[0],
                            'rating' => null,
                            'provider' => 'hotelAhs',
                            'microLocation' => $hotel->micro_location,
                            'driverAcc' => $hotel->driver_accomadation,
                            'liftStatus' => $hotel->lift_status,
                            'vehicleApproach' => $hotel->vehicle_approchable,
                            'accountStatus' => null,
                        ]);
                    }
                }
                return response(['status' => 200, 'message' => 'created']);
            } else {
                return response(['status' => 400]);
            }

            // return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
