<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use Httpful\Handlers\JsonHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TestController extends Controller
{

    function getSignature()
    {
        $api_key = config('services.hotelbed.key');
        $secret_key = config('services.hotelbed.secret');
        $current_timestamp = Carbon::now()->timestamp;
        $signature = $api_key . $secret_key . $current_timestamp;

        $x_sig = hash('sha256', $signature, true);

        $test_key = bin2hex($x_sig);

        return $test_key;
    }

    function getHeader()
    {
        $Header = [];

        $Header['Accept'] = 'application/json';
        $Header['Api-key'] = config('services.hotelbed.key');
        $Header['X-Signature'] = $this->getSignature();
        $Header['Content-Type'] = 'application/json';

        return $Header;
    }

    public function checkAvailability(Request $request)
    {
        $MainArray = [];

        $dateNow = Carbon::now()->toDateTimeString();


        $checkInDate = date('Y-m-d', strtotime($request->input('check_in_date')));
        $checkOutDate = date('Y-m-d', strtotime($request->input('check_out_date')));

        $MainArray['stay']['checkIn'] = $checkInDate;
        $MainArray['stay']['checkOut'] = $checkOutDate;


        $MainArray['occupancies']['rooms'] = $request->input('number_of_rooms');
        $NoOfPax = [];
        $MainArray['occupancies']['adults'] = $request->input('no_of_adults');
        $MainArray['occupancies']['children'] = $request->input('no_of_childs');

        $AgeofChild = explode(',', $request->input('age_of_child'));

        if ($MainArray['occupancies']['children'] > 0) {
            for ($cc = 0; $cc < $MainArray['occupancies']['children']; $cc++) {
                // for($x; $x < )
                $MainArray['occupancies']['paxes'][] = ['type' => 'CH', 'age' => $AgeofChild[$cc]];
            }
        }

        $MainArray['hotels']['hotel'] = [1533, 1803, 2587, 3219];

        $sub_array = [];

        $sub_array['stay'] = $MainArray['stay'];
        $sub_array['occupancies'] = [$MainArray['occupancies']];
        $sub_array['hotels'] = $MainArray['hotels'];



        $response = Http::withHeaders($this->getHeader())
            ->post('https://api.test.hotelbeds.com/hotel-api/1.0/hotels', $sub_array)->json();

        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/HotelBedsAPI_Logs.log')
        ])->info('executing booking confirmation for:' . Auth::user() . $dateNow, $response);

        return $response;
    }

    public function confirmBooking(Request $request)
    {
        $dateNow = Carbon::now()->toDateTimeString();
        $BookingArray = [];
        $Header = $this->getHeader();
        $url = 'https://api.test.hotelbeds.com/hotel-api/1.0/bookings';

        $rateKey = $request->input('rateKey');
        $roomId = $request->input('roomId');

        $holderFirstName = $request->input('holderFirstName');
        $holderLastName = $request->input('holderLastName');

        $noOfPax = [];

        $type = explode(',', $request->input('type'));
        $name = explode(',', $request->input('name'));
        $surname = explode(',', $request->input('surname'));

        $noOfPax['pax']['roomId'] = $roomId;
        $noOfPax['pax']['type'] = $type;
        $noOfPax['pax']['name'] = $name;
        $noOfPax['pax']['surname'] = $surname;

        $BookingArray['holder']['name'] = $holderFirstName;
        $BookingArray['holder']['surname'] = $holderLastName;

        $BookingArray['rooms']['rateKey'] = $rateKey;

        // $subArray['holder'] = $BookingArray['holder'];
        // $subArray['rooms'] = [$BookingArray['rooms']];
        // $subArray['rooms'] = [$BookingArray['paxes']];



        if (count($noOfPax['pax']['type']) > 0) {
            for ($c = 0; $c < count($noOfPax['pax']['type']); $c++) {

                // $BookingArray['rooms']['paxes'][] = ['roomId' => $roomId, 'type' => $type[$c], 'name' => $name[$c], 'surname' => $surname[$c]];
                $BookingArray['rooms']['paxes'][] = ['roomId' => $roomId, 'type' => $type[$c], 'name' => $name[$c], 'surname' => $surname[$c]];
            }
        }

        $BookingArray['clientReference'] = 'IntegrationAgency';
        $BookingArray['remark'] = $request->input('remarks');

        $subBookingArray['holder'] = $BookingArray['holder'];
        $subBookingArray['rooms'] = [$BookingArray['rooms']];
        $subBookingArray['clientReference'] = $BookingArray['clientReference'];
        $subBookingArray['remark'] = $BookingArray['remark'];

        $response = Http::withHeaders($this->getHeader())
            ->post('https://api.test.hotelbeds.com/hotel-api/1.0/bookings', $subBookingArray)->json();


        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/HotelBedsAPI_Logs.log')
        ])->info('executing booking confirmation for:' . Auth::user() . $dateNow, $response);
        // Log::info('executing booking confirmation for', $response);

        return $response;

        // return $BookingArray;
    }

    /* -- */
    public function bookingCancellation($id)
    {
        $flag = "CANCELLATION";

        // $bookingReference = $request->input('bookingReference');

        $Header = $this->getHeader();

        $dateNow = Carbon::now()->toDateTimeString();

        $response = Http::withHeaders($Header)
            ->delete('https://api.test.hotelbeds.com/hotel-api/1.0/bookings/' . $id . '?cancellationFlag=' . $flag)->json();

        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/HotelBedsAPI_Logs.log')
        ])->info('executing booking confirmation for:' . Auth::user() . $dateNow, $response);

        return $response;
    }

    /* ---------xxx---------- */

    public function getHotelDetails($fields = false, $codes = false, $destinationCode = false, $countryCode = false, $lastUpdateTime = false, $language = false, $from = 1, $to = 10, $useSecondaryLanguage = false)
    {
        $url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?';

        $Header = $this->getHeader();

        $url_Fields = [];

        if ($codes) {
            $url_Fields['codes'] = $codes;
        }
        if ($destinationCode) {
            $url_Fields['destinationCode'] = $destinationCode;
        }
        if ($countryCode) {
            $url_Fields['countryCode'] = $countryCode;
        }
        if ($lastUpdateTime) {
            $url_Fields['lastUpdateTime'] = $lastUpdateTime;
        }
        if ($language) {
            $url_Fields['language'] = $language;
        }
        if ($from) {
            $url_Fields['from'] = $from;
        }
        if ($to) {
            $url_Fields['to'] = $to;
        }
        if ($useSecondaryLanguage) {
            $url_Fields['useSecondaryLanguage'] = $useSecondaryLanguage;
        }

        $url .= http_build_query($url_Fields);

        if ($fields) {
            $url .= '&fields=' . explode('%2C', $fields);
        } else {
            $url .= '&fields=all';
        }

        try {

            $response = Http::withHeaders($this->getHeader())->get($url);

            $response = json_decode($response, true, 512, JSON_OBJECT_AS_ARRAY);

            if (empty($response['hotels'])) {
                return response()->json([
                    'status' => 400,
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'hotelsdata' => $response
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 401,
                'error_message' => $exception->getMessage()
            ]);
        }
    }

    // public function getRateKey(Request $request)
    // {
    //     $checkInDate = str_replace('-', '', $request->input('checkIn'));
    //     $checkOutDate = str_replace('-', '', $request->input('checkOut'));

    //     $response = $checkInDate . '|' . $checkOutDate;

    //     return $response;
    // }

    public function getCountryList(Request $request)
    {
        $Header = $this->getHeader();
        $url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/locations/countries?';
        $from_date = $request->input('fromDate');
        $to_date = $request->input('toDate');

        $countryList['language'] = 'ENG';
        $countryList['from'] = $from_date;
        $countryList['to'] = $to_date;
        $countryList['fields'] = 'all';

        $url .= http_build_query($countryList);

        $response = Http::withHeaders($Header)->get($url);

        $response = json_decode($response, true, 512, JSON_OBJECT_AS_ARRAY);

        if (isset($response->body['countries'])) {
            return $response->body['countries'];
        }

        return $response;
    }

    public function getDestinationList(Request $request)
    {
        $Header = $this->getHeader();
        $url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/locations/destinations?';
        $from_date = $request->input('fromDate');
        $to_date = $request->input('toDate');
        $countryCodes = explode(',', $request->input('countryCodes'));

        $destinationList['fields'] = 'all';
        $destinationList['countryCodes'] = $countryCodes;
        $destinationList['language'] = 'ENG';
        $destinationList['from'] = $from_date;
        $destinationList['to'] = $to_date;

        $url .= http_build_query($destinationList);

        $response = Http::withHeaders($Header)->get($url);

        $response = json_decode($response, true, 512, JSON_OBJECT_AS_ARRAY);

        // if (isset($response->body['destinations'])) {
        //     return $response;
        // }

        return $response;
    }
    /* xxxxxxxxxxxxxxxxxxxxxxxxxxxx */
    /* Hotel TBO Method for Testing */
    /* xxxxxxxxxxxxxxxxxxxxxxxxxxxx */


    public function decryptEmail(Request $req)
    {
        $decryptValue = $req->input('dec_value');
        // $encrypt = encrypt($decryptValue);
        //eyJpdiI6Im1uZnJNTUpjS0FCSkJ4bnVwL21MSFE9PSIsInZhbHVlIjoiTDNkcWpHOXVQK3UrdkNyQ3lvZzdZdUpQT3FjdWgzcFBJV2hXWFRBOVFoTXV2RG5JOVRpL2hiRnYxTVRJSS9rSSIsIm1hYyI6IjRlZTZmMGY1NWQwNDRmYjcxNjM3NjQ5ZDEyNGFhMmRlZjhjOGIwOTQwYTkxMzhkNWJmMWRjMDQzNWQ3YzIzNDQiLCJ0YWciOiIifQ==
        // $toDecrypt = Crypt::decrypt($decryptValue);
        $toDecrypt1 = decrypt($decryptValue);

        return $toDecrypt1;
    }
}
