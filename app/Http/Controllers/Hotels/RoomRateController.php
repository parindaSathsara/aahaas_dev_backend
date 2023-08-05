<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotels\RoomRate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoomRateController extends Controller
{

    public $roomrate;

    public function __construct() {
        $this->roomrate = new RoomRate();
    }
    /* Fetch all room rate data function starting */
    public function index()
    {
        $room_rate_allData = DB::table('tbl_room_rate')->get();

        return response()->json([
            'status' => 200,
            'room_rate_data' => $room_rate_allData
        ]);
    }
    /* Fetch all room rate data function ending */

    /* create new room rate row function starting */
    public function createNewRoomRate(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $hotel_id = $request->input('hotel_id');
            $booking_startdate = date('Y-m-d H:i:s', strtotime($request->input('booking_startdate')));
            $booking_enddate = date('Y-m-d H:i:s', strtotime($request->input('booking_enddate')));
            $travel_startdate = date('Y-m-d', strtotime($request->input('travel_startdate')));
            $travel_enddate = date('Y-m-d', strtotime($request->input('travel_enddate')));
            $room_category = $request->input('room_category');
            $room_type = $request->input('room_type');
            $meal_plan = $request->input('meal_plan');
            $market_nationality = $request->input('market_nationality');
            $currency = $request->input('currency');
            $single_rate = (float)$request->input('single_rate');
            $double_rate = (float)$request->input('double_rate');
            $triple_rate = (float)$request->input('triple_rate');
            $quad_rate = (float)$request->input('quad_rate');
            $other_occupancy = $request->input('other_occupancy');
            $other_rate = (float)$request->input('other_rate');
            $child_with_bed = (float)$request->input('child_with_bed');
            $child_no_bed = (float)$request->input('child_no_bed');
            $extra_bed = (float)$request->input('extra_bed');
            $extra_meal_child = (float)$request->input('extra_meal_child');
            $extra_meal_adult = (float)$request->input('extra_meal_adult');
            $child_foc_age = $request->input('child_foc_age');
            $child_meal_age = $request->input('child_meal_age');
            $adult_age = (int)$request->input('adult_age');
            $special_rate = (float)$request->input('special_rate');
            $payment_policy = $request->input('payment_policy');
            $book_by_days = (int)$request->input('book_by_days');
            $cancellation_days = (int)$request->input('cancellation_days');
            $cancellation_policy = $request->input('cancellation_policy');
            $stop_sales_startdate = date('Y-m-d', strtotime($request->input('stop_sales_startdate')));
            $stop_sales_enddate = date('Y-m-d', strtotime($request->input('stop_sales_enddate')));
            $blackoutdates = $request->input('blackoutdates');
            $blackoutdays = $request->input('blackoutdays');

            $validator = Validator::make($request->all(), [
                'booking_startdate' => 'required',
                'booking_enddate' => 'required',
                'travel_startdate' => 'required',
                'travel_enddate' => 'required',
                'room_category' => 'required',
                'room_type' => 'required',
                'meal_plan' => 'required',
                'market_nationality' => 'required',
                'currency' => 'required',
                'single_rate' => 'required',
                'double_rate' => 'required',
                'triple_rate' => 'required',
                'quad_rate' => 'required',
                'other_occupancy' => 'required',
                'other_rate' => 'required',
                'child_with_bed' => 'required',
                'child_no_bed' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            }
            if (!Auth::check()) {
                return response()->json([
                    'status' => 403,
                    'login_error' => 'Session Expired, Please log again'
                ]);
            }

            // 2022-10-27T13:03:55.000000Z

            $new_room_rate = RoomRate::create([
                'hotel_id' => $hotel_id,
                'booking_startdate' => $booking_startdate,
                'booking_enddate' => $booking_enddate,
                'travel_startdate' => $travel_startdate,
                'travel_enddate' => $travel_enddate,
                'room_category' => $room_category,
                'room_type' => $room_type,
                'meal_plan' => $meal_plan,
                'Market_nationality' => $market_nationality,
                'currency' => $currency,
                'single_rate' => $single_rate,
                'double_rate' => $double_rate,
                'triple_rate' => $triple_rate,
                'quad_rate' => $quad_rate,
                'other_occupancy' => $other_occupancy,
                'other_rate' => $other_rate,
                'child_with_bed' => $child_with_bed,
                'child_no_bed' => $child_no_bed,
                'extra_bed' => $extra_bed,
                'extra_meal_child' => $extra_meal_child,
                'extra_meal_adult' => $extra_meal_adult,
                'child_foc_age' => $child_foc_age,
                'child_meal_age' => $child_meal_age,
                'adult_age' => $adult_age,
                'special_rate' => $special_rate,
                'payment_policy' => $payment_policy,
                'book_by_days' => $book_by_days,
                'cancellation_days' => $cancellation_days,
                'cancellation_policy' => $cancellation_policy,
                'stop_sales_startdate' => $stop_sales_startdate,
                'stop_sales_enddate' => $stop_sales_enddate,
                'blackoutdates' => $blackoutdates,
                'blackoutdays' => $blackoutdays,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
                'updated_by' => $request->input('updated_by')
            ]);

            return response()->json([
                'status' => 200,
                'new_room_rateData' => $new_room_rate,
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* create new room rate row function ending */

    /* Find room rate by id function starting */
    public function findRoomRateById($id)
    {
        $findRoomRate = RoomRate::find($id)->first();

        return response()->json([
            'status' => 200,
            'room_rate_byid' => $findRoomRate
        ]);
    }
    /* Find room rate by id function ending */

    /* Room rate data update function starting */
    public function updateRoomRateData(Request $request, $id)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
        $updated_by = $request->input('updated_by');

        try {

            $booking_startdate = date('Y-m-d H:i:s', strtotime($request->input('booking_startdate')));
            $booking_enddate = date('Y-m-d H:i:s', strtotime($request->input('booking_enddate')));
            $travel_startdate = date('Y-m-d', strtotime($request->input('travel_startdate')));
            $travel_enddate = date('Y-m-d', strtotime($request->input('travel_enddate')));
            $room_category = $request->input('room_category');
            $room_type = $request->input('room_type');
            $meal_plan = $request->input('meal_plan');
            $market_nationality = $request->input('market_nationality');
            $currency = $request->input('currency');
            $single_rate = (float)$request->input('single_rate');
            $double_rate = (float)$request->input('double_rate');
            $triple_rate = (float)$request->input('triple_rate');
            $quad_rate = (float)$request->input('quad_rate');
            $other_occupancy = $request->input('other_occupancy');
            $other_rate = (float)$request->input('other_rate');
            $child_with_bed = (float)$request->input('child_with_bed');
            $child_no_bed = (float)$request->input('child_no_bed');
            $extra_bed = (float)$request->input('extra_bed');
            $extra_meal_child = (float)$request->input('extra_meal_child');
            $extra_meal_adult = (float)$request->input('extra_meal_adult');
            $child_foc_age = $request->input('child_foc_age');
            $child_meal_age = $request->input('child_meal_age');
            $adult_age = (int)$request->input('adult_age');
            $special_rate = (float)$request->input('special_rate');
            $payment_policy = $request->input('payment_policy');
            $book_by_days = (int)$request->input('book_by_days');
            $cancellation_days = (int)$request->input('cancellation_days');
            $cancellation_policy = $request->input('cancellation_policy');
            $stop_sales_startdate = date('Y-m-d', strtotime($request->input('stop_sales_startdate')));
            $stop_sales_enddate = date('Y-m-d', strtotime($request->input('stop_sales_enddate')));
            $blackoutdates = $request->input('blackoutdates');
            $blackoutdays = $request->input('blackoutdays');

            $validator = Validator::make($request->all(), [
                'booking_startdate' => 'required',
                'booking_enddate' => 'required',
                'travel_startdate' => 'required',
                'travel_enddate' => 'required',
                'room_category' => 'required',
                'room_type' => 'required',
                'meal_plan' => 'required',
                'market_nationality' => 'required',
                'currency' => 'required',
                'single_rate' => 'required',
                'double_rate' => 'required',
                'triple_rate' => 'required',
                'quad_rate' => 'required',
                'other_occupancy' => 'required',
                'other_rate' => 'required',
                'child_with_bed' => 'required',
                'child_no_bed' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            }
            if (!Auth::check()) {
                return response()->json([
                    'status' => 403,
                    'login_error' => 'Session Expired, Please log again'
                ]);
            }

            $updateRoomRate = DB::select(DB::raw("UPDATE tbl_room_rate SET booking_startdate='$booking_startdate', booking_enddate='$booking_enddate', travel_startdate='$travel_startdate',
            travel_enddate='$travel_enddate', room_category='$room_category', room_type='$room_type', meal_plan='$meal_plan', Market_nationality='$market_nationality', currency='$currency',
            single_rate='$single_rate', double_rate='$double_rate', triple_rate='$triple_rate', quad_rate='$quad_rate', other_occupancy='$other_occupancy', other_rate='$other_rate',
            child_with_bed='$child_with_bed', child_no_bed='$child_no_bed', extra_bed='$extra_bed', extra_meal_child='$extra_meal_child', extra_meal_adult='$extra_meal_adult',
            child_foc_age='$child_foc_age', child_meal_age='$child_meal_age', adult_age='$adult_age', special_rate='$special_rate', payment_policy='$payment_policy', book_by_days='$book_by_days',
            cancellation_days='$cancellation_days', stop_sales_startdate='$stop_sales_startdate', stop_sales_enddate='$stop_sales_enddate', blackoutdates='$blackoutdates', blackoutdays='$blackoutdays',
            updated_at='$currentTime', updated_by='$updated_by' WHERE id='$id'"));

            return response()->json([
                'status' => 200,
                'message' => 'Room rate data updated'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* Room rate data update function ending */

    /* Get room rate join with hotel function starting */
    public function getRoomRateDataWithHotel()
    {
        $room_data_with_hotelname = DB::table('tbl_hotel')
            ->join('tbl_room_rate', 'tbl_hotel.id', '=', 'tbl_room_rate.hotel_id')
            ->select(
                'tbl_hotel.hotel_name',
                'tbl_room_rate.booking_startdate',
                'tbl_room_rate.booking_enddate',
                'tbl_room_rate.travel_startdate',
                'tbl_room_rate.travel_enddate',
                'tbl_room_rate.room_category',
                'tbl_room_rate.room_type',
                'tbl_room_rate.meal_plan',
                'tbl_room_rate.Market_nationality',
                'tbl_room_rate.currency',
                'tbl_room_rate.single_rate',
                'tbl_room_rate.double_rate',
                'tbl_room_rate.triple_rate',
                'tbl_room_rate.quad_rate',
                'tbl_room_rate.other_occupancy',
                'tbl_room_rate.other_rate',
                'tbl_room_rate.child_with_bed',
                'tbl_room_rate.child_no_bed',
                'tbl_room_rate.extra_bed',
                'tbl_room_rate.extra_meal_child',
                'tbl_room_rate.extra_meal_adult',
                'tbl_room_rate.child_foc_age',
                'tbl_room_rate.child_meal_age',
                'tbl_room_rate.adult_age',
                'tbl_room_rate.special_rate',
                'tbl_room_rate.payment_policy',
                'tbl_room_rate.book_by_days',
                'tbl_room_rate.cancellation_days',
                'tbl_room_rate.stop_sales_startdate',
                'tbl_room_rate.stop_sales_enddate',
                'tbl_room_rate.blackoutdates',
                'tbl_room_rate.blackoutdays'
            )
            ->get();

        return response()->json([
            'status' => 200,
            'with_hotel_name' => $room_data_with_hotelname
        ]);
    }
    /* Get room rate join with hotel function ending */


    //create new hotel room rate data
    public function createNewHotelRoomRate(Request $request)
    {
        try {

            $HotelName = $request['hotel_name'];
            $BookinStartDate = $request['booking_startdate'];
            $BookingEndDate = $request['booking_enddate'];
            $TravelStartDate = $request['travel_startdate'];
            $TravelEndDate = $request['travel_enddate'];
            $RoomCategory = $request['room_category'];
            $RoomType = $request['room_type'];
            $MealPlan = $request['meal_plan'];
            $Market = $request['market_nationality'];
            $Currency = $request['currency'];
            $AdultRate = $request['adult_rate'];
            $ChildWithBed = $request['child_withbed'];
            $ChildWithoutBed = $request['child_withoutbed'];
            $SupType = $request['sup_type'];
            $Compulsory = $request['compulsory'];
            $ServiceType = $request['service_type'];
            $Service = $request['service'];
            $PackageAddPaxRate = $request['pkg_add_pxrate'];
            $PackageChildWBRate = $request['pkg_child_wbrate'];
            $PackageChildWORate = $request['pkg_child_withoutbed'];
            $ChildFocAge = $request['child_foc_age'];
            $ChidlWOAge = $request['child_wo_age'];
            $ChildWithBedRate = $request['child_wb_rate'];
            $AdultAge = $request['adult_age'];
            $SellingPoint = $request['selling_point'];
            $BookByDays = $request['book_by_days'];
            $SpecialRate = $request['special_rate'];
            $PaymentPolicy = $request['payment_policy'];
            $CancelDaysBefore = $request['cancel_days_before'];
            $CancelPolicy = $request['cancel_policy'];
            $BlackoutDates = $request['blackout_dates'];
            $BlackoutDays = $request['blackout_days'];

            $BlackoutDates = $request['blackout_dates'];

            
            $response = $this->roomrate->createNewHotelRoomRate($HotelName, $BookinStartDate, $BookingEndDate, $TravelStartDate, $TravelEndDate, $RoomCategory, $RoomType, $MealPlan,$Market,$Currency, $AdultRate, $ChildWithBed, $ChildWithoutBed, $SupType, $Compulsory, $ServiceType, $Service, $PackageAddPaxRate, $PackageChildWBRate, $PackageChildWORate, $ChildFocAge,  $ChidlWOAge, $ChildWithBedRate, $AdultAge, $SellingPoint, $BookByDays, $SpecialRate, $PaymentPolicy, $CancelDaysBefore, $CancelPolicy, $BlackoutDates, $BlackoutDays);


            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
