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
    public $hotelInven;

    public function __construct()
    {
        $this->hotelInven = new Hotel();
    }

    /* Fetch all the hotel data from the db function starting */
    public function index()
    {
        $hotel_data = DB::table('tbl_hotel')->get();

        return response([
            'status' => 200,
            'hotelData' => $hotel_data
        ]);
    }
    /* Fetch all the hotel data from the db function ending */

    /* Create new hotel details data function starting */
    public function createNewHotel(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $validator = Validator::make($request->all(), [
                'hotelName' => 'required',
                'hotelDescription' => 'required',
                'hotelLevel' => 'required',
                'longtitude' => 'required',
                'latitude' => 'required',
                'provider' => 'required',
                'hotelAddress' => 'required',
                'city' => 'required',
                'hotelStatus' => 'required',
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

            $hotel_images = [];
            $code = rand(1000, 99999);

            if ($request->hasFile('hotelImage')) {
                $file = $request->file('hotelImage');

                foreach ($request->file('hotelImage') as $image) {

                    $fileExtension = $image->getClientOriginalExtension();
                    $clientOriginalName = $image->getClientOriginalName();
                    $fileName = $code . $clientOriginalName . '__HI' . '.' . $fileExtension;
                    $image->move('uploads/hotelImages/', $fileName);
                    $Imageurl = 'uploads/hotelImages/' . $fileName;
                    $hotel_images[] = $Imageurl;
                }
            }

            $hotel_Data = Hotel::create([
                'hotel_name' => $request->input('hotelName'),
                'hotel_description' => $request->input('hotelDescription'),
                'hotel_level' => $request->input('hotelLevel'),
                'longtitude' => $request->input('longtitude'),
                'latitude' => $request->input('latitude'),
                'provider' => $request->input('provider'),
                'hotel_address' => $request->input('hotelAddress'),
                'trip_advisor_link' => $request->input('tripAdvisorLink'),
                'hotel_image' => implode('|', $hotel_images),
                'city' => $request->input('city'),
                'hotel_status' => $request->input('hotelStatus'),
                'start_date' => $request->input('startDate'),
                'end_date' => $request->input('endDate'),
                'micro_location' => $request->input('micro_location'),
                'vendor_id' => $request->input('vendor_id'),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
                'updated_by' => $request->input('loggedUser'),
            ]);

            return response()->json([
                'status' => 200,
                'hotel_success' => 'Hotel Details added to system'
            ]);
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

        // $serviceType = DB::table('tbl_hotel_room_rate')->where('hotel_id', $id)->select('service_type')->groupBy('service_type')->get();
        // $servicesList = DB::table('tbl_hotel_room_rate')->where('hotel_id', $id)->select('service_type', 'service')->groupBy('service')->get();

        $serviceType = DB::table('tbl_hotel_aahaas_services')
            ->join('tbl_hotel_aahaas_service_types', 'tbl_hotel_aahaas_service_types.id', '=', 'tbl_hotel_aahaas_services.service_type_id')
            ->where('tbl_hotel_aahaas_services.hotel_id', $id)
            ->select('tbl_hotel_aahaas_service_types.service_name as service_type')
            ->groupBy('tbl_hotel_aahaas_services.service_type_id')
            ->get();

        $servicesList = DB::table('tbl_hotel_aahaas_services')
            ->join('tbl_hotel_aahaas_service_types', 'tbl_hotel_aahaas_service_types.id', '=', 'tbl_hotel_aahaas_services.service_type_id')
            ->where('tbl_hotel_aahaas_services.hotel_id', $id)
            ->select('tbl_hotel_aahaas_service_types.service_name as service_type', 'tbl_hotel_aahaas_services.service_name as service', 'tbl_hotel_aahaas_services.id')
            // ->groupBy('tbl_hotel_aahaas_services.service_type_id')
            ->get();

        $InvenData = $this->hotelInven->getRoomTypesById($id);

        // return $InvenData;


        return response()->json([
            'status' => 200,
            'hotel_by_id' => $hotelById,
            'mealType' => $mealType,
            'serviceType' => $serviceType,
            'service' => $servicesList,
            'room_types' => $InvenData
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
