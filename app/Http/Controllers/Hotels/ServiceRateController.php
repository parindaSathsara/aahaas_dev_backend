<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotels\ServiceRate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceRateController extends Controller
{
    /* fetch all the data of Service rates function starting */
    public function index()
    {
        $service_rate_allData = DB::table('tbl_service_rate')->get();

        return response()->json([
            'status' => 200,
            'service_rate_data' => $service_rate_allData
        ]);
    }
    /* fetch all the data of Service rates function ending */

    /* Create new service rate function starting */
    public function createNewServiceRate(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
            $updated_by = $request->input('updated_by');

            $hotel_id = $request->input('hotel_id');
            $booking_startdate = date('Y-m-d H:i:s', strtotime($request->input('booking_start_date')));
            $booking_enddate = date('Y-m-d H:i:s', strtotime($request->input('booking_end_date')));
            $travel_startdate = date('Y-m-d', strtotime($request->input('travel_start_date')));
            $travel_enddate = date('Y-m-d', strtotime($request->input('travel_end_date')));
            $room_category = $request->input('room_category');
            $room_type = $request->input('room_type');
            $meal_plan = $request->input('meal_plan');
            $market_nationality = $request->input('market_nationality');
            $currency = $request->input('currency');
            $service_type = $request->input('service_type');
            $supplement_type = $request->input('supplement_type');
            $compulsory = $request->input('compulsory');
            $adult_rate = (float)$request->input('adult_rate');
            $child_rate = (float)$request->input('child_rate');
            $package_px_count = (int)$request->input('package_px_count');
            $package_rate = (float)$request->input('package_rate');
            $package_add_px_rate = (float)$request->input('package_add_px_rate');
            $package_child_rate = (float)$request->input('package_child_rate');
            $child_foc_age = (int)$request->input('child_foc_age');
            $child_age = (int)$request->input('child_age');
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
                'booking_start_date' => 'required',
                'booking_end_date' => 'required',
                'travel_start_date' => 'required',
                'travel_end_date' => 'required',
                'room_category' => 'required',
                'room_type' => 'required',
                'meal_plan' => 'required',
                'market_nationality' => 'required',
                'currency' => 'required',
                'service_type' => 'required',
                'supplement_type' => 'required',
                'compulsory' => 'required',
                'adult_rate' => 'required',
                'child_rate' => 'required',
                'package_px_count' => 'required',
                'package_rate' => 'required',
                'package_add_px_rate' => 'required',
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

            $new_service_rate = ServiceRate::create([
                'hotel_id' => $hotel_id,
                'booking_start_date' => $booking_startdate,
                'booking_end_date' => $booking_enddate,
                'travel_start_date' => $travel_startdate,
                'travel_end_date' => $travel_enddate,
                'room_category' => $room_category,
                'room_type' => $room_type,
                'meal_plan' => $meal_plan,
                'market_nationality' => $market_nationality,
                'currency' => $currency,
                'service_type' => $service_type,
                'supplement_type' => $supplement_type,
                'compulsory' => $compulsory,
                'adult_rate' => $adult_age,
                'child_rate' => $child_rate,
                'package_px_count' => $package_px_count,
                'package_rate' => $package_rate,
                'package_add_px_rate' => $package_add_px_rate,
                'package_child_rate' => $package_child_rate,
                'child_foc_age' => $child_foc_age,
                'child_age' => $child_age,
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
                'updated_by' => $updated_by
            ]);

            return response()->json([
                'status' => 200,
                'new_room_rateData' => $new_service_rate,
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* Create new service rate function ending */

    /* fetch service rate data by id function starting */
    public function fetchServiceDataById($id)
    {
        $fetch_service_rate = ServiceRate::find($id)->first();

        return response()->json([
            'status' => 200,
            'service_rate_byid' => $fetch_service_rate
        ]);
    }
    /* fetch service rate data by id function ending */

    /* update service rate data function starting */
    public function updateServiceRateData(Request $request, $id)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
            $updated_by = $request->input('updated_by');

            $booking_startdate = date('Y-m-d H:i:s', strtotime($request->input('booking_start_date')));
            $booking_enddate = date('Y-m-d H:i:s', strtotime($request->input('booking_end_date')));
            $travel_startdate = date('Y-m-d', strtotime($request->input('travel_start_date')));
            $travel_enddate = date('Y-m-d', strtotime($request->input('travel_end_date')));
            $room_category = $request->input('room_category');
            $room_type = $request->input('room_type');
            $meal_plan = $request->input('meal_plan');
            $market_nationality = $request->input('market_nationality');
            $currency = $request->input('currency');
            $service_type = $request->input('service_type');
            $supplement_type = $request->input('supplement_type');
            $compulsory = $request->input('compulsory');
            $adult_rate = (float)$request->input('adult_rate');
            $child_rate = (float)$request->input('child_rate');
            $package_px_count = (int)$request->input('package_px_count');
            $package_rate = (float)$request->input('package_rate');
            $package_add_px_rate = (float)$request->input('package_add_px_rate');
            $package_child_rate = (float)$request->input('package_child_rate');
            $child_foc_age = (int)$request->input('child_foc_age');
            $child_age = (int)$request->input('child_age');
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
                'booking_start_date' => 'required',
                'booking_end_date' => 'required',
                'travel_start_date' => 'required',
                'travel_end_date' => 'required',
                'room_category' => 'required',
                'room_type' => 'required',
                'meal_plan' => 'required',
                'market_nationality' => 'required',
                'currency' => 'required',
                'service_type' => 'required',
                'supplement_type' => 'required',
                'compulsory' => 'required',
                'adult_rate' => 'required',
                'child_rate' => 'required',
                'package_px_count' => 'required',
                'package_rate' => 'required',
                'package_add_px_rate' => 'required',
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

            $update_service_rate = DB::select(DB::raw("UPDATE tbl_service_rate SET booking_start_date='$booking_startdate',booking_end_date='$booking_enddate',travel_start_date='$travel_startdate',travel_end_date='$travel_enddate',
            room_category='$room_category',room_type='$room_type',meal_plan='$meal_plan',market_nationality='$market_nationality',currency='$currency',service_type='$service_type',supplement_type='$supplement_type',
            compulsory='$compulsory',adult_rate='$adult_rate',child_rate='$child_rate',package_px_count='$package_px_count',package_rate='$package_rate',package_add_px_rate='$package_add_px_rate',
            package_child_rate='$package_child_rate',child_foc_age='$child_foc_age',child_age='$child_age',adult_age='$adult_age',special_rate='$special_rate',payment_policy='$payment_policy',book_by_days='$book_by_days',
            cancellation_days='$cancellation_days',cancellation_policy='$cancellation_policy',stop_sales_startdate='$stop_sales_startdate',stop_sales_enddate='$stop_sales_enddate',blackoutdates='$blackoutdates',
            blackoutdays='$blackoutdays',updated_at='$currentTime',updated_by='$updated_by' WHERE id='$id'"));

            return response()->json([
                'status' => 200,
                'message' => 'service rate data updated'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* update service rate data function ending */
}
