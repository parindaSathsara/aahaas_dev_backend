<?php

namespace App\Http\Controllers\Hotels\AppleHolidays;

use App\Http\Controllers\Controller;
use App\Models\Hotels\HotelResevation;
use App\Models\Hotels\HotelResevationChildDetail;
use App\Models\Hotels\HotelResevationPayment;
use App\Models\Hotels\HotelRoomDetails;
use App\Models\Hotels\HotelsPreBookings;
use App\Models\Hotels\ResevationMealDetail;
use App\Models\Hotels\ResevationServiceType;
use App\Models\Hotels\ResevationTraverllerDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class BookingController extends Controller
{
    // ***** Checking Availability on Apple Hotels ***** //
    public function checkingAvailability(Request $request, $id)
    {
        // $arrayUnique
        $meals = explode(',', $request->input('mealTypeList'));
        $serviceTypeList = explode(',', $request->input('serviceTypeList'));

        $datesInRangeMeals = explode(',', $request->input('datesInRangeMeals'));
        $datesInRangeServices = explode(',', $request->input('datesInRangeServices'));
        // return $serviceTypeList;
        // $serviceTypeList = $request['serviceTypeList'];
        // return $meals;

        $meals = array_unique($meals);


        $serviceTypeListUnique = array_unique($serviceTypeList);


        $TodayDate = \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d');
        $CheckIn = date('Y-m-d', strtotime($request->input('checkInDate')));
        $CheckOut = date('Y-m-d', strtotime($request->input('checkOutDate')));
        $AdultCount = (int)$request->input('no_of_adults');
        $ChildCount = (int)$request->input('no_of_childs');
        $TotalPax = (int)($AdultCount + $ChildCount);
        $RoomCategory = $request->input('roomCategory');
        $ChildAge = explode(',', $request->input('childAges'));

        $ageSum = array_sum($ChildAge);
        $ageArray = array();
        // $finalAge = array();
        $childBoardType = '';

        $childBoard = array();

        if (count($ChildAge) >= 1) {
            foreach ($ChildAge as $age) {
                if ($age > 3 && $age <= 5) {
                    $childBoard['age'] = '4-5';
                } else if ($age >= 6 && $age <= 11) {
                    $childBoard['age'] = '6-11';
                }
            }
        }

        // return $childBoard;

        $current_timestamp = Carbon::now()->timestamp;
        $rateKey = $current_timestamp . $TodayDate;
        $x_sig = hash('sha256', $rateKey, true);
        $test_key = bin2hex($x_sig);


        try {
            $TodayDate = \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d');
            $CheckIn = date('Y-m-d', strtotime($request->input('checkInDate')));
            $CheckOut = date('Y-m-d', strtotime($request->input('checkOutDate')));
            $AdultCount = (int)$request->input('no_of_adults');
            $ChildCount = (int)$request->input('no_of_childs');
            $TotalPax = (int)($AdultCount + $ChildCount);
            $RoomCategory = $request->input('roomCategory');
            $ChildAge = explode(',', $request->input('childAges'));
            $MealType = $request['meal'];
            $ServiceType = $request['service'];



            // return $serviceTypeList;

            $ageSum = array_sum($ChildAge);
            $ageArray = array();
            // $finalAge = array();
            $childBoardType = '';

            $childBoard = array();

            if (count($ChildAge) >= 1) {
                foreach ($ChildAge as $age) {
                    if ($age > 3 && $age <= 5) {
                        $childBoard['age'] = '4-5';
                    } else if ($age >= 6 && $age <= 11) {
                        $childBoard['age'] = '6-11';
                    }
                }
            }


            $current_timestamp = Carbon::now()->timestamp;
            $rateKey = $current_timestamp . $TodayDate;
            $x_sig = hash('sha256', $rateKey, true);
            $test_key = bin2hex($x_sig);



            $inventory_query = DB::table('tbl_hotel_inventory')
                ->where('hotel_id', '=', $id)
                ->where('allotment', '>', 0)->select('allotment')->get()->count();

            // return $inventory_query;


            $additionalRates = DB::table('tbl_hotel')
                ->where('tbl_hotel.id', $id)
                ->where('tbl_hotel_room_rate.travel_startdate', '<=', $CheckIn)
                ->where('tbl_hotel_inventory.max_adult_occupancy', '<=', $AdultCount)
                ->where('tbl_hotel_room_rate.child_withno_bed_age', '=', '4-5')->where('tbl_hotel_inventory.room_category', $RoomCategory)
                ->join('tbl_hotel_room_rate', 'tbl_hotel.id', '=', 'tbl_hotel_room_rate.hotel_id')->join('tbl_hotel_inventory', 'tbl_hotel.id', '=', 'tbl_hotel_inventory.hotel_id')
                ->select(
                    'tbl_hotel_room_rate.*',

                )->orderBy('tbl_hotel_room_rate.special_rate', 'DESC')->get();


            $sql_query = [];
            if ($inventory_query > 0) {

                $sql_query = DB::table('tbl_hotel')
                    ->where('tbl_hotel.id', $id)
                    ->where('tbl_hotel_room_rate.travel_startdate', '<=', $CheckIn)
                    ->where('tbl_hotel_inventory.max_adult_occupancy', '<=', $AdultCount)
                    ->whereIn('tbl_hotel_room_rate.meal_plan', $meals)
                    // ->where('tbl_hotel_room_rate.meal_plan', '=', $MealType)
                    // ->where('tbl_hotel_room_rate.service', '=', $serviceTypeList[0])
                    // ->where('tbl_hotel_room_rate.service_type', '=', $ServiceType)
                    ->where('tbl_hotel_room_rate.child_withno_bed_age', '=', '4-5')->where('tbl_hotel_inventory.room_category', $RoomCategory)
                    ->join('tbl_hotel_room_rate', 'tbl_hotel.id', '=', 'tbl_hotel_room_rate.hotel_id')->join('tbl_hotel_inventory', 'tbl_hotel.id', '=', 'tbl_hotel_inventory.hotel_id')
                    ->select(
                        'tbl_hotel.hotel_name',
                        'tbl_hotel_room_rate.travel_startdate',
                        'tbl_hotel_room_rate.travel_enddate',
                        'tbl_hotel_room_rate.room_type',
                        'tbl_hotel_room_rate.meal_plan',
                        'tbl_hotel_room_rate.Market_nationality',
                        'tbl_hotel_room_rate.currency',
                        'tbl_hotel_room_rate.adult_rate',
                        'tbl_hotel_room_rate.child_withbed_rate',
                        'tbl_hotel_room_rate.child_withoutbed_rate',
                        'tbl_hotel_inventory.room_category',
                        'tbl_hotel_inventory.max_adult_occupancy',
                        'tbl_hotel_inventory.max_child_occupancy',
                        'tbl_hotel_inventory.min_child_occupancy',
                        'tbl_hotel_inventory.id AS Inventory ID',
                        'tbl_hotel_inventory.room_type AS InvenRoomType',
                        'tbl_hotel_room_rate.service_type',
                        'tbl_hotel_room_rate.service',
                        'tbl_hotel_room_rate.package_addpax_rate',
                        'tbl_hotel_room_rate.package_childwith_bed_rate',
                        'tbl_hotel_room_rate.package_childno_bed_rate',
                        'tbl_hotel_room_rate.child_foc_age',
                        'tbl_hotel_room_rate.child_withno_bed_age',
                        'tbl_hotel_room_rate.child_withbed_age',
                        'tbl_hotel_room_rate.special_rate',
                        'tbl_hotel_room_rate.id AS rate_id',
                    )->orderBy('tbl_hotel_room_rate.special_rate', 'DESC')->get();





                // return $otherServices;

                $hotelArray = [];
                $subArray = [];
                $hotelArrayBB = [];
                $hotelArrayHB = [];
                $hotelArrayFB = [];


                // return $otherServices;




                foreach ($sql_query as $hotelData) {
                    $mealPlan = $hotelData->meal_plan;
                    if ($mealPlan == "HB") {
                        $hotelArrayHB[] = $hotelData;
                    } else if ($mealPlan == "FB") {
                        $hotelArrayFB[] = $hotelData;
                    } else if ($mealPlan == "BB") {
                        $hotelArrayBB[] = $hotelData;
                    }
                }



                if (in_array("HB", $meals) && in_array("BB", $meals) && in_array("FB", $meals)) {
                    foreach ($hotelArrayHB as $hbData) {
                        foreach ($hotelArrayFB as $fbData) {
                            foreach ($hotelArrayBB as $bbData) {
                                // if($fbData->)
                                $hotelArray[] = [$fbData, $hbData, $bbData];
                            }
                        }
                    }
                } else if (in_array("HB", $meals) && in_array("BB", $meals)) {
                    foreach ($hotelArrayHB as $hbData) {
                        foreach ($hotelArrayBB as $bbData)
                            $hotelArray[] = [$hbData, $bbData];
                    }
                } else if (in_array("HB", $meals) && in_array("FB", $meals)) {
                    foreach ($hotelArrayHB as $hbData) {
                        foreach ($hotelArrayFB as $fbData) {
                            $hotelArray[] = [$fbData, $hbData];
                        }
                    }
                } else if (in_array("FB", $meals) && in_array("BB", $meals)) {

                    foreach ($hotelArrayFB as $fbData) {
                        foreach ($hotelArrayBB as $bbData) {
                            $hotelArray[] = [$fbData, $bbData];
                        }
                    }
                } else if (count($meals) == 1) {
                    // return $hotelArray;
                    $hotelArray = $sql_query;
                }

                $hotelFinalDataSet = [];



                // var totalAdultRate = hotelData['adult_rate'] * hotelsCustomerData.no_of_adults

                // console.log(hotelsCustomerData.no_of_childs)
                // var childWithNoBedAges = hotelData['child_withno_bed_age']?.split('-')


                // var childRates = 0.00;

                // console.log(childAges)
                // for (let index = 1; index <= childCounter; index++) {
                //     // arrayChild.push(childAges['child' + index])

                //     var age = childAges['child' + index]

                //     if (age >= childWithNoBedAges[0] && age <= childWithNoBedAges[1]) {
                //         childRates = childRates + hotelData['child_withoutbed_rate']
                //     }
                //     else {
                //         childRates = childRates + hotelData['child_withbed_rate']
                //     }
                // }
                // var start = moment(hotelsCustomerData.checkInDate);
                // var end = moment(hotelsCustomerData.checkOutDate);

                // var daysCount = end.diff(start, "days")

                // return (totalAdultRate + childRates) * daysCount


                $otherServices = DB::table('tbl_hotel_aahaas_services')
                    ->whereIn('tbl_hotel_aahaas_services.id', $serviceTypeListUnique)
                    ->get();

                $otherServicesDataSet = [];

                // return $otherServices;

                foreach ($serviceTypeList as $service) {
                    foreach ($otherServices as $servicesDataSet) {
                        if ($service == $servicesDataSet->id) {
                            $otherServicesDataSet[] = $servicesDataSet;
                        }
                    }
                }
                $childRatesServices = 0.00;
                $adultRateServices = 0.00;

                $cnb_rate = 0.00;
                $cwb_rate = 0.00;


                $adultRateServicesArr = [];

                $ages = [];

                foreach ($otherServicesDataSet as $serviceDataSet) {
                    $childWithNoBedAges = explode('-', $serviceDataSet->cnb_age);
                    // return $childWithNoBedAges[0];

                    $adultRateServices = $adultRateServices + $serviceDataSet->adult_rate;
                    $adultRateServicesArr[] = $serviceDataSet->adult_rate;

                    for ($i = 0; $i < $ChildCount; $i++) {
                        $age = $ChildAge[$i];

                        // return $age;


                        if ($age >= $childWithNoBedAges[0] && $age <= $childWithNoBedAges[1]) {
                            $cnb_rate = $serviceDataSet->cnb_rate;
                            $childRatesServices = $childRatesServices + $serviceDataSet->cnb_rate;
                            // return $serviceDataSet->cnb_rate;
                            // $ages[]=$serviceDataSet->cnb_rate;
                        } else {
                            $cwb_rate = $serviceDataSet->cwb_rate;
                            $childRatesServices = $childRatesServices + $serviceDataSet->cwb_rate;
                            // $ages[]=$serviceDataSet->cwb_rate;
                        }
                    }
                }

                $otherServicesTotalAmount = $childRatesServices + ($adultRateServices * $AdultCount);
                // return $total_amount;


                if (count($meals) == 1) {
                    // $hotelFinalDataSet = $hotelArray;

                    foreach ($hotelArray as $hotelFinal) {

                        $childWithNoBedAges = explode('-', $hotelFinal->child_withno_bed_age);
                        // return $childWithNoBedAges;
                        $childRates = 0.00;

                        for ($i = 0; $i < $ChildCount; $i++) {
                            $age = $ChildAge[$i];


                            if ($age >= $childWithNoBedAges[0] && $age <= $childWithNoBedAges[1]) {
                                $childRates = $childRates + $hotelFinal->child_withoutbed_rate;
                            } else {
                                $childRates = $childRates + $hotelFinal->child_withbed_rate;
                            }
                            // return $childWithNoBedAges[0]
                        }


                        // return count(array_unique($datesInRangeMeals));

                        $totalAmount = (($childRates + ($hotelFinal->adult_rate * $AdultCount))) * count(array_unique($datesInRangeMeals)) + $otherServicesTotalAmount;
                        $mealPerPriceArray = [];

                        for ($i = 0; $i < count(array_unique($datesInRangeMeals)); $i++) {
                            $mealPerPriceArray[] = $hotelFinal->adult_rate;
                        }

                        $hotelFinalDataSet[] = [
                            'rate_id' => $hotelFinal->rate_id,
                            'currency' => $hotelFinal->currency,
                            'adult_rate' => $hotelFinal->adult_rate,
                            'child_withbed_rate' => $hotelFinal->child_withbed_rate,
                            'child_withoutbed_rate' => $hotelFinal->child_withoutbed_rate,
                            'child_withno_bed_age' => $hotelFinal->child_withno_bed_age,
                            'child_withbed_age' => $hotelFinal->child_withbed_age,
                            'child_foc_age' => $hotelFinal->child_foc_age,
                            'meal_plan' => $hotelFinal->meal_plan,
                            'InvenRoomType' => $hotelFinal->InvenRoomType,
                            'room_category' => $hotelFinal->room_category,
                            'mealNames' => $hotelFinal->meal_plan,
                            'total_adult_rate' => ($hotelFinal->adult_rate * $AdultCount),
                            'per_adult_rate' => ($hotelFinal->adult_rate + $adultRateServices),

                            'meal_per_price' => implode(',', $mealPerPriceArray),
                            'service_per_price' => implode(',', $adultRateServicesArr),

                            'per_cwb' => $hotelFinal->child_withbed_rate + $cwb_rate,
                            'per_cnb' => $hotelFinal->child_withoutbed_rate + $cnb_rate,

                            'total_amount' => $totalAmount,
                            'otherServices' => $request->input('serviceTypeList'),
                            'otherServicesList' => $otherServicesDataSet,
                        ];
                    }
                } else {
                    foreach ($hotelArray as $hotelData) {
                        foreach ($hotelData as $hotelFinal) {
                            $dataSet[] = $hotelFinal;
                            $rateKeyData[] = $hotelFinal->rate_id;
                            $adultRate[] = $hotelFinal->adult_rate;
                            $childWithBedRate[] = $hotelFinal->child_withbed_rate;
                            $childWithoutBedRate[] = $hotelFinal->child_withoutbed_rate;
                            $mealNames[] = $hotelFinal->meal_plan;

                            if ($hotelFinal->meal_plan == "BB") {
                                $mealPlanData[] = "Bed & Breakfast";
                            } else if ($hotelFinal->meal_plan == "FB") {
                                $mealPlanData[] = "Full Board";
                            } else if ($hotelFinal->meal_plan == "RO") {
                                $mealPlanData[] = "Room Only";
                            } else if ($hotelFinal->meal_plan == "HB") {
                                $mealPlanData[] = "Half Board";
                            }
                        }

                        $childWithNoBedAges = explode('-', $hotelFinal->child_withno_bed_age);
                        // return $childWithNoBedAges;
                        $childRates = 0.00;

                        for ($i = 0; $i < $ChildCount; $i++) {
                            $age = $ChildAge[$i];


                            if ($age >= $childWithNoBedAges[0] && $age <= $childWithNoBedAges[1]) {
                                $childRates = $childRates + $hotelFinal->child_withoutbed_rate;
                            } else {
                                $childRates = $childRates + $hotelFinal->child_withbed_rate;
                            }
                            // return $childWithNoBedAges[0]
                        }

                        $mealPerPriceArray = [];
                        for ($i = 0; $i < count(array_unique($datesInRangeMeals)); $i++) {
                            $mealPerPriceArray[] = array_sum($adultRate) / count($mealNames);
                        }

                        // return count(array_unique($datesInRangeMeals));

                        $totalAmount = (($childRates + (array_sum($adultRate) * $AdultCount)) * count(array_unique($datesInRangeMeals))) + $otherServicesTotalAmount;


                        $hotelFinalDataSet[] = [
                            'rate_id' => implode(",", $rateKeyData),
                            'currency' => $dataSet[0]->currency,
                            'adult_rate' => array_sum($adultRate),
                            'child_withbed_rate' => array_sum($childWithBedRate),
                            'child_withoutbed_rate' => array_sum($childWithoutBedRate),
                            'child_withno_bed_age' => $dataSet[0]->child_withno_bed_age,
                            'child_withbed_age' => $dataSet[0]->child_withbed_age,
                            'child_foc_age' => $dataSet[0]->child_foc_age,
                            'meal_plan' => implode(',', $mealPlanData),
                            'InvenRoomType' => $dataSet[0]->InvenRoomType,
                            'room_category' => $dataSet[0]->room_category,
                            'mealNames' => implode(",", $mealNames),
                            'total_adult_rate' => array_sum($adultRate) * $AdultCount,
                            'per_adult_rate' => $adultRate[0] + $adultRateServices,

                            'meal_per_price' => implode(',', $mealPerPriceArray),
                            'service_per_price' => implode(',', $adultRateServicesArr),


                            'per_cwb' => $dataSet[0]->child_withbed_rate + $cwb_rate,
                            'per_cnb' => $dataSet[0]->child_withoutbed_rate + $cnb_rate,


                            'total_amount' => $totalAmount,
                            'otherServices' => $request->input('serviceTypeList'),
                            'otherServicesList' => $otherServicesDataSet,

                        ];
                        $dataSet = [];
                        $rateKeyData = [];
                        $adultRate = [];
                        $childWithBedRate = [];
                        $childWithoutBedRate = [];
                        $mealPlanData = [];
                        $mealNames = [];
                        $childRates = 0.00;
                        $mealPerPriceArray = [];
                    }
                }


                return response()->json([
                    'status' => 200,
                    'query_result' => $hotelFinalDataSet,
                    'rateKey' => $test_key,
                    'additionalRates' => $additionalRates,
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'query_result' => 'No allotments available for selected hotel'
                ]);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }


    public function validateHotelBooking(Request $request)
    {
        $id = $request->input('hotels_pre_id');
        $status = $request->input('status');

        // return $id;
        HotelsPreBookings::where('booking_id', $id)->update(['cartStatus' => $status]);

        $hotelPreBooking = DB::table('tbl_hotels_pre_booking')->where('booking_id', '=', $id)->get();

        $hotelDataSet = $hotelPreBooking[0];

        $rateKeyData = $hotelDataSet->rate_key;

        $rateKeyData = json_decode($rateKeyData);

        $id = $request->input('hotels_pre_id');
        $status = $request->input('status');

        // return $id;
        HotelsPreBookings::where('booking_id', $id)->update(['cartStatus' => $status]);

        $hotelPreBooking = DB::table('tbl_hotels_pre_booking')->where('booking_id', '=', $id)->get();

        $hotelDataSet = $hotelPreBooking[0];

        $rateKeyData = $hotelDataSet->rate_key;

        $rateKeyData = json_decode($rateKeyData);



        $request = $rateKeyData;


        return response()->json([
            'status' => 200,
            'hotelAahaas' => $this->confirmCartBookingApple($request, $hotelDataSet->ref_id)
        ]);
    }

    public function confirmCartBookingApple($request, $id)
    {

        // return $request;

        $sql_query = DB::table('tbl_hotel')->where('tbl_hotel.id', $id)
            ->join('tbl_hotel_terms_conditions', 'tbl_hotel.id', '=', 'tbl_hotel_terms_conditions.hotel_id')
            ->join('tbl_hotel_vendor', 'tbl_hotel.id', '=', 'tbl_hotel_vendor.hotel_id')
            ->select('tbl_hotel.hotel_name', 'tbl_hotel.hotel_address', 'tbl_hotel_vendor.hotel_email', 'tbl_hotel_terms_conditions.cancellation_deadline AS Days_Count')->first();



        $current_timestamp = Carbon::now()->timestamp;
        $randNumber = rand(2, 50);
        $bookingRef = "AHBK_" . $current_timestamp . "_" . $randNumber;

        // return $request->rate_key;
        $rate_Key = $request->rate_key;
        $CheckIn = \Carbon\Carbon::parse($request->check_in);
        $CheckOut = \Carbon\Carbon::parse($request->check_out);
        $TodayDate = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $HotelName = $request->hotel_id;
        $HolderFName = $request->holderFirstName;
        $HolderLName = $request->holderLastName;
        $BookingReference = $bookingRef;
        $ResevationName = $HolderFName . ' ' . $HolderLName;
        $NoOfAdults = $request->no_of_adults;
        $NoOfChilds = $request->no_of_childs;
        $AdultRate = $request->adult_rate;
        $ChildWithBed = $request->child_withbed_rate;
        $ChildNoBed = $request->child_withoutbed_rate;
        $BedType = $request->bed_type;
        $RoomType = $request->room_type;
        $RoomCount = $request->room_count;
        $BoardCode = $request->board_code;
        $SpecialRemarks = $request->special_remarks;
        $ResevationPlatform = 'Aahaas';
        $BookingRemarks = $request->special_remarks;
        $Status = 'Pending';
        $Currency = 'USD';
        $CancellationDays = $sql_query->Days_Count;
        $CancellationDeadline = $CheckIn->subDays($CancellationDays);

        // ######################################################### //

        $Child_Age = explode(',', $request->child_age);
        $childagearray = array();

        $count = 0;

        if (count($Child_Age) >= 1) {
            foreach ($Child_Age as $age) {
                if ($age > 3 && $age <= 5) {
                    $childagearray[] = '4-5';
                } else if ($age >= 6 && $age <= 11) {
                    $childagearray[] = '6-11';
                }
            }

            $count = array_count_values($childagearray);
        }

        $ChildNoBedCount = 0;
        $ChildWithBedCount = 0;

        if (in_array('4-5', $childagearray)) {
            $ChildNoBedCount = $count['4-5'];
        }
        if (in_array('6-11', $childagearray)) {
            $ChildWithBedCount = $count['6-11'];
        }

        // return $ChildWithBedCount;

        // **** Meal Types **** //

        $MealType = explode(',', $request->meal_type);
        $MealDate = explode(',', $request->meal_date);
        $MealAdult = explode(',', $request->meal_adults);
        $MealChild = explode(',', $request->meal_childs);
        $MealSpReq = explode(',', $request->meal_Sp_req);
        $MealUnitPrice = explode(',', $request->meal_unit_price);

        // **** Service Types **** //
        $ServiceType = explode(',', $request->service_type);
        $ServiceDate = explode(',', $request->service_date);
        $ServiceAdult = explode(',', $request->service_adult);
        $ServiceChild = explode(',', $request->service_child);
        $ServiceUnitPrice = explode(',', $request->service_rate);

        // **** Pax Details **** //
        $FirstName = explode(',', $request->first_name);
        $LastName = explode(',', $request->last_name);
        $PaxType = explode(',', $request->pax_type);
        $Price = explode(',', $request->per_price);

        //**** Child Details ****//
        $ChildName = explode(',', $request->child_name);

        $paxDetails = array();
        $serviceDetail = array();
        $mealDetail = array();
        $childAgeDetails = array();

        // $CategoryID = $request->category_id;

        try {

            HotelResevation::create([
                'rate_key' => $rate_Key,
                'resevation_no' => $BookingReference,
                'resevation_name' => $ResevationName,
                'resevation_date' => $TodayDate,
                'hotel_name' => $HotelName,
                'checkin_time' => $CheckIn,
                'checkout_time' => $CheckOut,
                'baby_crib' => '-',
                'no_of_adults' => $NoOfAdults,
                'no_of_childs' => $NoOfChilds,
                'child_withbed' => $ChildWithBedCount,
                'child_nobed' => $ChildNoBedCount,
                'bed_type' => $BedType,
                'room_type' => $RoomType,
                'no_of_rooms' => $RoomCount,
                'board_code' => $BoardCode,
                'special_notice' => $SpecialRemarks,
                'resevation_platform' => $ResevationPlatform,
                'resevation_status' => 'CONFIRMED',
                'currency' => $Currency,
                'cancelation' => 'true',
                'modification' => 'false',
                'cancelation_amount' => '-',
                'cancelation_deadline' => $CancellationDeadline,
                'booking_remarks' => $BookingRemarks,
                'status' => 'NEW',
                'created_at' => $TodayDate,
                'updated_at' => $TodayDate,
                'user_id' => $request->userID
            ]);

            // **** Hotel Resevation Child Details ****//
            if (count($Child_Age) >= 1) {
                for ($cc = 0; $cc < count($Child_Age); $cc++) {
                    $childAgeDetails[] = ['name' => $ChildName[$cc], 'age' => $Child_Age[$cc]];
                }
                foreach ($childAgeDetails as $childAge) {
                    HotelResevationChildDetail::create([
                        'resevation_no' => $BookingReference,
                        'child_name' => $childAge['name'],
                        'child_age' => $childAge['age']
                    ]);
                }
            }

            // **** Payments **** //
            $TotalAmount = (float)$request->total_amount;
            $PaidAmount = (float)$request->paid_amount;
            $BalanceAmount = (float)$request->balance_amount;
            $PaymentMethod = $request->payment_method;

            HotelRoomDetails::create([
                'resevation_no' => $BookingReference,
                'room_code' => $BedType,
                'adult_count' => $NoOfAdults,
                'child_count' => $NoOfChilds,
                'adult_rate' => $AdultRate,
                'child_withbed_rate' => $ChildWithBed,
                'child_nobed_rate' => $ChildNoBed
            ]);


            if (count($MealType) >= 1) {
                for ($c = 0; $c < count($MealType); $c++) {
                    $mealDetail[] = ['type' => $MealType[$c], 'date' => $MealDate[$c], 'adult' => $MealAdult[$c], 'child' => $MealChild[$c], 'sp_req' => $MealSpReq[$c], 'price' => $MealUnitPrice[$c]];
                }

                foreach ($mealDetail as $meal) {
                    ResevationMealDetail::create([
                        'resevation_no' => $BookingReference,
                        'meal_plan' => $meal['type'],
                        'date' => $meal['date'],
                        'adult_count' => $meal['adult'],
                        'child_count' => $meal['child'],
                        'special_request' => $meal['sp_req'],
                        'unit_price' => $meal['price'],
                    ]);
                }
            }


            if (count($ServiceType) >= 1) {
                for ($x = 0; $x < count($ServiceType); $x++) {
                    $serviceDetail[] = ['type' => $ServiceType[$x], 'date' => $ServiceDate[$x], 'adult' => $ServiceAdult[$x], 'child' => $ServiceChild[$x], 'price' => $ServiceUnitPrice[$x]];
                }

                foreach ($serviceDetail as $service) {
                    ResevationServiceType::create([
                        'resevation_no' => $BookingReference,
                        'service_type' => $service['type'],
                        'date' => $service['date'],
                        'adult_count' => $service['adult'],
                        'child_count' => $service['child'],
                        'unit_price' => $service['price'],
                    ]);
                }
            }

            if (count($PaxType) >= 1) {
                for ($y = 0; $y < count($PaxType); $y++) {
                    $paxDetails[] = ['fname' => $FirstName[$y], 'lname' => $LastName[$y], 'type' => $PaxType[$y], 'price' => $Price[$y]];
                }

                foreach ($paxDetails as $pax) {
                    ResevationTraverllerDetail::create([
                        'resevation_no' => $BookingReference,
                        'first_name' => $pax['fname'],
                        'last_name' => $pax['lname'],
                        'type' => $pax['type']
                    ]);
                }
            }

            if ($TotalAmount == $BalanceAmount) {
                HotelResevationPayment::create([
                    'resevation_no' => $BookingReference,
                    'total_amount' => $TotalAmount,
                    'paid_amount' => $PaidAmount,
                    'balance_payment' => $BalanceAmount,
                    'amendment_refund' => 0.00,
                    'payment_method' => $PaymentMethod,
                    'payment_status' => 'PENDING',
                    'booking_status' => 'NEW',
                    'payment_slip_image' => '-'
                ]);
            } else {
                HotelResevationPayment::create([
                    'resevation_no' => $BookingReference,
                    'total_amount' => $TotalAmount,
                    'paid_amount' => $PaidAmount,
                    'balance_payment' => $BalanceAmount,
                    'amendment_refund' => 0.00,
                    'payment_method' => $PaymentMethod,
                    'payment_status' => 'COMPLETED',
                    'booking_status' => 'NEW',
                    'payment_slip_image' => '-'
                ]);
            }

            // DB::select(DB::raw("UPDATE tbl_hotel_inventory SET allotment=allotment-$RoomCount WHERE tbl_hotel_inventory.id=$CategoryID"));


            return $this->sendConfirmationEmail($BookingReference);

            // return response()->json([
            //     'status' => 200,
            //     'message' => 'Booking confirmed! Confirmation e-mail will recieve to your email shortley'
            // ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 501,
                'error_message' => throw $ex
            ]);
        }
    }


    // ***** Booking Confirming AppleHolidays ***** //
    public function confirmBookingApple(Request $request, $id)
    {

        $sql_query = DB::table('tbl_hotel')->where('tbl_hotel.id', $id)
            ->join('tbl_hotel_terms_conditions', 'tbl_hotel.id', '=', 'tbl_hotel_terms_conditions.hotel_id')
            ->join('tbl_hotel_vendor', 'tbl_hotel.id', '=', 'tbl_hotel_vendor.hotel_id')
            ->select('tbl_hotel.hotel_name', 'tbl_hotel.hotel_address', 'tbl_hotel_vendor.hotel_email', 'tbl_hotel_terms_conditions.cancellation_deadline AS Days_Count')->first();



        $current_timestamp = Carbon::now()->timestamp;
        $randNumber = rand(2, 50);
        $bookingRef = "AHBK_" . $current_timestamp . "_" . $randNumber;

        // return $request->input('rate_key');
        $rate_Key = $request->input('rate_key');
        $CheckIn = \Carbon\Carbon::parse($request->input('check_in'));
        $CheckOut = \Carbon\Carbon::parse($request->input('check_out'));
        $TodayDate = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $HotelName = $request->input('hotel_id');
        $HolderFName = $request->input('holderFirstName');
        $HolderLName = $request->input('holderLastName');
        $BookingReference = $bookingRef;
        $ResevationName = $HolderFName . ' ' . $HolderLName;
        $NoOfAdults = $request->input('no_of_adults');
        $NoOfChilds = $request->input('no_of_childs');
        $AdultRate = $request->input('adult_rate');
        $ChildWithBed = $request->input('child_withbed_rate');
        $ChildNoBed = $request->input('child_withoutbed_rate');
        $BedType = $request->input('bed_type');
        $RoomType = $request->input('room_type');
        $RoomCount = $request->input('room_count');
        $BoardCode = $request->input('board_code');
        $SpecialRemarks = $request->input('special_remarks');
        $ResevationPlatform = 'Aahaas';
        $BookingRemarks = $request->input('special_remarks');
        $Status = 'Pending';
        $Currency = 'USD';
        $CancellationDays = $sql_query->Days_Count;
        $CancellationDeadline = $CheckIn->subDays($CancellationDays);

        // ######################################################### //

        $Child_Age = explode(',', $request->input('child_age'));
        $childagearray = array();

        $count = 0;

        if (count($Child_Age) >= 1) {
            foreach ($Child_Age as $age) {
                if ($age > 3 && $age <= 5) {
                    $childagearray[] = '4-5';
                } else if ($age >= 6 && $age <= 11) {
                    $childagearray[] = '6-11';
                }
            }

            $count = array_count_values($childagearray);
        }

        $ChildNoBedCount = 0;
        $ChildWithBedCount = 0;

        if (in_array('4-5', $childagearray)) {
            $ChildNoBedCount = $count['4-5'];
        }
        if (in_array('6-11', $childagearray)) {
            $ChildWithBedCount = $count['6-11'];
        }

        // return $ChildWithBedCount;

        // **** Meal Types **** //

        $MealType = explode(',', $request->input('meal_type'));
        $MealDate = explode(',', $request->input('meal_date'));
        $MealAdult = explode(',', $request->input('meal_adults'));
        $MealChild = explode(',', $request->input('meal_childs'));
        $MealSpReq = explode(',', $request->input('meal_Sp_req'));
        $MealUnitPrice = explode(',', $request->input('meal_unit_price'));

        // **** Service Types **** //
        $ServiceType = explode(',', $request->input('service_type'));
        $ServiceDate = explode(',', $request->input('service_date'));
        $ServiceAdult = explode(',', $request->input('service_adult'));
        $ServiceChild = explode(',', $request->input('service_child'));
        $ServiceUnitPrice = explode(',', $request->input('service_rate'));

        // **** Pax Details **** //
        $FirstName = explode(',', $request->input('first_name'));
        $LastName = explode(',', $request->input('last_name'));
        $PaxType = explode(',', $request->input('pax_type'));
        $Price = explode(',', $request->input('per_price'));

        //**** Child Details ****//
        $ChildName = explode(',', $request->input('child_name'));

        $paxDetails = array();
        $serviceDetail = array();
        $mealDetail = array();
        $childAgeDetails = array();

        $CategoryID = $request->input('category_id');

        try {

            HotelResevation::create([
                'rate_key' => $rate_Key,
                'resevation_no' => $BookingReference,
                'resevation_name' => $ResevationName,
                'resevation_date' => $TodayDate,
                'hotel_name' => $HotelName,
                'checkin_time' => $CheckIn,
                'checkout_time' => $CheckOut,
                'baby_crib' => '-',
                'no_of_adults' => $NoOfAdults,
                'no_of_childs' => $NoOfChilds,
                'child_withbed' => $ChildWithBedCount,
                'child_nobed' => $ChildNoBedCount,
                'bed_type' => $BedType,
                'room_type' => $RoomType,
                'no_of_rooms' => $RoomCount,
                'board_code' => $BoardCode,
                'special_notice' => $SpecialRemarks,
                'resevation_platform' => $ResevationPlatform,
                'resevation_status' => 'CONFIRMED',
                'currency' => $Currency,
                'cancelation' => 'true',
                'modification' => 'false',
                'cancelation_amount' => '-',
                'cancelation_deadline' => $CancellationDeadline,
                'booking_remarks' => $BookingRemarks,
                'status' => 'NEW',
                'created_at' => $TodayDate,
                'updated_at' => $TodayDate,
                'user_id' => $request->input('userID')
            ]);

            // **** Hotel Resevation Child Details ****//
            if (count($Child_Age) >= 1) {
                for ($cc = 0; $cc < count($Child_Age); $cc++) {
                    $childAgeDetails[] = ['name' => $ChildName[$cc], 'age' => $Child_Age[$cc]];
                }
                foreach ($childAgeDetails as $childAge) {
                    HotelResevationChildDetail::create([
                        'resevation_no' => $BookingReference,
                        'child_name' => $childAge['name'],
                        'child_age' => $childAge['age']
                    ]);
                }
            }

            // **** Payments **** //
            $TotalAmount = (float)$request->input('total_amount');
            $PaidAmount = (float)$request->input('paid_amount');
            $BalanceAmount = (float)$request->input('balance_amount');
            $PaymentMethod = $request->input('payment_method');

            HotelRoomDetails::create([
                'resevation_no' => $BookingReference,
                'room_code' => $BedType,
                'adult_count' => $NoOfAdults,
                'child_count' => $NoOfChilds,
                'adult_rate' => $AdultRate,
                'child_withbed_rate' => $ChildWithBed,
                'child_nobed_rate' => $ChildNoBed
            ]);


            if (count($MealType) >= 1) {
                for ($c = 0; $c < count($MealType); $c++) {
                    $mealDetail[] = ['type' => $MealType[$c], 'date' => $MealDate[$c], 'adult' => $MealAdult[$c], 'child' => $MealChild[$c], 'sp_req' => $MealSpReq[$c], 'price' => $MealUnitPrice[$c]];
                }

                foreach ($mealDetail as $meal) {
                    ResevationMealDetail::create([
                        'resevation_no' => $BookingReference,
                        'meal_plan' => $meal['type'],
                        'date' => $meal['date'],
                        'adult_count' => $meal['adult'],
                        'child_count' => $meal['child'],
                        'special_request' => $meal['sp_req'],
                        'unit_price' => $meal['price'],
                    ]);
                }
            }


            if (count($ServiceType) >= 1) {
                for ($x = 0; $x < count($ServiceType); $x++) {
                    $serviceDetail[] = ['type' => $ServiceType[$x], 'date' => $ServiceDate[$x], 'adult' => $ServiceAdult[$x], 'child' => $ServiceChild[$x], 'price' => $ServiceUnitPrice[$x]];
                }

                foreach ($serviceDetail as $service) {
                    ResevationServiceType::create([
                        'resevation_no' => $BookingReference,
                        'service_type' => $service['type'],
                        'date' => $service['date'],
                        'adult_count' => $service['adult'],
                        'child_count' => $service['child'],
                        'unit_price' => $service['price'],
                    ]);
                }
            }

            if (count($PaxType) >= 1) {
                for ($y = 0; $y < count($PaxType); $y++) {
                    $paxDetails[] = ['fname' => $FirstName[$y], 'lname' => $LastName[$y], 'type' => $PaxType[$y], 'price' => $Price[$y]];
                }

                foreach ($paxDetails as $pax) {
                    ResevationTraverllerDetail::create([
                        'resevation_no' => $BookingReference,
                        'first_name' => $pax['fname'],
                        'last_name' => $pax['lname'],
                        'type' => $pax['type']
                    ]);
                }
            }

            if ($TotalAmount == $BalanceAmount) {
                HotelResevationPayment::create([
                    'resevation_no' => $BookingReference,
                    'total_amount' => $TotalAmount,
                    'paid_amount' => $PaidAmount,
                    'balance_payment' => $BalanceAmount,
                    'amendment_refund' => 0.00,
                    'payment_method' => $PaymentMethod,
                    'payment_status' => 'PENDING',
                    'booking_status' => 'NEW',
                    'payment_slip_image' => '-'
                ]);
            } else {
                HotelResevationPayment::create([
                    'resevation_no' => $BookingReference,
                    'total_amount' => $TotalAmount,
                    'paid_amount' => $PaidAmount,
                    'balance_payment' => $BalanceAmount,
                    'amendment_refund' => 0.00,
                    'payment_method' => $PaymentMethod,
                    'payment_status' => 'COMPLETED',
                    'booking_status' => 'NEW',
                    'payment_slip_image' => '-'
                ]);
            }

            // DB::select(DB::raw("UPDATE tbl_hotel_inventory SET allotment=allotment-$RoomCount WHERE tbl_hotel_inventory.id=$CategoryID"));


            return $this->sendConfirmationEmail($BookingReference);

            // return response()->json([
            //     'status' => 200,
            //     'message' => 'Booking confirmed! Confirmation e-mail will recieve to your email shortley'
            // ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 501,
                'error_message' => throw $ex
            ]);
        }
    }


    // **** Confirmation Email **** //
    public function sendConfirmationEmail($bookingId) //$bookingId
    {

        $dataJoinOne = DB::table('tbl_hotel_resevation')
            ->where('tbl_hotel_resevation.resevation_no', $bookingId)
            ->where('tbl_hotel_resevation.status', 'New')
            ->join('users', 'tbl_hotel_resevation.user_id', '=', 'users.id')
            ->join('tbl_hotel_roomdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_roomdetails.resevation_no')
            ->join('tbl_hotel_resevation_payments', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_resevation_payments.resevation_no')
            ->join('tbl_hotel', 'tbl_hotel_resevation.hotel_name', '=', 'tbl_hotel.id')
            ->join('tbl_hotel_vendor', 'tbl_hotel_resevation.hotel_name', '=', 'tbl_hotel_vendor.hotel_id')
            ->select(
                'users.email',
                'tbl_hotel_resevation.*',
                'tbl_hotel_resevation.id AS InoiceId',
                'tbl_hotel_roomdetails.*',
                'tbl_hotel_resevation_payments.*',
                'tbl_hotel.hotel_name',
                'tbl_hotel.hotel_address',
                'tbl_hotel_vendor.hotel_email'
            )->first();

        // return $dataJoinOne; 

        $dataJoinTwo = DB::table('tbl_hotel_travellerdetails')->where('tbl_hotel_travellerdetails.resevation_no', $bookingId)->select('*')->get();
        $dataJoinThree = DB::table('tbl_hotel_resevation')
            ->where('tbl_hotel_resevation.resevation_no', $bookingId)
            ->where('tbl_hotel_resevation.status', 'New')
            ->join('tbl_hotel_mealdetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_mealdetail.resevation_no')
            ->select('*')->get();
        $dataJoinFour = DB::table('tbl_hotel_servicedetail')->where('tbl_hotel_servicedetail.resevation_no', $bookingId)->select('*')->get();



        $detailJoin = DB::table('tbl_hotel_resevation')
            ->where('tbl_hotel_resevation.resevation_no', $bookingId)
            ->where('tbl_hotel_resevation.status', 'New')
            ->join('tbl_hotel_roomdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_roomdetails.resevation_no')
            ->join('tbl_hotel_mealdetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_mealdetail.resevation_no')
            ->join('tbl_hotel_servicedetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_servicedetail.resevation_no')
            ->join('tbl_hotel_travellerdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_travellerdetails.resevation_no')
            ->select(
                'tbl_hotel_roomdetails.adult_count AS AdultCount',
                'tbl_hotel_roomdetails.child_count AS ChildCount',
                'tbl_hotel_mealdetail.meal_plan AS MealPlan',
                'tbl_hotel_mealdetail.adult_count AS MealAdult',
                'tbl_hotel_mealdetail.child_count AS MealChild',
                'tbl_hotel_mealdetail.date AS MealDate',
                'tbl_hotel_mealdetail.special_request AS MealSpeReq',
                'tbl_hotel_mealdetail.unit_price AS MealPrice',
                'tbl_hotel_servicedetail.service_type AS SerType',
                'tbl_hotel_servicedetail.unit_price AS ServicePrice',
                'tbl_hotel_servicedetail.child_count AS SerChildCount',
                'tbl_hotel_servicedetail.date AS SerDate',
                'tbl_hotel_servicedetail.unit_price AS SerPerPrice',
                'tbl_hotel_travellerdetails.type AS PaxType',
                // 'tbl_hotel_reservation.special_notice',
            )->get();

        // return $detailJoin;

        $userEmail = $dataJoinOne->email;

        $invoice_no = $dataJoinOne->InoiceId;
        $resevationNumber = $dataJoinOne->resevation_no;
        $resevation_name = $dataJoinOne->resevation_name;
        $resevation_date = $dataJoinOne->resevation_date;
        $checkin_time = date('Y-m-d', strtotime($dataJoinOne->checkin_time));
        $checkout_time = date('Y-m-d', strtotime($dataJoinOne->checkout_time));
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
        $hotel_address = $dataJoinOne->hotel_address;
        $hotel_email = $dataJoinOne->hotel_email;
        $pax = (int)$no_of_adults + (int)$no_of_childs;

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
            'no_of_rooms' => $no_of_rooms, 'board_code' => $board_code, 'special_notice' => $special_notice, 'resevation_status' => $resevation_status, 'pax_count' => $pax,
            'cancel_dealine' => $cancelation_deadline, 'room_code' => $room_code, 'total_amount' => $total_amount, 'nights' => $nightsCount, 'hotelName' => $hotel_name, 'otherData' => $detailJoin,
            'meal_data' => $dataJoinThree, 'hotelName' => $hotel_name, 'hotelAddress' => $hotel_address, 'hotelEmail' => $hotel_email
        ];

        // return view('Mails.AahaasRecipt', $dataSet);
        // $pdf = Pdf::loadView('pdf_view', $dataSet);
        // $pdf = PDF::loadView('Mails.AahaasRecipt', $dataSet);


        $pdf = app('dompdf.wrapper');
        $pdf->loadView('Mails.AahaasRecipt', $dataSet);
        // return $pdf->download('pdf_file.pdf');

        try {
            $done = Mail::send('Mails.ReciptBody', $dataSet, function ($message) use ($userEmail, $resevationNumber, $pdf) {
                $message->to($userEmail);
                $message->subject('Confirmation Email on your Booking Reference: #' . $resevationNumber . '.');
                $message->attachData($pdf->output(), $resevationNumber . '_' . 'Recipt.pdf', ['mime' => 'application/pdf',]);
            });

            return response()->json([
                'status' => 200,
                'message' => 'Booking Confirmed and Confirmation Mail sent your email'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    // **** Booking Cancellation Request **** //
    public function bookingCancellationRequest(Request $request, $booking_Id)
    {
        try {
            $TodayDate = \Carbon\Carbon::now()->format('Y-m-d');
            $SqlQuery = DB::table('tbl_hotel_resevation')
                ->join('users', 'tbl_hotel_resevation.user_id', '=', 'users.id')
                ->join('tbl_hotel', 'tbl_hotel_resevation.hotel_name', '=', 'tbl_hotel.id')
                ->select('tbl_hotel_resevation.resevation_status', 'tbl_hotel_resevation.cancelation_deadline', 'users.email')
                ->where('resevation_no', $booking_Id)->first();
            $Status = 'CANCELLED';
            $Reason = $request->input('cancel_reason');
            $userEmail = $SqlQuery->email;
            $HotelId = $SqlQuery->hotel_name;

            $QueryHotel = DB::table('tbl_hotel')->where('tbl_hotel.id', $HotelId)
                ->join('tbl_hotel_terms_conditions', 'tbl_hotel.id', '=', 'tbl_hotel_terms_conditions.hotel_id')
                ->select('*')
                ->first();

            $CancellationInDays = $QueryHotel->cancellation_deadline;

            $CancellationDeadline = date('Y-m-d', strtotime($SqlQuery->cancelation_deadline));

            if ($CancellationDeadline <= $TodayDate) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Your given cancellation deadline is over.'
                ]);
            } else {

                $data = ['booking_id' => $booking_Id, 'cancel_ref' => $booking_Id, 'status' => $Status, 'cancel_date' => $TodayDate, 'cancel_reason' => $Reason];

                Mail::send(
                    'Mails.BookingCancel',
                    $data,
                    function ($message) use ($userEmail) {
                        $message->to($userEmail);
                        $message->subject('Booking Cancellation Confirmation');
                    }
                );

                DB::select(DB::raw("UPDATE tbl_hotel_resevation SET resevation_status='$Status' WHERE resevation_no='$booking_Id'"));

                return response()->json([
                    'status' => 200,
                    'message' => '#' . $booking_Id . ' booking canceled successfully'
                ]);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function ammendBooking(Request $request, $id)
    {
        try {

            $NewStatus = 'AMENDMENT';

            DB::select(DB::raw("UPDATE tbl_hotel_resevation SET status='$NewStatus' WHERE resevation_no='$id'")); //$UpdateResevationQuery
            DB::select(DB::raw("UPDATE tbl_hotel_resevation_payments SET booking_status='$NewStatus' WHERE resevation_no='$id'")); //$UpdatePaymentQuery

            // ********** //

            $QueryOne = DB::table('tbl_hotel_resevation')
                ->where('tbl_hotel_resevation.resevation_no', $id)
                ->where('tbl_hotel_resevation.status', $NewStatus)
                ->join('tbl_hotel_resevation_payments', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_resevation_payments.resevation_no')->first();

            $RowCount = DB::table('tbl_hotel_resevation')->where('tbl_hotel_resevation.resevation_no', $id)->count();

            $sql_query = DB::table('tbl_hotel')->where('tbl_hotel.id', $id)
                ->join('tbl_hotel_terms_conditions', 'tbl_hotel.id', '=', 'tbl_hotel_terms_conditions.hotel_id')
                ->join('tbl_hotel_vendor', 'tbl_hotel.id', '=', 'tbl_hotel_vendor.hotel_id')
                ->select('tbl_hotel.hotel_name', 'tbl_hotel.hotel_address', 'tbl_hotel_vendor.hotel_email', 'tbl_hotel_terms_conditions.cancellation_deadline AS Days_Count')->first();

            $rate_Key = $request->input('rate_key');
            $CheckIn = \Carbon\Carbon::parse($request->input('check_in'));
            $CheckOut = \Carbon\Carbon::parse($request->input('check_out'));
            $TodayDate = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $HotelName = $request->input('hotel_id');
            $HolderFName = $request->input('holder_first_name');
            $HolderLName = $request->input('holder_last_name');
            $BookingReference = $id;
            $ResevationName = $HolderFName . ' ' . $HolderLName;
            $NoOfAdults = $request->input('no_of_adults');
            $NoOfChilds = $request->input('no_of_childs');
            $AdultRate = $request->input('adult_rate');
            $ChildWithBed = $request->input('child_withbed_rate');
            $ChildNoBed = $request->input('child_nobed_rate');
            $BedType = $request->input('bed_type');
            $RoomType = $request->input('room_type');
            $RoomCount = $request->input('room_count');
            $BoardCode = $request->input('board_code');
            $SpecialRemarks = $request->input('special_remarks');
            $ResevationPlatform = 'Aahaas';
            $BookingRemarks = $request->input('special_remarks');
            $Status = 'Pending';
            $Currency = 'USD';
            $CancellationDays = $sql_query->Days_Count;
            $CancellationDeadline = $CheckIn->subDays($CancellationDays);

            // ######################################################### //

            $Child_Age = explode(',', $request->input('child_age'));
            $childagearray = array();

            $count = 0;

            if (count($Child_Age) >= 1) {
                foreach ($Child_Age as $age) {
                    if ($age > 3 && $age <= 5) {
                        $childagearray[] = '4-5';
                    } else if ($age >= 6 && $age <= 11) {
                        $childagearray[] = '6-11';
                    }
                }

                $count = array_count_values($childagearray);
            }

            $ChildNoBedCount = 0;
            $ChildWithBedCount = 0;

            if (in_array('4-5', $childagearray)) {
                $ChildNoBedCount = $count['4-5'];
            }
            if (in_array('6-11', $childagearray)) {
                $ChildWithBedCount = $count['6-11'];
            }

            // return $ChildWithBedCount;

            // **** Meal Types **** //

            $MealType = explode(',', $request->input('meal_type'));
            $MealDate = explode(',', $request->input('meal_date'));
            $MealAdult = explode(',', $request->input('meal_adults'));
            $MealChild = explode(',', $request->input('meal_childs'));
            $MealSpReq = explode(',', $request->input('meal_Sp_req'));
            $MealUnitPrice = explode(',', $request->input('meal_unit_price'));

            // **** Service Types **** //
            $ServiceType = explode(',', $request->input('service_type'));
            $ServiceDate = explode(',', $request->input('service_date'));
            $ServiceAdult = explode(',', $request->input('service_adult'));
            $ServiceChild = explode(',', $request->input('service_child'));
            $ServiceUnitPrice = explode(',', $request->input('service_rate'));

            // **** Pax Details **** //
            $FirstName = explode(',', $request->input('first_name'));
            $LastName = explode(',', $request->input('last_name'));
            $PaxType = explode(',', $request->input('pax_type'));
            $Price = explode(',', $request->input('per_price'));

            //**** Child Details ****//
            $ChildName = explode(',', $request->input('child_name'));

            $paxDetails = array();
            $serviceDetail = array();
            $mealDetail = array();
            $childAgeDetails = array();

            $CategoryID = $request->input('category_id');

            // $UpdateResevationQuery = 

            HotelResevation::create([
                'rate_key' => $rate_Key,
                'resevation_no' => $BookingReference,
                'resevation_name' => $ResevationName,
                'resevation_date' => $TodayDate,
                'hotel_name' => $HotelName,
                'checkin_time' => $CheckIn,
                'checkout_time' => $CheckOut,
                'baby_crib' => '-',
                'no_of_adults' => $NoOfAdults,
                'no_of_childs' => $NoOfChilds,
                'child_withbed' => $ChildWithBedCount,
                'child_nobed' => $ChildNoBedCount,
                'bed_type' => $BedType,
                'room_type' => $RoomType,
                'no_of_rooms' => $RoomCount,
                'board_code' => $BoardCode,
                'special_notice' => $SpecialRemarks,
                'resevation_platform' => $ResevationPlatform,
                'resevation_status' => 'CONFIRMED',
                'currency' => $Currency,
                'cancelation' => 'true',
                'modification' => 'false',
                'cancelation_amount' => '-',
                'cancelation_deadline' => $CancellationDeadline,
                'booking_remarks' => $BookingRemarks,
                'status' => 'NEW',
                'created_at' => $TodayDate,
                'updated_at' => $TodayDate,
                'user_id' => '1'
            ]);

            // **** Hotel Resevation Child Details ****//
            if (count($Child_Age) >= 1) {
                for ($cc = 0; $cc < count($Child_Age); $cc++) {
                    $childAgeDetails[] = ['name' => $ChildName[$cc], 'age' => $Child_Age[$cc]];
                }
                foreach ($childAgeDetails as $childAge) {
                    HotelResevationChildDetail::create([
                        'resevation_no' => $BookingReference,
                        'child_name' => $childAge['name'],
                        'child_age' => $childAge['age']
                    ]);
                }
            }

            // **** Last Payments Details **** //
            // $TotalAmount = (float)$request->input('last_booking_total_amount');
            // $PaidAmount = (float)$request->input('paid_amount');
            // $BalanceAmount = (float)$request->input('balance_amount');
            $PaymentMethod = $request->input('payment_method');

            $TotalAmount = $QueryOne->total_amount;
            $PaidAmount = $QueryOne->paid_amount;

            $LatestAmount = (float)$request->input('latest_amount');

            $ToBePaid = 0.00;
            $AmendmentRefund = 0.00;

            if ($LatestAmount > $PaidAmount) {
                $ToBePaid = (float)($LatestAmount - $PaidAmount);
            } else if ($LatestAmount < $PaidAmount) {
                $AmendmentRefund = (float)($PaidAmount - $LatestAmount);
            }

            HotelRoomDetails::create([
                'resevation_no' => $BookingReference,
                'room_code' => $BedType,
                'adult_count' => $NoOfAdults,
                'child_count' => $NoOfChilds,
                'adult_rate' => $AdultRate,
                'child_withbed_rate' => $ChildWithBed,
                'child_nobed_rate' => $ChildNoBed
            ]);


            if (count($MealType) >= 1) {
                for ($c = 0; $c < count($MealType); $c++) {
                    $mealDetail[] = ['type' => $MealType[$c], 'date' => $MealDate[$c], 'adult' => $MealAdult[$c], 'child' => $MealChild[$c], 'sp_req' => $MealSpReq[$c], 'price' => $MealUnitPrice[$c]];
                }

                foreach ($mealDetail as $meal) {
                    ResevationMealDetail::create([
                        'resevation_no' => $BookingReference,
                        'meal_plan' => $meal['type'],
                        'date' => $meal['date'],
                        'adult_count' => $meal['adult'],
                        'child_count' => $meal['child'],
                        'special_request' => $meal['sp_req'],
                        'unit_price' => $meal['price'],
                    ]);
                }
            }


            if (count($ServiceType) >= 1) {
                for ($x = 0; $x < count($ServiceType); $x++) {
                    $serviceDetail[] = ['type' => $ServiceType[$x], 'date' => $ServiceDate[$x], 'adult' => $ServiceAdult[$x], 'child' => $ServiceChild[$x], 'price' => $ServiceUnitPrice[$x]];
                }

                foreach ($serviceDetail as $service) {
                    ResevationServiceType::create([
                        'resevation_no' => $BookingReference,
                        'service_type' => $service['type'],
                        'date' => $service['date'],
                        'adult_count' => $service['adult'],
                        'child_count' => $service['child'],
                        'unit_price' => $service['price'],
                    ]);
                }
            }

            if (count($PaxType) >= 1) {
                for ($y = 0; $y < count($PaxType); $y++) {
                    $paxDetails[] = ['fname' => $FirstName[$y], 'lname' => $LastName[$y], 'type' => $PaxType[$y], 'price' => $Price];
                }

                foreach ($paxDetails as $pax) {
                    ResevationTraverllerDetail::create([
                        'resevation_no' => $BookingReference,
                        'first_name' => $pax['fname'],
                        'last_name' => $pax['lname'],
                        'type' => $pax['type']
                    ]);
                }
            }

            HotelResevationPayment::create([
                'resevation_no' => $BookingReference,
                'total_amount' => $ToBePaid,
                'paid_amount' => $ToBePaid,
                'balance_payment' => 0.00,
                'amendment_refund' => $AmendmentRefund,
                'payment_method' => $PaymentMethod,
                'payment_status' => 'COMPLETED',
                'payment_slip_image' => '-'
            ]);


            return $this->bookingAmendmentEmail($BookingReference);

            return response()->json([
                'status' => 200,
                'message' => 'Booking confirmed! Confirmation e-mail will recieve to your email shortley'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    // ***** Booking Amendment Email Sending *****//
    public function bookingAmendmentEmail($amendBookId)
    {
        $dataJoinOne = DB::table('tbl_hotel_resevation')
            ->where('tbl_hotel_resevation.resevation_no', $amendBookId)
            ->where('tbl_hotel_resevation.status', 'AMENDMENT')
            ->join('users', 'tbl_hotel_resevation.user_id', '=', 'users.id')
            ->join('tbl_hotel_roomdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_roomdetails.resevation_no')
            ->join('tbl_hotel_resevation_payments', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_resevation_payments.resevation_no')
            ->join('tbl_hotel', 'tbl_hotel_resevation.hotel_name', '=', 'tbl_hotel.id')
            ->join('tbl_hotel_vendor', 'tbl_hotel_resevation.hotel_name', '=', 'tbl_hotel_vendor.hotel_id')
            ->select(
                'users.email',
                'tbl_hotel_resevation.*',
                'tbl_hotel_resevation.id AS InoiceId',
                'tbl_hotel_roomdetails.*',
                'tbl_hotel_resevation_payments.*',
                'tbl_hotel.hotel_name',
                'tbl_hotel.hotel_address',
                'tbl_hotel_vendor.hotel_email'
            )->first();

        // return $dataJoinOne; 

        $dataJoinTwo = DB::table('tbl_hotel_travellerdetails')->where('tbl_hotel_travellerdetails.resevation_no', $amendBookId)->select('*')->get();
        $dataJoinThree = DB::table('tbl_hotel_resevation')
            ->where('tbl_hotel_resevation.resevation_no', $amendBookId)
            ->where('tbl_hotel_resevation.status', 'AMENDMENT')
            ->join('tbl_hotel_mealdetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_mealdetail.resevation_no')
            ->select('*')->get();
        $dataJoinFour = DB::table('tbl_hotel_servicedetail')->where('tbl_hotel_servicedetail.resevation_no', $amendBookId)->select('*')->get();



        $detailJoin = DB::table('tbl_hotel_resevation')
            ->where('tbl_hotel_resevation.resevation_no', $amendBookId)
            ->where('tbl_hotel_resevation.status', 'AMENDMENT')
            ->join('tbl_hotel_roomdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_roomdetails.resevation_no')
            ->join('tbl_hotel_mealdetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_mealdetail.resevation_no')
            ->join('tbl_hotel_servicedetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_servicedetail.resevation_no')
            ->join('tbl_hotel_travellerdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_travellerdetails.resevation_no')
            ->select(
                'tbl_hotel_roomdetails.adult_count AS AdultCount',
                'tbl_hotel_roomdetails.child_count AS ChildCount',
                'tbl_hotel_mealdetail.meal_plan AS MealPlan',
                'tbl_hotel_mealdetail.adult_count AS MealAdult',
                'tbl_hotel_mealdetail.child_count AS MealChild',
                'tbl_hotel_mealdetail.date AS MealDate',
                'tbl_hotel_mealdetail.special_request AS MealSpeReq',
                'tbl_hotel_mealdetail.unit_price AS MealPrice',
                'tbl_hotel_servicedetail.service_type AS SerType',
                'tbl_hotel_servicedetail.unit_price AS ServicePrice',
                'tbl_hotel_servicedetail.child_count AS SerChildCount',
                'tbl_hotel_servicedetail.date AS SerDate',
                'tbl_hotel_servicedetail.unit_price AS SerPerPrice',
                'tbl_hotel_travellerdetails.type AS PaxType'
            )->get();

        // return $detailJoin;

        $userEmail = $dataJoinOne->email;

        $invoice_no = $dataJoinOne->InoiceId;
        $resevationNumber = $dataJoinOne->resevation_no;
        $resevation_name = $dataJoinOne->resevation_name;
        $resevation_date = $dataJoinOne->resevation_date;
        $checkin_time = date('Y-m-d', strtotime($dataJoinOne->checkin_time));
        $checkout_time = date('Y-m-d', strtotime($dataJoinOne->checkout_time));
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
        $hotel_address = $dataJoinOne->hotel_address;
        $hotel_email = $dataJoinOne->hotel_email;
        $pax = (int)$no_of_adults + (int)$no_of_childs;

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
            'no_of_rooms' => $no_of_rooms, 'board_code' => $board_code, 'special_notice' => $special_notice, 'resevation_status' => $resevation_status, 'pax_count' => $pax,
            'cancel_dealine' => $cancelation_deadline, 'room_code' => $room_code, 'total_amount' => $total_amount, 'nights' => $nightsCount, 'hotelName' => $hotel_name, 'otherData' => $detailJoin,
            'meal_data' => $dataJoinThree, 'hotelName' => $hotel_name, 'hotelAddress' => $hotel_address, 'hotelEmail' => $hotel_email
        ];

        // return view('Mails.AahaasRecipt', $dataSet);
        // $pdf = Pdf::loadView('pdf_view', $dataSet);
        // $pdf = PDF::loadView('Mails.AahaasRecipt', $dataSet);


        $pdf = app('dompdf.wrapper');
        $pdf->loadView('Mails.Amendment', $dataSet);
        // return $pdf->download('pdf_file.pdf');

        try {
            $done = Mail::send('Mails.ReciptBody', $dataSet, function ($message) use ($userEmail, $resevationNumber, $pdf) {
                $message->to($userEmail);
                $message->subject('Booking Amendment Confirmation Email on your Booking Reference: #' . $resevationNumber . '.');
                $message->attachData($pdf->output(), $resevationNumber . '_' . 'Recipt.pdf', ['mime' => 'application/pdf',]);
            });

            return response()->json([
                'status' => 200,
                'message' => 'Booking Confirmed and Confirmation Mail sent your email'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
