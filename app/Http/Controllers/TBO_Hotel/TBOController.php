<?php

namespace App\Http\Controllers\TBO_Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel\HotelMeta\HotelMeta;
use App\Models\HotelsMeta\HotelRoomDailyInventory;
use App\Models\Hotels\HotelsPreBookings;
use App\Models\HotelsMeta\HotelPreBooking;
use App\Models\HotelsMeta\HotelRoomRate;
use App\Models\HotelsMeta\HotelsMeta;
use App\Models\HotelsMeta\TBOUserTokens;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use IlluminateAgnostic\Arr\Support\Arr;
use App\Models\Customer\MainCheckout;

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


    public function revalidateWhenCheckout(Request $request)
    {
        $id = $request->input("customer_id");

        $preBookingDataSet = DB::table('tbl_customer_carts')
            ->where('customer_id', $id)
            ->join('hotel_prebooking', 'tbl_customer_carts.hotels_pre_id', '=', 'hotel_prebooking.prebooking_id')
            ->get();

        foreach ($preBookingDataSet as $preBooking) {
            $request->replace([
                'customer_cart_id' => $preBooking->customer_cart_id
            ]);

            $revalidate[] = $this->reValidateCartHotelBeforeBooking($request);
        }

        return response()->json([
            'status' => 200,
            'response' => $revalidate
        ]);
    }


    public function hotelBooking(Request $request)
    {
        $hotelPreID = $request->input("hotelPreID");
        $preBookingDataSet = DB::table('hotel_prebooking')
            ->where('hotel_prebooking.prebooking_id', $hotelPreID)
            ->get();




        $originFromDB = json_decode($preBookingDataSet[0]->bookingdataset, true);

        // return $originFromDB;


        $response = $this->generateTBOToken($request);


        $dataSet = [
            "ResultIndex" => $originFromDB["hotelMainRequest"]['hotelData']['ResultIndex'],
            "HotelCode" => $originFromDB["hotelMainRequest"]["hotelData"]["HotelCode"],
            "HotelName" =>  $originFromDB["hotelMainRequest"]["hotelData"]["HotelName"],
            "GuestNationality" => "IN",
            "NoOfRooms" => $originFromDB["NoOfRooms"],
            "ClientReferenceNo" => "0",
            "IsVoucherBooking" => "false",
            "HotelRoomsDetails" => [
                "RoomIndex" => $originFromDB["hotelRatesRequest"]["RoomIndex"],
                "RoomTypeCode" => $originFromDB["hotelRatesRequest"]["RoomTypeCode"],
                "RoomTypeName" => $originFromDB["hotelRatesRequest"]["RoomTypeName"],
                "RatePlanCode" => $originFromDB["hotelRatesRequest"]["RatePlanCode"],
                "BedTypeCode" => null,
                "SmokingPreference" => 0,
                "Supplements" => null,
                "Price" => $originFromDB["hotelRatesRequest"]["Price"],
                // [
                //     "CurrencyCode" => "INR",
                //     "RoomPrice" => "4620.0",
                //     "Tax" => "0.0",
                //     "ExtraGuestCharge" => "0.0",
                //     "ChildCharge" => "0.0",
                //     "OtherCharges" => "0.0",
                //     "Discount" => "0.0",
                //     "PublishedPrice" => "4620.0",
                //     "PublishedPriceRoundedOff" => "4620",
                //     "OfferedPrice" => "4620.0",
                //     "OfferedPriceRoundedOff" => "4620",
                //     "AgentCommission" => "0.0",
                //     "AgentMarkUp" => "0.0",
                //     "TDS" => "0.0"
                // ],
                "HotelPassenger" => $originFromDB["paxDetails"],
            ],
            "EndUserIp" => $request->ip(),
            "TokenId" => $response,
            "TraceId" => $originFromDB["traceId"],
        ];


        $bookingDataSet = Http::post('http://api.tektravels.com/BookingEngineService_Hotel/hotelservice.svc/rest/Book', $dataSet)->json();



        return $bookingDataSet;
        // return $preBookingDataSet;

    }




    public function  reValidateCartHotelBeforeBooking(Request $request)
    {
        try {
            $customer_cart_id = $request->input("customer_cart_id");

            $preBookingDataSet = DB::table('tbl_customer_carts')
                ->where('customer_cart_id', $customer_cart_id)
                ->join('hotel_prebooking', 'tbl_customer_carts.hotels_pre_id', '=', 'hotel_prebooking.prebooking_id')
                ->get();

            if (count($preBookingDataSet) == 0) {
                return "NoHotelsAvailable";
            }
            //
            else {

                $hotelCart = json_decode($preBookingDataSet[0]->bookingdataset);

                $request->replace(
                    [
                        "CheckInDate" => $hotelCart->CheckInDate,
                        "NoOfNights" => $hotelCart->NoOfNights,
                        "NoOfRooms" => $hotelCart->NoOfRooms,
                        "NoOfAdults" => $hotelCart->NoOfAdults,
                        "NoOfChild" => $hotelCart->NoOfChild,
                    ]
                );

                $hotelDetails = $this->hotelsDetails($request, $hotelCart->hotelMainRequest->hotelData->HotelCode, "hotelTbo", "details")->getData();

                if ($hotelDetails->status == 200) {

                    $hotelRoom = $this->hotelsDetails($request, $hotelCart->HotelID, "hotelTbo", "rates");

                    if ($hotelRoom["GetHotelRoomResult"]["Error"]["ErrorMessage"] == "") {

                        $currentRoomRatePlan = $hotelCart->hotelRatesRequest->RatePlanCode;

                        $filteredHotel = array_values(Arr::where($hotelRoom["GetHotelRoomResult"]['HotelRoomsDetails'], function ($value, $key) use ($currentRoomRatePlan) {
                            return $value["RatePlanCode"] == $currentRoomRatePlan;
                        }));

                        if (count($filteredHotel) == 0) {
                            $hotelRoomAvailablility = $hotelRoom["GetHotelRoomResult"]['HotelRoomsDetails'][0];

                            $hotelCart->hotelRatesRequest = $hotelRoomAvailablility;

                            $hotelCart->traceId = $hotelDetails->traceID;

                            $bookingdataset = json_encode($hotelCart, true);

                            HotelPreBooking::where('prebooking_id', $preBookingDataSet[0]->prebooking_id)->update([
                                "bookingdataset" => $bookingdataset,
                                "update_status" => "RatesUpdated"
                            ]);

                            return "RatesUpdated";
                        } else {
                            HotelPreBooking::where('prebooking_id', $preBookingDataSet[0]->prebooking_id)->update([
                                "update_status" => "Origin"
                            ]);

                            return "Origin";
                        }
                        //
                    } else {
                        HotelPreBooking::where('prebooking_id', $preBookingDataSet[0]->prebooking_id)->update([
                            "update_status" => "NotAvailable"
                        ]);

                        return "Error";
                    }
                } else {
                    HotelPreBooking::where('prebooking_id', $preBookingDataSet[0]->prebooking_id)->update([
                        "update_status" => "NotAvailable"
                    ]);

                    return "Error";
                }
            }
        } catch (\Exception $exception) {
            return "Error";
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

    public function searchHotels(Request $request)
    {
        $checkInDate = $request->input('CheckInDate');
        $noOfNights = $request->input('NoOfNights');
        $noOfRooms = $request->input('NoOfRooms');
        $noOfAdults = $request->input('NoOfAdults');
        $noOfChild = $request->input('NoOfChild');
        $city = $request->input("City");

        $response = $this->generateTBOToken($request);

        $requestedDataSet = [
            'ClientId' => 'ApiIntegrationNew',
            'EndUserIp' => $request->ip(),
            'TokenId' => $response,
            'SearchType' => 1,
            'CountryCode' => $request->input("Country"),
        ];

        $CityList = Http::post('http://api.tektravels.com/SharedServices/StaticData.svc/rest/GetDestinationSearchStaticData', $requestedDataSet)->json();


        if ($CityList["Error"]["ErrorMessage"] == "") {
            $filteredCity = array_values(Arr::where($CityList["Destinations"], function ($value, $key) use ($city) {
                return $value["CityName"] == $city;
            }));

            $childAge = [];

            $requestHotelInfo = [
                "CheckInDate" => $checkInDate,
                "NoOfNights" => $noOfNights,
                "CountryCode" => $request->input("Country"),
                "CityId" => $filteredCity[0]["DestinationId"],
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


            $getResults = Http::post('http://api.tektravels.com/BookingEngineService_Hotel/hotelservice.svc/rest/GetHotelResult/', $requestHotelInfo)->json();

            return response()->json([
                'status' => 200,
                'hoteldataset' => $getResults
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'error_message' => "Error !. Hotels Can't Be Search. Please Change the Location."
            ]);
        }
    }


    public function ahsRoomAllocation(Request $request, $id, $provider)
    {
        $checkInDate = $request->input('CheckInDate');
        $noOfNights = $request->input('NoOfNights');
        $noOfRooms = $request->input('NoOfRooms');
        $noOfAdults = $request->input('NoOfAdults');
        $noOfChild = $request->input('NoOfChild');
        $childAges = $request->input('childAges');
        $cityId = $request->input('cityId');


        $cwb_age = explode("-", "6-12");
        $cnb_age = explode("-", "1-6");

        $childAges = explode(",", $childAges);


        $cnb_array = [];
        $cwb_array = [];

        foreach ($childAges as $key => $age) {
            if ($age > $cwb_age[0] && $age < $cwb_age[1]) {
                $cwb_array[] = $age;
            } else if ($age > $cnb_age[0] && $age < $cnb_age[1]) {
                $cnb_array[] = $age;
            }
        }

        $cnb_count = count($cnb_array);
        $cwb_count = count($cwb_array);


        if ($noOfChild == 0) {
            if ($noOfAdults == 1) {
                $allocatedRoom =  "SGL";
            } else if ($noOfAdults == 2) {
                $allocatedRoom =  "DBL";
            } else if ($noOfAdults == 3) {
                $allocatedRoom = "TPL";
            }
        } else {
            if ($noOfAdults == 1 && $cnb_count == 1) {
                $allocatedRoom = "SGL";
            } else if ($noOfAdults == 1 && $cnb_count == 1 && $cwb_count == 1) {
                $allocatedRoom = "DBL";
            } else if ($noOfAdults == 1 && $cwb_count == 2) {
                $allocatedRoom = "DBL";
            } else if ($noOfAdults == 2 && $cnb_count == 1 && $cwb_count == 1) {
                $allocatedRoom = "DBL";
            } else if ($noOfAdults == 2 && $cwb_count == 2) {
                $allocatedRoom = "";
            } else if ($noOfAdults == 2 && $cwb_count == 1) {
                $allocatedRoom = "DBL";
            } else if ($noOfAdults == 2 && $cnb_count == 1) {
                $allocatedRoom = "DBL";
            } else if ($noOfAdults == 3 && $cwb_count == 1) {
                $allocatedRoom = "QDP";
            }
        }


        return $allocatedRoom;
    }


    public function updateHotelStatusCart(Request $request)
    {
        $hotels_pre_id = $request->input("hotels_pre_id");
        $oid = $request->input("oid");
        $currency = $request->input("currency");
        $userID = $request->input("userId");

        $total_price = $request->input("total_price");

        HotelPreBooking::where('prebooking_id', $hotels_pre_id)->update(['status' => "Booked"]);
        $hotelPreBooking = HotelPreBooking::where('prebooking_id', $hotels_pre_id)->first();

        $hotelPreBookingDataSet = json_decode($hotelPreBooking->bookingdataset);



        MainCheckout::create([
            'checkout_id' => $oid,
            'essnoness_id' => null,
            'lifestyle_id' => null,
            'education_id' => null,
            'hotel_id' => $hotelPreBooking->hotel_id,
            'flight_id' => null,
            'main_category_id' => '4',
            'quantity' => null,
            'each_item_price' => null,
            'total_price' => $total_price,
            'discount_price' => 0.00,
            'bogof_item_name' => null,
            'delivery_charge' => null,
            'discount_type' => null,
            'child_rate' => $total_price,
            'adult_rate' => $total_price,
            'discountable_child_rate' => null,
            'discountable_adult_rate' => null,
            'flight_trip_type' => null,
            'flight_total_price' => null,
            'related_order_id' => $hotels_pre_id,
            'currency' => $currency,
            'status' => 'CustomerOrdered',
            'delivery_status' => null,
            'delivery_date' => null,
            'delivery_address' => null,
            'cx_id' => $userID,
        ]);
    }


    public function getHotelRates(Request $request, $id)
    {
        $roomCategory = $request->input("room_category");
        // $selected_meals = explode(",", $request->input("meals"));
        $check_in = $request->input("check_in");
        $check_out = $request->input("check_out");
        $mealAllocation = $request->input("mealAllocation");

        $room_count_dataset = $request->input("room_count");
        $room_types = [];



        if ($room_count_dataset['Single'] > 0) {
            $room_types[] = "Single";
        }
        if ($room_count_dataset['Double'] > 0) {
            $room_types[] = "Double";
        }
        if ($room_count_dataset['Triple'] > 0) {
            $room_types[] = "Triple";
        }
        if ($room_count_dataset['Quad'] > 0) {
            $room_types[] = "Quad";
        }

        // return $room_types;


        $date_period = CarbonPeriod::create($check_in, $check_out);

        $total_adult_rate = 0.00;
        $total_child_rate = 0.00;

        $baseCurrency = "USD";

        $dailyInventoryAvailability = [];
        $rateIds = [];

        foreach ($room_types as $room_type) {
            foreach ($date_period as $date) {

                $dateVal = $date->format('Y-m-d');


                $dailyInventoryAvailability[] = HotelRoomDailyInventory::where("room_category_id", $roomCategory)->where('date', $dateVal)->where('balance', '>=', $room_count_dataset[$room_type])->first();
            }
        }

        if (in_array(null, $dailyInventoryAvailability)) {
            return response()->json([
                'status' => 401,
                'message' => "No Any Rooms Available for This Criteria"
            ]);
        } else {
            foreach ($room_types as $room_type) {
                foreach ($date_period as $date) {

                    $dateVal = $date->format('Y-m-d');

                    $hotelRoomRate = HotelRoomRate::where("room_category_id", $roomCategory)
                        ->where("booking_start_date", "<=", $dateVal)
                        ->where("booking_end_date", ">=", $dateVal)
                        ->where('hotel_id', $id)
                        ->where('room_type_id', $room_type)
                        ->where('meal_plan', $mealAllocation[$dateVal])
                        ->first();

                    $hotelRoomRates[$room_type][] = $hotelRoomRate;

                    $baseCurrency = $hotelRoomRate->currency;
                }


                foreach ($hotelRoomRates[$room_type] as $hotelRoomRate) {
                    if ($hotelRoomRate != null) {
                        $total_adult_rate += $hotelRoomRate->adult_rate * $room_count_dataset[$room_type];
                        $total_child_rate += $hotelRoomRate->child_with_bed_rate * $room_count_dataset[$room_type];
                        $rateIds[] = $hotelRoomRate->id;
                    } else {
                        return "No Any Rooms Available for This Criteria";
                    }
                    // $child_with_bed_rate = $hotelRoomRate->child_without_bed_rate;
                }
            }


            $hotelRoomDetails = [
                [
                    "RoomTypeName" => $roomCategory . " Room",
                    "Inclusions" => [],
                    "CancellationPolicy" => "",
                    "RateIDs" => implode("|", $rateIds),
                    "Price" => [
                        "PublishedPrice" => $total_adult_rate,
                        "CurrencyCode" => $baseCurrency
                    ]
                ]
            ];

            return response()->json([
                'status' => 200,
                'HotelRoomsDetails' => $hotelRoomDetails
            ]);
        }
    }



    public function hotelsDetails(Request $request, $id, $provider, $status)
    {

        $checkInDate = $request->input('CheckInDate');
        $noOfNights = $request->input('NoOfNights');
        $noOfRooms = $request->input('NoOfRooms');
        $noOfAdults = $request->input('NoOfAdults');
        $noOfChild = $request->input('NoOfChild');
        $cityId = $request->input('cityId');

        if ($noOfChild > 0) {
            $childAge = explode(',', $request->input('ChildAge'));
        } else {
            $childAge = null;
        }


        if ($provider == "hotelAhs") {

            return response()->json([
                'status' => 200,
                'hotelData' => HotelMeta::where("ahs_HotelId", $id)->first()
            ]);
        } else {
            $response = $this->generateTBOToken($request);

            if ($cityId == "") {
                $cityId = HotelMeta::where("hotelCode", $id)->first()->city_code;
            }


            $requestHotelInfo = [
                "CheckInDate" => $checkInDate,
                "NoOfNights" => $noOfNights,
                "CountryCode" => "LK",
                "CityId" => $cityId,
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

            $hotelPreID = $request->input("hotelPreID");
            $preBookingDataSet = DB::table('hotel_prebooking')
                ->where('hotel_prebooking.prebooking_id', $hotelPreID)
                ->get();


            $originFromDB = json_decode($preBookingDataSet[0]->bookingdataset, true);

            $response = $this->generateTBOToken($request);

            $hotelMainResponse = $originFromDB['hotelMainRequest'];

            $hotelRatesResponse = $originFromDB['hotelRatesRequest'];


            $hotelBlockRoom = ([
                'ResultIndex' => $hotelMainResponse["hotelData"]["ResultIndex"],
                'HotelCode' => $hotelMainResponse["hotelData"]["HotelCode"],
                'HotelName' => $hotelMainResponse["hotelData"]["HotelName"],
                'GuestNationality' => "IN",
                'NoOfRooms' => (string)$originFromDB["NoOfRooms"],
                'ClientReferenceNo' => "0",
                'IsVoucherBooking' => "true",
                'HotelRoomsDetails' => [[
                    'RoomIndex' => (string)$hotelRatesResponse["RoomIndex"],
                    'RoomTypeCode' => $hotelRatesResponse["RoomTypeCode"],
                    'RoomTypeName' => $hotelRatesResponse["RoomTypeName"],
                    'RatePlanCode' => $hotelRatesResponse["RatePlanCode"],
                    'BedTypeCode' => null,
                    'SmokingPreference' => 0,
                    'Supplements' => null,
                    'Price' => [
                        "CurrencyCode" => (string)$hotelRatesResponse["Price"]["CurrencyCode"],
                        "RoomPrice" => (string)$hotelRatesResponse["Price"]["RoomPrice"],
                        "Tax" => (string)$hotelRatesResponse["Price"]["Tax"],
                        "ExtraGuestCharge" => (string)$hotelRatesResponse["Price"]["ExtraGuestCharge"],
                        "ChildCharge" => (string)$hotelRatesResponse["Price"]["ChildCharge"],
                        "OtherCharges" => (string)$hotelRatesResponse["Price"]["OtherCharges"],

                        "Discount" => (string)$hotelRatesResponse["Price"]["Discount"],
                        "PublishedPrice" => (string)$hotelRatesResponse["Price"]["PublishedPrice"],
                        "PublishedPriceRoundedOff" => (string)$hotelRatesResponse["Price"]["PublishedPriceRoundedOff"],
                        "OfferedPrice" => (string)$hotelRatesResponse["Price"]["OfferedPrice"],
                        "OfferedPriceRoundedOff" => (string)$hotelRatesResponse["Price"]["OfferedPriceRoundedOff"],
                        "AgentCommission" => (string)$hotelRatesResponse["Price"]["AgentCommission"],
                        "AgentMarkUp" => (string) $hotelRatesResponse["Price"]["AgentMarkUp"],
                        "TDS" => (string)$hotelRatesResponse["Price"]["TDS"],
                    ],
                ]],
                'EndUserIp' => $request->ip(),
                'TokenId' => $response,
                'TraceId' => $originFromDB["traceId"],
            ]);

            // return $hotelBlockRoom;

            // $blockRoom = Http::post('http://api.tektravels.com/BookingEngineService_Hotel/hotelservice.svc/rest/BlockRoom', $hotelBlockRoom)->json();
            $blockRoom = Http::post('http://api.tektravels.com/BookingEngineService_Hotel/hotelservice.svc/rest/BlockRoom', $hotelBlockRoom)->json();


            return $blockRoom;
        } catch (Exception $e) {
            return $e;
        }
    }
}
