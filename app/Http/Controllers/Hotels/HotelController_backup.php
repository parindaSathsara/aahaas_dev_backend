<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotels\Hotel;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller
{

    public $hotel;

    public function __construct()
    {
        $this->hotel = new Hotel();
    }

    /* Fetch all the hotel data from the db function starting */
    public function index()
    {
        $hotel_data = DB::table('tbl_hotel')->get();

        return response()->json([
            'status' => 200,
            'hotelData' => $hotel_data
        ]);
    }
    /* Fetch all the hotel data from the db function ending */

    /* Create new hotel details data function starting */
    public function createNewHotel(Request $request)
    {
        try {

            // $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $hotel_name = $request['hotel_name'];
            $hotel_level = $request['hotel_level'];
            $hotel_catgory = $request['hotel_catgory'];
            $hotel_des = $request['hotel_des'];
            $hotel_latitude = $request['hotel_latitude'];
            $hotel_longitude = $request['hotel_longitude'];
            $hotel_provider = $request['hotel_provider'];
            $hotel_address = $request['hotel_address'];
            $trip_ad_link = $request['trip_ad_link'];
            $hotel_country = $request['hotel_country'];
            $hotel_city = $request['hotel_city'];
            $hotel_micro_location = $request['hotel_micro_location'];
            $hotel_status = $request['hotel_status'];
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $hotel_vendor = $request['hotel_vendor'];

            $validator = Validator::make($request->all(), [
                'hotel_name' => 'required',
                'hotel_des' => 'required',
                'hotel_level' => 'required',
                'hotel_longitude' => 'required',
                'hotel_latitude' => 'required',
                'hotel_provider' => 'required',
                'hotel_address' => 'required',
                'hotel_city' => 'required',
                'hotel_status' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            }
            // if (!Auth::check()) {
            //     return response()->json([
            //         'status' => 403,
            //         'login_error' => 'Session Expired, Please log again'
            //     ]);
            // }

            $hotel_images = [];
            $code = rand(1000, 99999);

            if ($request->hasFile('hotel_image')) {
                $file = $request->file('hotel_image');

                foreach ($request->file('hotel_image') as $image) {

                    $fileExtension = $image->getClientOriginalExtension();
                    $clientOriginalName = $image->getClientOriginalName();
                    $fileName = $code . $clientOriginalName . '__HI' . '.' . $fileExtension;
                    $image->move('uploads/hotelImages/', $fileName);
                    $Imageurl = 'uploads/hotelImages/' . $fileName;
                    $hotel_images[] = $Imageurl;
                }
            }

            $response = $this->hotel->createNewHotel($hotel_name, $hotel_des, $hotel_level, $hotel_catgory, $hotel_longitude, $hotel_latitude, $hotel_provider, $hotel_address, $trip_ad_link, $hotel_images, $hotel_country, $hotel_city, $hotel_micro_location, $hotel_status, $start_date, $end_date, $hotel_vendor);

            return $response;
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* Create new hotel details data function ending */

    /* Fetch Hotel Data by ID Function starting */

    public function fecthHotelById($id)
    {
        $hotelById = Hotel::where('id', $id)->first();

        $mealType = DB::table('tbl_hotel_room_rate')->where('hotel_id', $id)->select('meal_plan')->groupBy('meal_plan')->get();
        $serviceType = DB::table('tbl_hotel_room_rate')->where('hotel_id', $id)->select('service_type')->groupBy('service_type')->get();

        return response()->json([
            'status' => 200,
            'hotel_by_id' => $hotelById,
            'mealType' => $mealType,
            'serviceType' => $serviceType
        ]);
    }

    /* Fetch Hotel Data by ID Function ending */

    /* Update hotel data by id function starting */
    public function updateHotelDataById(Request $request, $id)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $validator = Validator::make($request->all(), [
                'preferred_status' => 'required',
                'hotelStatus' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'selling_point' => 'required',
                'updated_by' => 'required'
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

            $prefered_status = $request->input('preferred_status');
            $hotelStatus = $request->input('hotelStatus');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $selling_point = $request->input('selling_point');
            $updated_by = $request->input('updated_by');

            $updateHotelData = DB::select(DB::raw("UPDATE tbl_hotel SET preferred_status='$prefered_status', hotel_status='$hotelStatus', startdate='$start_date', enddate='$end_date',
            selling_point='$selling_point',updated_at='$currentTime',updated_by='$updated_by' WHERE id='$id'"));

            return response()->json([
                'status' => 200,
                'message' => 'Hotel Details Update Successfully'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* Update hotel data by id function ending */

    // ***** Get Hotel Details for landing page order by lowest price ***** //
    public function getLowestHotels($category1)
    {
        if ($category1 == 0) {
            try {

                $SqlQuery = DB::table('tbl_hotel')
                    ->join('tbl_hotel_room_rate', 'tbl_hotel.id', '=', 'tbl_hotel_room_rate.hotel_id')
                    ->leftJoin('tbl_hotel_terms_conditions', 'tbl_hotel.id', 'tbl_hotel_terms_conditions.hotel_id')
                    ->leftJoin('tbl_hotel_inventory', 'tbl_hotel.id', '=', 'tbl_hotel_inventory.hotel_id')
                    ->leftJoin('tbl_hotel_discount', 'tbl_hotel.id', '=', 'tbl_hotel_discount.hotel_id')
                    ->select('tbl_hotel.*', 'tbl_hotel.id AS HotelIDHOTEL', 'tbl_hotel_room_rate.*', 'tbl_hotel_terms_conditions.*', 'tbl_hotel_inventory.*', 'tbl_hotel_discount.*')
                    ->orderBy('tbl_hotel_room_rate.adult_rate', 'ASC')
                    ->groupBy('tbl_hotel_room_rate.hotel_id')
                    ->get();


                return response()->json([
                    'status' => 200,
                    'query_result' => $SqlQuery
                ]);
            } catch (\Exception $ex) {

                return response()->json([
                    'status' => 401,
                    'message' => throw $ex
                ]);
            }
        } else {
            try {

                $SqlQuery = DB::table('tbl_hotel')
                    ->join('tbl_hotel_room_rate', 'tbl_hotel.id', '=', 'tbl_hotel_room_rate.hotel_id')
                    ->leftJoin('tbl_hotel_terms_conditions', 'tbl_hotel.id', 'tbl_hotel_terms_conditions.hotel_id')
                    ->leftJoin('tbl_hotel_inventory', 'tbl_hotel.id', '=', 'tbl_hotel_inventory.hotel_id')
                    ->leftJoin('tbl_hotel_discount', 'tbl_hotel.id', '=', 'tbl_hotel_discount.hotel_id')
                    ->select('tbl_hotel.*', 'tbl_hotel.id AS HotelIDHOTEL', 'tbl_hotel_room_rate.*', 'tbl_hotel_terms_conditions.*', 'tbl_hotel_inventory.*', 'tbl_hotel_discount.*')
                    ->orderBy('tbl_hotel_room_rate.adult_rate', 'ASC')
                    ->groupBy('tbl_hotel_room_rate.hotel_id')
                    ->where('tbl_hotel.category1', $category1)
                    ->get();


                return response()->json([
                    'status' => 200,
                    'query_result' => $SqlQuery
                ]);
            } catch (\Exception $ex) {

                return response()->json([
                    'status' => 401,
                    'message' => throw $ex
                ]);
            }
        }
    }



    // ***** Get Hotel Details for landing page order by lowest price ***** //

    // ***** Get Hotel Room Category Details by Hotel Id ***** //
    public function getRoomCategoryDetailsById($id)
    {
        try {

            $SqlQuery = DB::table('tbl_hotel_inventory')->where('tbl_hotel_inventory.hotel_id', $id)
                ->join('tbl_hotel_room_rate', 'tbl_hotel_inventory.id', '=', 'tbl_hotel_room_rate.room_category')
                ->select('tbl_hotel_inventory.*', 'tbl_hotel_inventory.room_category AS ROOMCATEGORY', 'tbl_hotel_room_rate.*', 'tbl_hotel_room_rate.hotel_id AS HotelID')
                ->orderBy('tbl_hotel_room_rate.adult_rate', 'ASC')
                ->groupBy('tbl_hotel_room_rate.hotel_id')
                ->limit(3)->get();


            return response()->json([
                'status' => 200,
                'query_result' => $SqlQuery
            ]);
        } catch (\Exception $ex) {

            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }

    public function getHotelReservationDataById($resId)
    {
        try {

            $HotelData = DB::table('tbl_hotel_resevation')->select('*')->where('resevation_no', '=', $resId)->first();

            // return $HotelData;

            $CancelAmount = $HotelData->cancelation_amount;
            $Platform = $HotelData->resevation_platform;

            return response(['status' => 200, 'result' => $CancelAmount, 'platform' => $Platform]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getSpecialRatesById(Request $request, $id)
    {
        try {

            $Query = DB::table('tbl_hotel')->where('tbl_hotel.id', '=', $id)
                ->leftJoin('tbl_hotel_inventory', 'tbl_hotel.id', '=', 'tbl_hotel_inventory.hotel_id')
                ->leftJoin('tbl_hotel_room_rate', 'tbl_hotel.id', '=', 'tbl_hotel_room_rate.hotel_id')
                ->leftJoin('tbl_hotel_discount', 'tbl_hotel.id', '=', 'tbl_hotel_discount.hotel_id')
                ->select('*', 'tbl_hotel.id AS HotelID', 'tbl_hotel_inventory.room_type AS RoomType', 'tbl_hotel_inventory.room_category AS RoomCat')
                ->orderBy('tbl_hotel_inventory.room_type', 'ASC')
                ->limit('3')
                ->groupBy('tbl_hotel_inventory.room_category')
                ->get();


            return response([
                'status' => 200,
                'dataset_query' => $Query,
            ]);
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
}
