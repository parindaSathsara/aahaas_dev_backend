<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotels\HotelDetail;
use App\Models\Hotels\Hotel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HotelDetailController extends Controller
{
    
    /* Fetch all the hotel details function starting */
    public function index()
    {
        $hotelDetails = DB::table('tbl_hotel_details')->get();

        return response()->json([
            'status'=>200,
            'hotel_details'=>$hotelDetails
        ]);
    }
    /* Fetch all the hotel details function ending */

    /* Create new data to hotel details function starting */
    public function createHotelDetails(Request $request)
    {
        try{

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $validator = Validator::make($request->all(),[
                'hotel_id'=>'required',
                'driver_accomadation'=>'required',
                'lift_status'=>'required',
                'vehicle_approchable'=>'required',
                'ac_status'=>'required',
                'covid_safe'=>'required',
            ]);

            if($validator->fails())
            {
                return response()->json([
                    'status'=>400,
                    'validation_error'=>$validator->messages()
                ]);
            }
            if(!Auth::check())
            {
                return response()->json([
                    'status'=>403,
                    'login_error'=>'Session Expired, Please log again'
                ]);
            }

            $hotelDetail = HotelDetail::create([
                'hotel_id'=>$request->input('hotel_id'),
                'driver_accomadation'=>$request->input('driver_accomadation'),
                'lift_status'=>$request->input('lift_status'),
                'vehicle_approchable'=>$request->input('vehicle_approchable'),
                'ac_status'=>$request->input('ac_status'),
                'covid_safe'=>$request->input('covid_safe'),
                'created_at'=>$currentTime,
                'updated_at'=>$currentTime,
                'updated_by'=>$request->input('updated_by')
            ]);

            return response()->json([
                'status'=>200,
                'hotel_success'=>'Hotel Details added to system'
            ]);

        }catch(\Exception $exception){

            return response()->json([
                'status'=>401,
                'message'=> throw $exception
            ]);

        }
    }
    /* Create new data to hotel details function ending */

    /* Fetch data by id function stating */
    public function fetchHotelDetailsById($id)
    {
        $hotelDetailsById = HotelDetail::find($id)->first();

        return response()->json([
            'status'=>200,
            'hotel_by_id'=>$hotelDetailsById
        ]);
    }
    /* Fetch data by id function ending */

    /* Update Hotel Details function starting */
    public function updateHotelDetails(Request $request,$id)
    {
        try{
            
            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $validator = Validator::make($request->all(),[
                'driver_accomadation'=>'required',
                'lift_status'=>'required',
                'vehicle_approchable'=>'required',
                'ac_status'=>'required',
                'covid_safe'=>'required',
            ]);

            if($validator->fails())
            {
                return response()->json([
                    'status'=>400,
                    'validation_error'=>$validator->messages()
                ]);
            }
            if(!Auth::check())
            {
                return response()->json([
                    'status'=>403,
                    'login_error'=>'Session Expired, Please log again'
                ]);
            }

            $driver_accomadation = $request->input('driver_accomadation');
            $lift_status = $request->input('lift_status');
            $vehicle_approchable = $request->input('vehicle_approchable');
            $ac_status = $request->input('ac_status');
            $covid_safe = $request->input('covid_safe');
            $updated_by = $request->input('updated_by');


            $updateHotelDetailsData = DB::select(DB::raw("UPDATE tbl_hotel_details SET driver_accomadation='$driver_accomadation', lift_status='$lift_status', vehicle_approchable='$vehicle_approchable', ac_status='$ac_status',
            covid_safe='$covid_safe',updated_at='$currentTime',updated_by='$updated_by' WHERE id='$id'"));

            return response()->json([
                'status'=>200,
                'message'=>'Hotel Details Update Successfully'
            ]);


        }catch(\Exception $exception){

            return response()->json([
                'status'=>401,
                'message'=> throw $exception
            ]);
        }
    }
    /* Update Hotel Details function ending */

    /* Hotel and Hotel detail table all data fetch function starting */
    public function getHotelDetailsJoinData()
    {
        try{

            $hotelDetailsJoinData = DB::table('tbl_hotel')
                                    ->join('tbl_hotel_details','tbl_hotel.id','=','tbl_hotel_details.hotel_id')
                                    ->select('tbl_hotel.hotel_name AS Hotel','tbl_hotel.hotel_description','tbl_hotel.hotel_level','tbl_hotel.longtitude','tbl_hotel.latitude','tbl_hotel.provider','tbl_hotel.preferred_status','tbl_hotel.hotel_address','tbl_hotel.trip_advisor_link',
                                    'tbl_hotel.hotel_image','tbl_hotel.city','tbl_hotel.hotel_status','tbl_hotel.startdate','tbl_hotel.enddate','tbl_hotel.micro_location','tbl_hotel.selling_point','tbl_hotel.vendor_id','tbl_hotel_details.driver_accomadation','tbl_hotel_details.lift_status',
                                    'tbl_hotel_details.vehicle_approchable','tbl_hotel_details.ac_status','tbl_hotel_details.covid_safe')
                                    ->get();
        

            return response()->json([
                'status'=>200,
                'hotelDetails_joindata'=>$hotelDetailsJoinData
            ]);

        }catch(\Exception $exception){

            return response()->json([
                'status'=>401,
                'message'=> throw $exception
            ]);

        }
    }
    /* Hotel and Hotel detail table all data fetch function ending */
}
