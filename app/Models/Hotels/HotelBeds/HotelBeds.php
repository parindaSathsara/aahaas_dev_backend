<?php

namespace App\Models\Hotels\HotelBeds;

use App\Models\Customer\MainCheckout;
use App\Models\Hotels\HotelResevation;
use App\Models\Hotels\HotelResevationPayment;
use App\Models\Hotels\HotelRoomDetails;
use App\Models\Hotels\HotelsPreBookings;
use App\Models\Hotels\HotelTravellerDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HotelBeds extends Model
{
    use HasFactory;

    public $api_key;
    public $secret_key;
    public $hotel_reservation;
    public $main_checkout;
    public $hotel_room_details;
    public $hotel_reservation_payment;
    public $hotel_traveller_details;

    public function __construct()
    {
        // set_time_limit(0);
        $this->api_key = config('services.hotelbed.key');
        $this->secret_key = config('services.hotelbed.secret');
        $this->hotel_reservation = new HotelResevation();
        $this->main_checkout = new MainCheckout();
        $this->hotel_room_details = new HotelRoomDetails();
        $this->hotel_reservation_payment = new HotelResevationPayment();
        $this->hotel_traveller_details = new HotelTravellerDetail();
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
    /***** Generating X-Signature AND API Headers code for API call END *****/

    /***** Checking Hotel beds API hotel Availability *****/
    public function checkAvailabilityHotelBeds($checkingdate, $checkoutdate, $noofrooms, $noofadults, $noofchild, $childages, $hotelcode)
    {
        try {
            $MainArray = [];

            $DateTime = Carbon::now()->toDateTimeString();

            $MainArray['stay']['checkIn'] = $checkingdate;
            $MainArray['stay']['checkOut'] = $checkoutdate;


            $MainArray['occupancies']['rooms'] = $noofrooms;
            $NoOfPax = [];
            $MainArray['occupancies']['adults'] = $noofadults;
            $MainArray['occupancies']['children'] = $noofchild;

            $AgeofChild = explode(',', $childages);

            if ($MainArray['occupancies']['children'] > 0) {
                for ($cc = 0; $cc < $MainArray['occupancies']['children']; $cc++) {
                    // for($x; $x < )
                    $MainArray['occupancies']['paxes'][] = ['type' => 'CH', 'age' => $AgeofChild[$cc]];
                }
            }

            $ReqHotelCode = (int)$hotelcode;

            $MainArray['hotels']['hotel'] = [$ReqHotelCode];

            $sub_array = [];

            $sub_array['stay'] = $MainArray['stay'];
            $sub_array['occupancies'] = [$MainArray['occupancies']];
            $sub_array['hotels'] = $MainArray['hotels'];

            $response = Http::withHeaders($this->getHeader())
                ->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels', $sub_array)->json();


            if ($response['hotels']['total'] == 0) {
                return response([
                    'status' => 404,
                    'data' => 'No Availability'
                ]);
            } else {

                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/HotelBedsAPI_Logs.log')
                ])->info('executing checking availability for:' . Auth::user() . $DateTime, $response);

                return $response;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /***** Checking Hotel beds API hotel Availability END *****/

    /***** Getting Hotels with Minimum Price *****/
    public function getHotelBedsMinPriceHotels()
    {
        try {

            ini_set('max_execution_time', 360);
            $todayDate = Carbon::now()->format('Y-m-d');
            $_30DaysAfterDate = date('Y-m-d', strtotime('+1 days', strtotime($todayDate)));

            // $URL_1 = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?fields=all&language=ENG&from=1&to=40&useSecondaryLanguage=false';

            $URL_1 = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?fields=hotelCodes,images&language=ENG&from=1&to=50&useSecondaryLanguage=false';

            $URL_2 = 'https://api.test.hotelbeds.com/hotel-api/1.0/hotels';


            $response_1 = Http::withHeaders($this->getHeader())->get($URL_1)->json();

            // return $response_1;

            $hotel_Code = $response_1['hotels'];

            $hotel_Codes = array();
            $hotel_Images = array();



            foreach ($hotel_Code as $code) {
                $hotel_Codes[] =  $code['code'];
                $hotel_Images[] = ['code' => $code['code'], 'image' => $code['images'][0]['path']];
            }

            // return $hotel_Images;

            // return $hotel_Codes;

            $MainArray = [];

            $dateNow = Carbon::now()->toDateTimeString();

            $checkInDate = $todayDate;
            $checkOutDate = $_30DaysAfterDate;

            $MainArray['stay']['checkIn'] = $checkInDate;
            $MainArray['stay']['checkOut'] = $checkOutDate;


            $MainArray['occupancies']['rooms'] = '1';
            $NoOfPax = [];
            $MainArray['occupancies']['adults'] = '2';
            $MainArray['occupancies']['children'] = '0';


            $MainArray['hotels']['hotel'] = $hotel_Codes; //[1533, 1803, 2587, 3219];

            $sub_array = [];

            $sub_array['stay'] = $MainArray['stay'];
            $sub_array['occupancies'] = [$MainArray['occupancies']];
            $sub_array['hotels'] = $MainArray['hotels'];

            // return $sub_array;


            Cache::put('hotelcache', $sub_array);
            // return $this->fetchMainHotelDataset($sub_array, $hotel_Images);

            $response_2 = Http::withHeaders($this->getHeader())->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels', $sub_array)->json();

            $h_codesR2 = $response_2['hotels']['hotels'];

            $stringArray = array();

            foreach ($h_codesR2 as $hcode) {
                $stringArray[] = $hcode['code'];
            }

            $implodeArray = implode(',', $stringArray);
            $finalDataArray = $implodeArray;

            $URL_3 = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels/' . $finalDataArray . '/details?language=ENG&useSecondaryLanguage=False';

            $response_3 = Http::withHeaders($this->getHeader())->get($URL_3)->json();

            // $resImages=$response_3['hotels']['images']

            $newarr = array();

            foreach ($response_2['hotels']['hotels'] as $hotel) {

                foreach ($response_3['hotels'] as $hotelDetails) {
                    if ($hotelDetails['code'] == $hotel['code']) {
                        $array['hotels']['rates'] = $hotel;
                        $array['hotels']['details'] = $hotelDetails;

                        array_push($newarr, $array);
                    }
                }
            }

            return response()->json([
                'status' => 200,
                'response_1_data' => $newarr,
                'images' => $hotel_Images
                // 'response_2_data' => $response_3['hotels'],
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /***** Getting Hotels with Minimum Price END *****/

    public function fetchMainHotelDataset($sub_array, $hotel_Images)
    {
        ini_set('max_execution_time', 360);
        $response_2 = Http::withHeaders($this->getHeader())->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels', $sub_array)->json();

        // return $response_2;

        Cache::put('hotelcache', $response_2);

        return response()->json([
            'status' => 200,
            'response_1_data' => $response_2,
            'images' => $hotel_Images,
            'test' => Cache::get('hotelcache')
            // 'response_2_data' => $response_3['hotels'],
        ]);
    }

    // ################################################## //

    public function getHotelBedsMinPriceHotelsV1()
    {
        try {

            ini_set('max_execution_time', 360);
            $todayDate = Carbon::now()->format('Y-m-d');
            $_30DaysAfterDate = date('Y-m-d', strtotime('+1 days', strtotime($todayDate)));

            // $URL_1 = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?fields=all&language=ENG&from=1&to=40&useSecondaryLanguage=false';

            $URL_1 = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?fields=hotelCodes,images&language=ENG&from=1&to=50&useSecondaryLanguage=false';

            $URL_2 = 'https://api.test.hotelbeds.com/hotel-api/1.0/hotels';


            $response_1 = Http::withHeaders($this->getHeader())->get($URL_1)->json();

            // return $response_1;

            $hotel_Code = $response_1['hotels'];

            $hotel_Codes = array();
            $hotel_Images = array();



            foreach ($hotel_Code as $code) {
                $hotel_Codes[] =  $code['code'];
                $hotel_Images[] = ['code' => $code['code'], 'image' => $code['images'][0]['path']];
            }

            // return $hotel_Images;

            // return $hotel_Codes;

            $MainArray = [];

            $dateNow = Carbon::now()->toDateTimeString();

            $checkInDate = $todayDate;
            $checkOutDate = $_30DaysAfterDate;

            $MainArray['stay']['checkIn'] = $checkInDate;
            $MainArray['stay']['checkOut'] = $checkOutDate;


            $MainArray['occupancies']['rooms'] = '1';
            $NoOfPax = [];
            $MainArray['occupancies']['adults'] = '2';
            $MainArray['occupancies']['children'] = '0';


            $MainArray['hotels']['hotel'] = $hotel_Codes; //[1533, 1803, 2587, 3219];

            $sub_array = [];

            $sub_array['stay'] = $MainArray['stay'];
            $sub_array['occupancies'] = [$MainArray['occupancies']];
            $sub_array['hotels'] = $MainArray['hotels'];

            // return $sub_array;


            Cache::put('hotelcache', $sub_array);
            return $this->fetchMainHotelDatasetV1($sub_array, $hotel_Images);

            $response_2 = Http::withHeaders($this->getHeader())->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels', $sub_array)->json();

            // return response()->json([
            //     'status' => 200,
            //     'response_1_data' => $response_2,
            //     // 'response_2_data' => $response_3['hotels'],
            // ]);

            $h_codesR2 = $response_2['hotels']['hotels'];

            $stringArray = array();

            foreach ($h_codesR2 as $hcode) {
                $stringArray[] = $hcode['code'];
            }

            $implodeArray = implode(',', $stringArray);
            $finalDataArray = $implodeArray;

            $URL_3 = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels/' . $finalDataArray . '/details?language=ENG&useSecondaryLanguage=False';

            $response_3 = Http::withHeaders($this->getHeader())->get($URL_3)->json();

            // $resImages=$response_3['hotels']['images']

            $newarr = array();

            foreach ($response_2['hotels']['hotels'] as $hotel) {

                foreach ($response_3['hotels'] as $hotelDetails) {
                    if ($hotelDetails['code'] == $hotel['code']) {
                        $array['hotels']['rates'] = $hotel;
                        $array['hotels']['details'] = $hotelDetails;

                        array_push($newarr, $array);
                    }
                }
            }

            return response()->json([
                'status' => 200,
                'response_1_data' => $newarr,
                'images' => $hotel_Images
                // 'response_2_data' => $response_3['hotels'],
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /***** Getting Hotels with Minimum Price END *****/

    public function fetchMainHotelDatasetV1($sub_array, $hotel_Images)
    {
        ini_set('max_execution_time', 360);
        $response_2 = Http::withHeaders($this->getHeader())->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels', $sub_array)->json();

        // return $response_2;

        Cache::put('hotelcache', $response_2);

        return response()->json([
            'status' => 200,
            'response_1_data' => $response_2,
            'images' => $hotel_Images,
            'test' => Cache::get('hotelcache')
            // 'response_2_data' => $response_3['hotels'],
        ]);
    }

    /***** Fetching hotel details from hotel beds API *****/
    public function getHotelBedsDetails($destcode)
    {
        $url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?';

        //https://api.test.hotelbeds.com//hotel-content-api/1.0/locations/destinations?

        $Header = $this->getHeader();

        $dateNow = Carbon::now()->toDateTimeString();

        $HotelByCode['fields'] = 'all';
        $HotelByCode['destinationCode'] = $destcode;
        $HotelByCode['language'] = 'ENG';
        $HotelByCode['from'] = 1;
        $HotelByCode['to'] = 100;
        $HotelByCode['useSecondaryLanguage'] = false;

        $url .= http_build_query($HotelByCode);

        $city_array = array();

        try {

            $response = Http::withHeaders($this->getHeader())->get($url);

            $response = json_decode($response, true, 512, JSON_OBJECT_AS_ARRAY);

            foreach ($response['hotels'] as $res) {
                $city_array[] = $res['city']['content'];
            }

            // return $city_array;

            //Logging executing data executing logger
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/HotelBedsAPI_Logs.log')
            ])->info('executing get hotel details for:' . Auth::user() . $dateNow, $response);

            return response()->json([
                'status' => 200,
                'hotelData' => $response,
                'city_array' => $city_array
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /***** Fetching country list from hotel beds API *****/
    public function getHotelBedsCountries($fromdate, $todate)
    {
        try {

            $url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/locations/countries?';
            $from_date = $fromdate;
            $to_date = $todate;

            $countryList['language'] = 'ENG';
            $countryList['from'] = $from_date;
            $countryList['to'] = $to_date;
            $countryList['fields'] = 'all';

            $url .= http_build_query($countryList);

            $response = Http::withHeaders($this->getHeader())->get($url);

            $response = json_decode($response, true, 512, JSON_OBJECT_AS_ARRAY);

            if (isset($response->body['countries'])) {
                return $response->body['countries'];
            }

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /***** Fetching country list from hotel beds API END *****/

    /***** Fetching destinations list from hotel beds API *****/
    public function getHotelBedsDestinations($fromdate, $todate, $countrycodes)
    {
        try {
            $url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/locations/destinations?';
            $from_date = $fromdate;
            $to_date = $todate;
            $countryCodes = $countrycodes;

            $destinationList['fields'] = 'all';
            $destinationList['countryCodes'] = $countryCodes;
            $destinationList['language'] = 'ENG';
            $destinationList['from'] = $from_date;
            $destinationList['to'] = $to_date;

            $url .= http_build_query($destinationList);

            $response = Http::withHeaders($this->getHeader())->get($url);

            $response = json_decode($response, true, 512, JSON_OBJECT_AS_ARRAY);

            // if (isset($response->body['destinations'])) {
            //     return $response;
            // }

            $codeDestArray = array();

            foreach ($response['destinations'] as $res) {
                $codeDestArray[] = [$res['code'], $res['name']['content']];
                // $codeDestArray[] = $res['name']['content'];
            }

            return response([
                'status' => 200,
                'dest_res' => $codeDestArray
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /***** Fetching destinations list from hotel beds API END *****/

    /***** Get Hotel By ID *****/
    public function getHotelBedsHotelById($id)
    {

        $URL = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels/' . $id . '/details?language=ENG&useSecondaryLanguage=False';

        $response = Http::withHeaders($this->getHeader())->get($URL)->json();

        return $response['hotel'];
    }
    /***** Get Hotel By ID End *****/

    /***** Filter Hotel by Geo Location *****/
    public function filterHotelBedsHotelByGeoLoc($checkingdate, $checkoutdate, $roomcount, $adultcount, $childcount, $latitude, $longitude)
    {
        try {

            $DataArray = [];

            $headers = $this->getHeader();

            $checkIn = date('Y-m-d', strtotime($checkingdate));
            $checkOut = date('Y-m-d', strtotime($checkoutdate));

            $DataArray['stay']['checkIn'] = $checkIn;
            $DataArray['stay']['checkOut'] = $checkOut;

            $DataArray['occupancies']['rooms'] = (int)$roomcount;
            $DataArray['occupancies']['adults'] = (int)$adultcount;
            $DataArray['occupancies']['children'] = (int)$childcount;

            $DataArray['geolocation']['latitude'] = $latitude;
            $DataArray['geolocation']['longitude'] = $longitude;
            $DataArray['geolocation']['radius'] = 20;
            $DataArray['geolocation']['unit'] = 'km';

            $subArray = [];
            $subArray['stay'] = $DataArray['stay'];
            $subArray['occupancies'] = [$DataArray['occupancies']];
            $subArray['geolocation'] = $DataArray['geolocation'];

            $response = Http::withHeaders($headers)->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels', $subArray)->json();

            // return $response;

            if ($response['hotels']['total'] == 0) {
                return response([
                    'status' => 404,
                    'data_set' => 0
                ]);
            } else {
                return response([
                    'status' => 200,
                    'data_set' => $response
                ]);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /***** Filter Hotel by Geo Location End *****/

    /***** Filter Hotel by Board Type *****/
    public function getHotelBedsHotelByBoardCode($checkingdate, $checkoutdate, $roomcount, $adultcount, $childcount, $boardcode)
    {
        try {

            $DataArray = [];

            $headers = $this->getHeader();

            $URL_1 = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?fields=all&language=ENG&from=1&to=5&useSecondaryLanguage=false';

            $responseHotelCodes = Http::withHeaders($this->getHeader())->get($URL_1)->json();

            $hotel_Code = $responseHotelCodes['hotels'];

            $hotel_Codes = array();

            foreach ($hotel_Code as $code) {
                $hotel_Codes[] =  $code['code'];
            }

            $checkIn = date('Y-m-d', strtotime($checkingdate));
            $checkOut = date('Y-m-d', strtotime($checkoutdate));

            $DataArray['stay']['checkIn'] = $checkIn;
            $DataArray['stay']['checkOut'] = $checkOut;

            $DataArray['occupancies']['rooms'] = (int)$roomcount;
            $DataArray['occupancies']['adults'] = (int)$adultcount;
            $DataArray['occupancies']['children'] = (int)$childcount;

            $DataArray['hotels']['hotel'] = $hotel_Codes;

            $DataArray['boards']['included'] = true;
            $DataArray['boards']['board'] = explode(',', $boardcode);

            $subArray = [];
            $subArray['stay'] = $DataArray['stay'];
            $subArray['occupancies'] = [$DataArray['occupancies']];
            $subArray['hotels']['hotel'] = $DataArray['hotels']['hotel'];
            $subArray['boards']['included'] = $DataArray['boards']['included'];
            $subArray['boards']['board'] = $DataArray['boards']['board'];

            $response = Http::withHeaders($headers)->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels', $subArray)->json();

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /***** Filter Hotel by Board Type End *****/


    /***** Filter Hotel by Hotel Code *****/
    public function getHotelBedsRoomAvailabilityByHotelCode($checkingdate, $checkoutdate, $roomcount, $adultcount, $childcount, $hotelcode, $childages)
    {
        $FilterJsonArray = [];

        $HotelCode = $hotelcode;

        $dateNow = Carbon::now()->toDateTimeString();

        $checkInDate = date('Y-m-d', strtotime($checkingdate));
        $checkOutDate = date('Y-m-d', strtotime($checkoutdate));

        $FilterJsonArray['stay']['checkIn'] = $checkInDate;
        $FilterJsonArray['stay']['checkOut'] = $checkOutDate;

        $FilterJsonArray['occupancies']['rooms'] = $roomcount;
        $NoOfPax = [];
        $FilterJsonArray['occupancies']['adults'] = $adultcount;
        $FilterJsonArray['occupancies']['children'] = $childcount;

        $AgeofChild = explode(',', $childages);

        if ($FilterJsonArray['occupancies']['children'] > 0) {
            for ($cc = 0; $cc < $FilterJsonArray['occupancies']['children']; $cc++) {
                // for($x; $x < )
                $MainArray['occupancies']['paxes'][] = ['type' => 'CH', 'age' => $AgeofChild[$cc]];
            }
        }

        $FilterJsonArray['hotels']['hotel'] = [$HotelCode];

        $sub_array = [];

        $sub_array['stay'] = $FilterJsonArray['stay'];
        $sub_array['occupancies'] = [$FilterJsonArray['occupancies']];
        $sub_array['hotels'] = $FilterJsonArray['hotels'];

        try {
            $response = Http::withHeaders($this->getHeader())
                ->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels', $sub_array)->json();

            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/HotelBedsAPI_Logs.log')
            ])->info('executing checking room availability for:' . Auth::user() . $dateNow, $response);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /***** Filter Hotel by Hotel Code End *****/

    /***** Booking confirmation for Hotel beds API *****/

    public function updateHotelBedsHotelStatus($hotelpreid, $status, $oid, $request)
    {

        try {
            HotelsPreBookings::where('booking_id', $hotelpreid)->update(['cartStatus' => $status]);

            $hotelPreBooking = DB::table('tbl_hotels_pre_booking')->where('booking_id', '=', $hotelpreid)->get();

            $hotelDataSet = $hotelPreBooking[0];

            // return $hotelDataSet;
            $request->merge([

                'mrp' => $hotelDataSet->totalFare,
                'rateKey' => $hotelDataSet->rate_key,
                'holderFirstName' => $hotelDataSet->holderFirstName,
                'holderLastName' => $hotelDataSet->holderLastName,
                'roomId' => $hotelDataSet->roomId,
                'type' => $hotelDataSet->type,
                'name' => $hotelDataSet->name,
                'surname' => $hotelDataSet->surname,
                'remarks' => $hotelDataSet->remarks,
                'user' => $hotelDataSet->userID,
            ]);


            return response()->json([
                'status' => 200,
                'hotel' => $hotelDataSet,
                'hotelBeds' => $this->confirmHotelBedsHotelBooking($request, $oid, $hotelpreid)
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /***** Booking confirmation for Hotel beds API Start *****/
    public function confirmHotelBedsHotelBooking($request, $oid, $hotelpreid)
    {
        try {
            set_time_limit(0);
            $BookingArray = [];

            $dateNow = Carbon::now()->toDateTimeString();

            $UserId = $request['user'];
            $rateKey = $request['rateKey'];

            $roomId = explode(',', $request['roomId']);

            $holderFirstName = $request['holderFirstName'];
            $holderLastName = $request['holderLastName'];

            $remarks = $request['remarks'];

            $noOfPax = [];

            $type = explode(',', $request['type']);
            $name = explode(',', $request['name']);
            $surname = explode(',', $request['surname']);

            $noOfPax['pax']['roomId'] = $roomId;
            $noOfPax['pax']['type'] = $type;
            $noOfPax['pax']['name'] = $name;
            $noOfPax['pax']['surname'] = $surname;

            $BookingArray['holder']['name'] = $holderFirstName;
            $BookingArray['holder']['surname'] = $holderLastName;

            $BookingArray['rooms']['rateKey'] = $rateKey;

            if (count($noOfPax['pax']['type']) > 0) {
                for ($c = 0; $c < count($noOfPax['pax']['type']); $c++) {
                    $BookingArray['rooms']['paxes'][] = ['roomId' => $roomId[$c], 'type' => $type[$c], 'name' => $name[$c], 'surname' => $surname[$c]];
                }
            }

            $BookingArray['clientReference'] = 'IntegrationAgency';
            $BookingArray['remark'] = $remarks;

            $subBookingArray['holder'] = $BookingArray['holder'];
            $subBookingArray['rooms'] = [$BookingArray['rooms']];
            $subBookingArray['clientReference'] = $BookingArray['clientReference'];
            $subBookingArray['remark'] = $BookingArray['remark'];

            try {
                $response = Http::withHeaders($this->getHeader())
                    ->post('https://api.test.hotelbeds.com/hotel-api/1.0/bookings', $subBookingArray)->json();

                //Logging executing data executing logger
                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/HotelBedsAPI_Logs.log')
                ])->info('executing confirm booking for:' . Auth::user() . $dateNow, $response);

                // return $response;

                if ($response['booking']['reference']) {
                    $bookingRef = $response['booking']['reference'];
                    return $this->getHotelBedsHotelBookingDetails($bookingRef, $UserId, $oid, $hotelpreid);
                } else {
                    return response([
                        'status' => 400,
                    ]);
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /***** Booking confirmation for Hotel beds API End *****/

    /***** Get Booking Details Hotel beds API *****/
    public function getHotelBedsHotelBookingDetails($refId, $UId, $oid, $hotelpreid)
    {
        try {
            $bookingID = $refId;

            set_time_limit(0);

            $bookingDataResponse = Http::withHeaders($this->getHeader())->get('https://api.test.hotelbeds.com/hotel-api/1.0/bookings/' . $refId)->json();

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            // ******************** Booking Reference Data *********************
            $bookingRef = $bookingDataResponse['booking']['reference'];
            $HolderFname = $bookingDataResponse['booking']['holder']['name'];
            $HolderLname = $bookingDataResponse['booking']['holder']['surname'];
            $HolderFullName = $HolderFname . ' ' . $HolderLname;
            $resevationDate = $bookingDataResponse['auditData']['timestamp'];
            $checkinTime = $bookingDataResponse['booking']['hotel']['checkIn'];
            $checkoutTime = $bookingDataResponse['booking']['hotel']['checkOut'];
            $hotelName = $bookingDataResponse['booking']['hotel']['name'];

            $noOfAD = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['adults'];
            $noOfCH = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['children'];
            $bedType = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['rateClass'];
            $room_code = $bookingDataResponse['booking']['hotel']['rooms'][0]['code'];
            $roomType = $bookingDataResponse['booking']['hotel']['rooms'][0]['name'];
            $noOfRooms = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['rooms'];
            $boardCode = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['boardCode'];

            $remarks = $bookingDataResponse['booking']['remark'];
            $resevationPlatform = 'HOTELBEDS';
            $resevationStatus = $bookingDataResponse['booking']['status'];
            $currency = $bookingDataResponse['booking']['hotel']['currency'];
            $cancelation = $bookingDataResponse['booking']['modificationPolicies']['cancellation'];
            $modification = $bookingDataResponse['booking']['modificationPolicies']['modification'];
            $cancelation_amount = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['cancellationPolicies'][0]['amount'];
            $cancelation_deadline = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['cancellationPolicies'][0]['from'];

            $totalAmount = $bookingDataResponse['booking']['totalNet'];
            $pendingAmount = $bookingDataResponse['booking']['pendingAmount'];

            // $balancePayment = $totalAmount
            $created_at = $currentTime;
            $updated_at = $currentTime;
            $user_Id = $UId;



            $hotelRes = $this->hotel_reservation->makeHotelReservation($bookingRef, $HolderFullName, $resevationDate, $hotelName, $checkinTime, $checkoutTime, $noOfAD, $noOfCH, $bedType, $roomType, $noOfRooms, $boardCode, $remarks, $resevationPlatform, $resevationStatus, $currency, $cancelation, $modification, $cancelation_amount, $cancelation_deadline, $created_at, $updated_at, $user_Id);

            // return $hotelRes;
            $this->main_checkout->checkoutOrderHotel($oid, $hotelRes, $totalAmount, $currency, $user_Id, $hotelpreid);

            $this->hotel_room_details->createNewHotelRoomDetail($bookingRef, $room_code, $noOfAD, $noOfCH);

            $this->hotel_reservation_payment->createNewHotelReservationPayment($bookingRef, $totalAmount);

            if ($bookingDataResponse['booking']['hotel']['rooms'][0]['paxes'] >= 1) {
                foreach ($bookingDataResponse['booking']['hotel']['rooms'][0]['paxes'] as $pax) {

                    $fName = $pax['name'];
                    $sName = $pax['surname'];
                    $type = $pax['type'];


                    $this->hotel_traveller_details->createNewHotelTravellerDetail($bookingRef, $fName, $sName, $type);
                }
            }

            return $this->emailRecipt($bookingRef);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /***** Get Booking Details Hotel beds API End *****/

    /***** Send Confirmation Email Hotel beds API *****/
    public function emailRecipt($bookingId) //$bookingId
    {
        try {
            $dataJoinOne = $this->hotel_reservation->reservationFullDataset($bookingId);

            $dataJoinTwo = $this->hotel_reservation->reservationDatasetSecond($bookingId);

            $detailJoin = $this->hotel_reservation->reservationDatasetDetail($bookingId);

            $dataJoinThree = $this->hotel_reservation->reservationDatasetThird($bookingId);

            // return $dataJoinOne->email;

            $userEmail = $dataJoinOne->email;

            $invoice_no = $dataJoinOne->InoiceId;
            $resevationNumber = $dataJoinOne->resevation_no;
            $resevation_name = $dataJoinOne->resevation_name;
            $resevation_date = $dataJoinOne->resevation_date;
            $checkin_time = $dataJoinOne->checkin_time;
            $checkout_time = $dataJoinOne->checkout_time;
            $no_of_adults = $dataJoinOne->no_of_adults;
            $no_of_childs = $dataJoinOne->no_of_childs;
            $bed_type = $dataJoinOne->bed_type;
            $room_type = $dataJoinOne->room_type;
            $no_of_rooms = $dataJoinOne->no_of_rooms;
            $board_code = $dataJoinOne->board_code;
            $special_notice = $dataJoinOne->special_notice;
            $currency = $dataJoinOne->currency;
            $cancelation_deadline = $dataJoinOne->cancelation_deadline;
            $room_code = $dataJoinOne->room_code;
            $net_amount = $dataJoinOne->total_amount;
            $resevation_status = $dataJoinOne->resevation_status;
            $hotel_name = $dataJoinOne->hotel_name;
            $hotelAddress = $dataJoinOne->hotel_address;
            $hotel_email = $dataJoinOne->hotel_email;
            $ResHotelName = $dataJoinOne->ResHotelName;

            // ************************ Calculating Nights ************
            $datetime1 = new \DateTime($checkin_time);
            $datetime2 = new \DateTime($checkout_time);
            $interval = $datetime1->diff($datetime2);
            $days = $interval->format('%a');

            $nightsCount = $days;

            $total_amount = currency($net_amount, $currency, 'USD', true);

            $dataSet = [
                'invoice_id' => $invoice_no, 'resevation_no' => $resevationNumber, 'resevation_name' => $resevation_name, 'resevation_date' => $resevation_date,
                'checkin_date' => $checkin_time, 'checkout_time' => $checkout_time, 'no_of_adults' => $no_of_adults, 'no_of_childs' => $no_of_childs, 'bed_type' => $bed_type, 'room_type' => $room_type,
                'no_of_rooms' => $no_of_rooms, 'board_code' => $board_code, 'special_notice' => $special_notice, 'resevation_status' => $resevation_status,
                'hotelAddress' => $hotelAddress, 'hotelEmail' => $hotel_email, 'otherData' => $detailJoin, 'meal_data' => $dataJoinThree, 'ResHotelName' => $ResHotelName,
                'cancel_dealine' => $cancelation_deadline, 'room_code' => $room_code, 'total_amount' => $total_amount, 'nights' => $nightsCount, 'hotelName' => $hotel_name, 'otherdata' => $dataJoinTwo,
                'main_data' => $dataJoinOne
            ];



            $pdf = app('dompdf.wrapper');
            $pdf->loadView('Mails.AahaasRecipt', $dataSet);

            $pdf2 = app('dompdf.wrapper');
            $pdf2->loadView('Mails.HotelBeds', $dataSet);
            // return $pdf->download('pdf_file.pdf');

            // return view('Mails.HotelBeds', $dataSet);
            try {
                // return "Try";
                Mail::send('Mails.ReciptBody', $dataSet, function ($message) use ($userEmail, $resevationNumber, $pdf, $pdf2) {
                    $message->to($userEmail);
                    $message->subject('Confirmation Email on your Booking Reference: #' . $resevationNumber . '.');
                    $message->attachData($pdf->output(), $resevationNumber . '_' . 'Recipt.pdf', ['mime' => 'application/pdf',]);
                    $message->attachData($pdf2->output(), $resevationNumber . '_' . 'Beds_Recipt.pdf', ['mime' => 'application/pdf',]);
                });

                return response()->json([
                    'status' => 200,
                    'message' => 'Booking Confirmed and Confirmation Mail sent your email'
                ]);
            } catch (\Exception $ex) {
                throw $ex;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /***** Send Confirmation Email Hotel beds API End *****/

    /***** Booking Cancellation and Email Sending Hotel beds API *****/
    public function cancelHotelBedsHotelBooking($cancelreason, $remarks, $bookid)
    {
        try {

            $flag = 'CANCELLATION';

            $CancellationReason = $cancelreason;
            $CancellationRemark = $remarks;
            $status = 'CANCELLED';

            $todayDate = Carbon::now()->format('Y-m-d H:i:s');

            $table_data = DB::table('tbl_hotel_resevation')
                ->join('users', 'tbl_hotel_resevation.user_id', '=', 'users.id')
                ->select('tbl_hotel_resevation.resevation_status', 'tbl_hotel_resevation.cancelation_deadline', 'users.email')
                ->where('resevation_no', $bookid)->first();

            $cancellation_date = date('Y-m-d H:i:s', strtotime($table_data->cancelation_deadline));
            $userEmail = $table_data->email;

            $response = Http::withHeaders($this->getHeader())
                ->delete('https://api.test.hotelbeds.com/hotel-api/1.0/bookings/' . $bookid . '?cancellationFlag=CANCELLATION')->json();

            if ($response['error']['code'] === 'INVALID_DATA') {
                return response([
                    'status' => 500,
                    'message' => 'error on execution'
                ]);
            } else {

                // return $response;

                DB::select(DB::raw("UPDATE tbl_hotel_resevation SET status='$status',cancellation_remarks='$CancellationReason',other_remarks='$CancellationRemark',updated_at='$todayDate' WHERE resevation_no='$bookid'"));

                $cancelRef = $response['booking']['cancellationReference'];
                $status = $response['booking']['status'];
                $canceledDate = $response['booking']['creationDate'];

                $data = ['booking_id' => $bookid, 'cancel_ref' => $cancelRef, 'status' => $status, 'cancel_date' => $canceledDate];

                Mail::send(
                    'Mails.BookingCancel',
                    $data,
                    function ($message) use ($userEmail) {
                        $message->to($userEmail);
                        $message->subject('Booking Cancellation Confirmation');
                    }
                );

                return response()->json([
                    'status' => 200,
                    'message' => '#' . $bookid . ' booking canceled successfully'
                ]);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /***** Booking Cancellation and Email Sending Hotel beds API End *****/

    //destinations wise Hotel search
    public function fetchDestinationWiseHotels($checkin, $checkout, $rooms, $adults, $children, $latitude, $longitude)
    {
        try {
            $DestinationArray = [];

            ini_set('max_execution_time', 360);

            $DestinationArray['stay']['checkIn'] = $checkin;
            $DestinationArray['stay']['checkOut'] = $checkout;
            $DestinationArray['occupancies']['rooms'] = (int)$rooms;
            $DestinationArray['occupancies']['adults'] = (int)$adults;
            $DestinationArray['occupancies']['children'] = (int)$children;
            $DestinationArray['geolocation']['latitude'] = (float)$latitude;
            $DestinationArray['geolocation']['longitude'] = (float)$longitude;
            $DestinationArray['geolocation']['radius'] = 20;
            $DestinationArray['geolocation']['unit'] = 'km';

            $SubArray = [];

            $SubArray['stay']['checkIn'] = $DestinationArray['stay']['checkIn'];
            $SubArray['stay']['checkOut'] = $DestinationArray['stay']['checkOut'];
            $SubArray['occupancies'] = [$DestinationArray['occupancies']];
            $SubArray['geolocation']['latitude'] = $DestinationArray['geolocation']['latitude'];
            $SubArray['geolocation']['longitude'] = $DestinationArray['geolocation']['longitude'];
            $SubArray['geolocation']['radius'] = $DestinationArray['geolocation']['radius'];
            $SubArray['geolocation']['unit'] = $DestinationArray['geolocation']['unit'];

            $response = Http::withHeaders($this->getHeader())
                ->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels', $SubArray)->json();

            // return $response;

            if ($response['hotels']['total'] == 0) {
                return response([
                    'status' => 404,
                    'data_set' => 0
                ]);
            } else {

                return $this->filterImagesForDestinationWiseHotels($response['hotels']);
                // return response([
                //     'status' => 200,
                //     'data_set' => $response
                // ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function filterImagesForDestinationWiseHotels($response)
    {
        try {

            ini_set('max_execution_time', 360);

            $DestHotelData = array();

            $hotelArray = array();

            // return $response;

            foreach ($response['hotels'] as $res) {
                $id = $res['code'];
                $apiCall = Http::withHeaders($this->getHeader())->get('https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels/' . $id . '/details?language=ENG&useSecondaryLanguage=False')->json();
                // $DestHotelData[] = $apiCall;

                Cache::put('dest-data', $apiCall);

                // $caching = Cache::get('dest-data');
                $DestHotelData[] = $apiCall;
            }

            return response([
                'status' => 200,
                'data_set' => $DestHotelData
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // ************** ############### *****************

    //get hotel beds dataset based on customer current location
    public function fetchHotelsBedsBasedOnCurrentLocation($country_code)
    {
        try {

            ini_set('max_execution_time', 360);
            $EndURL = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?';

            //https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?fields=all&countryCodes=LK&language=ENG&from=1&useSecondaryLanguage=false&to=100
            //https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?fields=all&countryCodes=LK&language=ENG&from=1&useSecondaryLanguage=false&to=100

            $Dataset['fields'] = 'all';
            $Dataset['countryCode'] = $country_code;
            $Dataset['language'] = 'ENG';
            $Dataset['from'] = 1;
            $Dataset['useSecondaryLanguage'] = 'false';
            $Dataset['to'] = 100;

            $EndURL .= http_build_query($Dataset);


            $response = Http::withHeaders($this->getHeader())->get($EndURL)->json();

            Cache::add('hotel-data', $response);

            return response([
                'status' => 200,
                'type' => 'caching',
                'hotel_data' => $response
            ]);

            if (Cache::has('hotel-data')) {
                return response([
                    'status' => 200,
                    'type' => 'cache',
                    'hotel_data' => Cache::get('hotel-data')
                ]);
            } else {

                $response = Http::withHeaders($this->getHeader())->get($EndURL)->json();

                Cache::add('hotel-data', $response);

                return response([
                    'status' => 200,
                    'type' => 'caching',
                    'hotel_data' => Cache::get('hotel-data')
                ]);
            }
        } catch (\Throwable $th) {

            throw $th;
        }
    }

    public function getHotelFacilities()
    {
        $URL_1 = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/types/facilitygroups?fields=all&language=ENG&from=1&to=100&useSecondaryLanguage=True';

        $response_1 = Http::withHeaders($this->getHeader())->get($URL_1)->json();

        return response()->json([
            'status' => 200,
            'facilities' => $response_1,

        ]);
    }
}
