<?php

namespace App\Http\Controllers\TBO_Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel\HotelMeta\HotelMeta;
use App\Models\HotelsMeta\HotelsMeta;
use App\Models\HotelsMeta\TBOUserTokens;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use IlluminateAgnostic\Arr\Support\Arr;

class TBOController extends Controller
{




    public function generateTBOToken(Request $request)
    {
        // $tboUserTokens = TBOUserTokens::where("token_ip", $request->ip())->where("created_at", ">", Carbon::now()->subDay())->where("created_at", "<", Carbon::now())->get();

        // if (count($tboUserTokens) > 0) {
        //     return $tboUserTokens[0]->token_code;
        // } else {

        $authArray['ClientId'] = "ApiIntegrationNew";
        $authArray['UserName'] = "Sharmila1";
        $authArray['Password'] = "Sharmila@1234";
        $authArray['EndUserIp'] = $request->ip();
        // $hotelOrigin = Hotel::all();
        $response = Http::post('http://api.tektravels.com/SharedServices/SharedData.svc/rest/Authenticate', $authArray)->json();

        // TBOUserTokens::create(['token_code' => $response['TokenId'], 'token_ip' => $request->ip()]);

        return $response['TokenId'];
        // }
    }

    public function reValidateCartHotelBeforeBooking(Request $request)
    {
        $hotelCart = json_decode($request->blockData);

        // return $hotelCart;

        $request->replace(
            [
                "CheckInDate" => $hotelCart->CheckInDate,
                "NoOfNights" => $hotelCart->NoOfNights,
                "NoOfRooms" => $hotelCart->NoOfRooms,
                "NoOfAdults" => $hotelCart->NoOfAdults,
                "NoOfChild" => $hotelCart->NoOfChild,
            ]
        );


        // return $hotelCart->hotelMainRequest->hotelData->HotelCode;

        $hotelDetails = $this->hotelsDetails($request, $hotelCart->hotelMainRequest->hotelData->HotelCode, "hotelTbo", "details")->getData();
        // return $hotelDetails;

        if ($hotelDetails->status == 200) {

            $hotelRoom = $this->hotelsDetails($request, $hotelCart->HotelID, "hotelTbo", "rates");

            // return $hotelRoom;

       

            
         

            if ($hotelRoom["GetHotelRoomResult"]["Error"]["ErrorCode"] == 0) {

                $currentRoomRatePlan = $hotelCart->hotelRatesRequest->RatePlanCode;

                $filteredHotel = array_values(Arr::where($hotelRoom["GetHotelRoomResult"]['HotelRoomsDetails'], function ($value, $key) use ($currentRoomRatePlan) {
                    return $value["RatePlanCode"] == $currentRoomRatePlan;
                }));

                if(count($filteredHotel)==0){
                    $hotelRoomAvailablility=$hotelRoom["GetHotelRoomResult"]['HotelRoomsDetails'][0];

                    $hotelCart->hotelRatesRequest=$hotelRoomAvailablility;
                    
                    return $hotelCart;
                    // return json_decode($hotelCart);
                }

                return response()->json([
                    'status' => 200,
                    'message' => "Rates fetching successfully"
                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'error_message' => $hotelRoom["GetHotelRoomResult"]["Error"]["ErrorMessage"]
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'error_message' => "This hotel can't checkout."
            ]);
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


        return response()->json([
            'status' => 200,
            'data' => $request
        ]);
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


            // return $getResults;

            if ($getResults['HotelSearchResult']['Error']['ErrorCode'] == 0) {
                $traceID = $getResults['HotelSearchResult']['TraceId'];


                $hotelResults = $getResults['HotelSearchResult']['HotelResults'];

                $filteredHotel = array_values(Arr::where($hotelResults, function ($value, $key) use ($id) {
                    return $value["HotelCode"] == $id;
                }));


  
                // return $id;

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

                        // $responseHotelInfo["EndUserIpRest"]
                        //

                        $requestHotelInfo['EndUserIp'] = $request->ip();
                        $requestHotelInfo['TokenId'] = $response;
                        $requestHotelInfo['TraceId'] = $traceID;
                        $requestHotelInfo['ResultIndex'] = $filteredHotel[0]['ResultIndex'];
                        $requestHotelInfo['HotelCode'] = $filteredHotel[0]['HotelCode'];

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



    public function hotelBlockRoom(Request $request)
    {

        try {
            $hotelMainResponse = json_decode($request->input('hotelMainRequest'));

            $hotelRatesResponse = json_decode($request->input('hotelRatesRequest'));

            $hotelBlockRoom = response()->json([
                'ResultIndex' => $hotelMainResponse->hotelData->ResultIndex,
                'HotelCode' => $hotelMainResponse->hotelData->HotelCode,
                'HotelName' => $hotelMainResponse->hotelData->HotelName,
                'GuestNationality' => "IN",
                'NoOfRooms' => $request->input("NoOfRooms"),
                'ClientReferenceNo' => "0",
                'IsVoucherBooking' => "true",
                'HotelRoomsDetails' => [[
                    'RoomIndex' => $hotelRatesResponse->RoomIndex,
                    'RoomTypeCode' => $hotelRatesResponse->RoomTypeCode,
                    'RoomTypeName' => $hotelRatesResponse->RoomTypeName,
                    'RatePlanCode' => $hotelRatesResponse->RatePlanCode,

                    'BedTypeCode' => null,
                    'SmokingPreference' => $hotelRatesResponse->SmokingPreference,
                    'Supplements' => $hotelRatesResponse->HotelSupplements,
                    'Price' => $hotelRatesResponse->Price,

                ]],
                'EndUserIp' => $request->ip(),
                'TokenId' => $hotelMainResponse->tokenID,
                'TraceId' => $request->input("traceId"),
            ]);



            return $hotelBlockRoom;
        } catch (Exception $e) {
            return $e;
        }
    }
}
