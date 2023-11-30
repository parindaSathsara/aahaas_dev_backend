<?php

namespace App\Http\Controllers\TBO_Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel\HotelMeta\HotelMeta;
use App\Models\HotelsMeta\HotelsMeta;
use App\Models\HotelsMeta\TBOUserTokens;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use IlluminateAgnostic\Arr\Support\Arr;

class TBOController extends Controller
{

    public function generateTBOToken(Request $request)
    {
        $tboUserTokens = TBOUserTokens::where("token_ip", $request->ip())->where("created_at", ">", Carbon::now()->subDay())->where("created_at", "<", Carbon::now())->get();

        if (count($tboUserTokens) > 0) {
            return $tboUserTokens[0]->token_code;
        } else {

            $authArray['ClientId'] = "ApiIntegrationNew";
            $authArray['UserName'] = "Sharmila1";
            $authArray['Password'] = "Sharmila@1234";
            $authArray['EndUserIp'] = $request->ip();
            // $hotelOrigin = Hotel::all();
            $response = Http::post('http://api.tektravels.com/SharedServices/SharedData.svc/rest/Authenticate', $authArray)->json();

            TBOUserTokens::create(['token_code' => $response['TokenId'], 'token_ip' => $request->ip()]);

            return $response['TokenId'];
        }
    }

    public function getHotelsByLatLon(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius');


        $harvasineRadius = "(6371 * acos(cos(radians(" . $latitude . ")) 
        * cos(radians(aahaas_hotel_meta.latitude)) 
        * cos(radians(aahaas_hotel_meta.longitude) - radians(" . $longitude . ")) 
        + sin(radians(" . $latitude . ")) 
        * sin(radians(aahaas_hotel_meta.latitude))))";


        $request = HotelsMeta::whereRaw("{$harvasineRadius} < ?", [$radius])->get();

        return $request;
    }

    public function hotelsDetails(Request $request, $id, $provider, $status)
    {

        $checkInDate = $request->input('CheckInDate');
        $noOfNights = $request->input('NoOfNights');
        $noOfRooms = $request->input('NoOfRooms');
        $noOfAdults = $request->input('NoOfAdults');
        $noOfChild = $request->input('NoOfChild');

        if ($noOfChild > 0) {
            $childAge = explode(',', $request->input('ChildAge'));
        } else {
            $childAge = null;
        }


        if ($provider == "hotelAhs") {
            //Nothing
        } else {
            $response = $this->generateTBOToken($request);

            $hotelMeta = HotelMeta::where("hotelCode", $id)->first();

            $requestHotelInfo = [
                "CheckInDate" => $checkInDate,
                "NoOfNights" => $noOfNights,
                "CountryCode" => "LK",
                "CityId" => $hotelMeta->city_code,
                "ResultCount" => null,
                "PreferredCurrency" => "INR",
                "GuestNationality" => "IN",
                "NoOfRooms" => $noOfRooms,
                "RoomGuests" => [
                    [
                        "NoOfAdults" => $noOfAdults,
                        "NoOfChild" => $noOfChild,
                        "ChildAge" => $childAge
                    ]
                ],
                "MaxRating" => 5,
                "MinRating" => 0,
                "ReviewScore" => null,
                "IsNearBySearchAllowed" => false,
                "EndUserIp" => $request->ip(),
                "TokenId" => $response
            ];

            // return $requestHotelInfo;


            $getResults = Http::post('http://api.tektravels.com/BookingEngineService_Hotel/hotelservice.svc/rest/GetHotelResult/', $requestHotelInfo)->json();


            // return $getResults;

            if ($getResults['HotelSearchResult']['Error']['ErrorCode'] == 0) {
                $traceID = $getResults['HotelSearchResult']['TraceId'];


                $hotelResults = $getResults['HotelSearchResult']['HotelResults'];

                $filteredHotel = array_values(Arr::where($hotelResults, function ($value, $key) use ($id) {
                    return $value["HotelCode"] === $id;
                }));

                if (count($filteredHotel) > 0) {

                    if ($status == "details") {
                        return response()->json([
                            'status' => 200,
                            'hotelData' => $filteredHotel[0],
                            'traceID' => $traceID,
                            'resultIndex' => $filteredHotel[0]['ResultIndex'],
                            'tokenID' => $response
                        ]);
                    } else {
                        //

                        $requestHotelInfo['EndUserIp'] = $request->ip();
                        $requestHotelInfo['TokenId'] =  $response;
                        $requestHotelInfo['TraceId'] = $traceID;
                        $requestHotelInfo['ResultIndex'] = $filteredHotel[0]['ResultIndex'];
                        $requestHotelInfo['HotelCode'] =  $filteredHotel[0]['HotelCode'];

                        $getRatesResult = Http::post('http://api.tektravels.com/BookingEngineService_Hotel/hotelservice.svc/rest/GetHotelRoom', $requestHotelInfo)->json();


                        return $getRatesResult;
                    }
                } else {
                    return response()->json([
                        'status' => 401,
                        'error_message' => "This hotel is not available for selected criteria."
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 401,
                    'error_message' => $getResults['HotelSearchResult']['Error']['ErrorMessage']
                ]);
            }
        }
    }
}
