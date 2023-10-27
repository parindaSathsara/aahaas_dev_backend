<?php

namespace App\Models\Hotel\HotelMeta;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class SmithWatermanGotoh
{
    private $gapValue;
    private $substitution;

    /**
     * Constructs a new Smith Waterman metric.
     *
     * @param gapValue
     *            a non-positive gap penalty
     * @param substitution
     *            a substitution function
     */
    public function __construct(
        $gapValue = -0.5,
        $substitution = null
    ) {
        if ($gapValue > 0.0) throw new Exception("gapValue must be <= 0");
        //if(empty($substitution)) throw new Exception("substitution is required");
        if (empty($substitution)) $this->substitution = new SmithWatermanMatchMismatch(1.0, -2.0);
        else $this->substitution = $substitution;
        $this->gapValue = $gapValue;
    }

    public function compare($a, $b)
    {
        if (empty($a) && empty($b)) {
            return 1.0;
        }

        if (empty($a) || empty($b)) {
            return 0.0;
        }

        $maxDistance = min(mb_strlen($a), mb_strlen($b))
            * max($this->substitution->max(), $this->gapValue);
        return $this->smithWatermanGotoh($a, $b) / $maxDistance;
    }

    private function smithWatermanGotoh($s, $t)
    {
        $v0 = [];
        $v1 = [];
        $t_len = mb_strlen($t);
        $max = $v0[0] = max(0, $this->gapValue, $this->substitution->compare($s, 0, $t, 0));

        for ($j = 1; $j < $t_len; $j++) {
            $v0[$j] = max(
                0,
                $v0[$j - 1] + $this->gapValue,
                $this->substitution->compare($s, 0, $t, $j)
            );

            $max = max($max, $v0[$j]);
        }

        // Find max
        for ($i = 1; $i < mb_strlen($s); $i++) {
            $v1[0] = max(0, $v0[0] + $this->gapValue, $this->substitution->compare($s, $i, $t, 0));

            $max = max($max, $v1[0]);

            for ($j = 1; $j < $t_len; $j++) {
                $v1[$j] = max(
                    0,
                    $v0[$j] + $this->gapValue,
                    $v1[$j - 1] + $this->gapValue,
                    $v0[$j - 1] + $this->substitution->compare($s, $i, $t, $j)
                );

                $max = max($max, $v1[$j]);
            }

            for ($j = 0; $j < $t_len; $j++) {
                $v0[$j] = $v1[$j];
            }
        }

        return $max;
    }
}

class SmithWatermanMatchMismatch
{
    private $matchValue;
    private $mismatchValue;

    /**
     * Constructs a new match-mismatch substitution function. When two
     * characters are equal a score of <code>matchValue</code> is assigned. In
     * case of a mismatch a score of <code>mismatchValue</code>. The
     * <code>matchValue</code> must be strictly greater then
     * <code>mismatchValue</code>
     *
     * @param matchValue
     *            value when characters are equal
     * @param mismatchValue
     *            value when characters are not equal
     */
    public function __construct($matchValue, $mismatchValue)
    {
        if ($matchValue <= $mismatchValue) throw new Exception("matchValue must be > matchValue");

        $this->matchValue = $matchValue;
        $this->mismatchValue = $mismatchValue;
    }

    public function compare($a, $aIndex, $b, $bIndex)
    {
        return ($a[$aIndex] === $b[$bIndex] ? $this->matchValue
            : $this->mismatchValue);
    }

    public function max()
    {
        return $this->matchValue;
    }

    public function min()
    {
        return $this->mismatchValue;
    }
}



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
        $earthRadius = 6371000; //km * 1000
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $val = (pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2));
        $res = 2 * asin(sqrt($val));

        // $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        //     cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($res * $earthRadius, 2);
    }

    //sql query
    public function sqlMethod($data)
    {
        // $query = DB::table('aahaas_hotel_meta')->whereFullText('hotelName', $data)->get();

        $q =  preg_match_all('([A-Za-z])', 'Hotel Colombo', $data);

        return $q;
    }


    public function gethotelDistance($start, $end)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?destinations=" . $start . "&origins=" . $end . "&key=AIzaSyB3x9uy0MRBuz4McmPvm-tRCjvq8VgFKOg";
        $response = Http::withHeaders($this->getHeader())->get($url)->json();
        return $response['rows'][0]['elements'][0]['distance']['value'];
    }

    //HotelBeds Data Feeding Route
    public function createHotelDetailsBeds()
    {


        try {

            // return $mainArray[] = $this->sqlMethod('Berjaya Hotel Colombo');

            ini_set('max_execution_time', 1000);

            $aahaasMetaOrigin = DB::table('aahaas_hotel_meta')->select('*')->limit(20)->get();

            $url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?';

            $Details['fields'] = 'code,name,coordinates';
            $Details['countryCode'] = 'LK';
            $Details['language'] = 'ENG';
            $Details['from'] = '1';
            $Details['to'] = '20';
            $Details['useSecondaryLanguage'] = 'false';

            $url .= http_build_query($Details);

            $response = Http::withHeaders($this->getHeader())->get($url)->json();



            $mainArray = array();
            $array = array();

            $obj = new SmithWatermanGotoh();



            // return $response;

            if (count($response['hotels']) != 0) {

                foreach ($response['hotels'] as $hotel) {


                    if (count($aahaasMetaOrigin) > 0) {

                        foreach ($aahaasMetaOrigin as $row) {

                            $ahsHotel = preg_replace('/\s+/', '', str_replace($row->hotelName, '', 'Hotel'));
                            $bedHotel = preg_replace('/\s+/', '', str_replace($hotel['name']['content'], '', 'Hotel'));
                            // // if (str_contains($hotel['name']['content'], 'Hotel')) {

                            // similar_text(preg_replace('/\s+/', '', strtolower($bedHotel)), preg_replace('/\s+/', '', strtolower($ahsHotel)), $percent);

                            // similar_text((string)round((float)$hotel['coordinates']['latitude'], 3) . "," . (string)round((float)$hotel['coordinates']['longitude'], 3), (string)round((float)$row->latitude, 3) . "," . (string)round((float)$row->longitude, 3), $percentDistance);

                            // $hotelDistance = $this->getDistance((string)round((float)$hotel['coordinates']['latitude'], 3), (string)round((float)$row->longitude, 3), (string)round((float)$row->latitude, 3), (string)round((float)$hotel['coordinates']['longitude'], 3));

                            $latlonStart = $hotel['coordinates']['latitude'] . "," . $hotel['coordinates']['longitude'];
                            $latlonEnd = $row->latitude . "," . $row->longitude;
                            // return $this->gethotelDistance($latlonStart, $latlonEnd);

                            $hotelDistance = $this->gethotelDistance($latlonStart, $latlonEnd);

                            if ($hotelDistance < 200) {
                                if ($obj->compare($ahsHotel, $bedHotel) >= 0.8) {
                                    // $array["percent"] = $obj->compare($ahsHotel, $bedHotel);
                                    $array["nameOrigin"] = $row->hotelName;
                                    $array["nameBeds"] = $hotel['name']['content'];
                                    $array["distance"] = $hotelDistance;

                                    $array["latLon"] = $latlonStart . "|" . $latlonEnd;

                                    $mainArray['Mapped'][] = $array;
                                }
                            }

                            // else {

                            //     if ($distance < 500 && $distance > 100) {

                            //         $array["percent"] = $obj->compare($ahsHotel, $bedHotel);
                            //         $array["nameOrigin"] = $row->hotelName;
                            //         $array["nameBeds_not"] = $hotel['name']['content'];

                            //         $mainArray['NotMapped'][] = $array;
                            //     }
                            // }

                            // // if ($distance > 95) {

                            // //     $array['code'] = $hotel['code'];
                            // //     $array['ahs_Name'] = preg_replace('/\s+/', '', strtolower($replaceStringTwo));
                            // //     $array['name'] = preg_replace('/\s+/', '', strtolower($replaceString));
                            // //     $array['latlon'] =  (string)round((float)$hotel['coordinates']['latitude'], 3) . "," . (string)round((float)$hotel['coordinates']['longitude'], 3);
                            // //     $array['latlonOrg'] =  (string)round((float)$row->latitude, 3) . "," . (string)round((float)$row->longitude, 3);
                            // //     $array['percentHotel'] = $percentDistance;

                            // //     $mainArray[] = $array;
                            // // }
                            // }
                            // else {

                            //     // $replaceString = str_replace('Hotel', '', $hotel['name']['content']);
                            //     // $replaceStringTwo = str_replace('Hotel', '', $row->hotelName);
                            //     similar_text(preg_replace('/\s+/', '', strtolower($hotel['name']['content'])), preg_replace('/\s+/', '', strtolower($row->hotelName)), $percent);

                            //     similar_text((string)round((float)$hotel['coordinates']['latitude'], 4) . "," . (string)round((float)$hotel['coordinates']['longitude'], 4), $row->latitude . "," . $row->longitude, $percentDistance);
                            //     if ($percent > 85) {
                            //         $array['code'] = $hotel['code'];
                            //         $array['ahs_Name'] = preg_replace('/\s+/', '', strtolower($row->hotelName));
                            //         $array['name'] = preg_replace('/\s+/', '', strtolower($hotel['name']['content']));
                            //         $array['latlon'] =  (string)round((float)$hotel['coordinates']['latitude'], 4) . "," . (string)round((float)$hotel['coordinates']['longitude'], 4);
                            //         $array['latlonOrg'] =  $row->latitude . "," . $row->longitude;
                            //         $array['percent'] = $percent;

                            //         $mainArray[] = $array;
                            //     }
                            // }
                        }
                    }
                }

                return $mainArray;

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
