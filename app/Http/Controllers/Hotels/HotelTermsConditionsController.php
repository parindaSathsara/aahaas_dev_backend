<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotels\HotelTermsConditions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HotelTermsConditionsController extends Controller
{
    /* Fetch all the hotels T&C function starting */
    public function index()
    {
        $fetch_all_TC = DB::table('tbl_hotel_terms_conditions')->get();

        return response()->json([
            'status'=>200,
            'all_t&c'=>$fetch_all_TC
        ]);
    }
    /* Fetch all the hotels T&C function ending */

    /* create new terms and condition function starting */
    public function createNewHotelTermsConditions(Request $request)
    {
        try{

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
            $updated_by = $request->input('updated_by');

            $hotel_id = $request->input('hotel_id');
            $general_tc = $request->input('general_tc');
            $cancellation_policy = $request->input('cancellation_policy');
            $cancellation_deadline = $request->input('cancellation_deadline');

            $validator = Validator::make($request->all(),[
                'hotel_id'=>'required|unique:tbl_hotel_terms_conditions',
                'general_tc'=>'required',
                'cancellation_policy'=>'required',
                'cancellation_deadline'=>'required',
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

            $new_hotel_TC = HotelTermsConditions::create([
                'hotel_id'=>$hotel_id,
                'general_tc'=> $general_tc,
                'cancellation_policy'=>$cancellation_policy,
                'cancellation_deadline'=> $cancellation_deadline,
                'created_at'=>$currentTime,
                'updated_at'=>$currentTime,
                'updated_by'=>$updated_by
            ]);

            return response()->json([
                'status'=>200,
                'message'=> 'New Terms and Conditions added'
            ]);

        }catch(\Exception $exception){

            return response()->json([
                'status'=>401,
                'message'=> throw $exception
            ]);

        }
    }
    /* create new terms and condition function ending */

    /* find terms and condition details by id fucntion starting */
    public function findTermsCondDetailsById($id)
    {
        $termscond_details_by_id = HotelTermsConditions::find($id)->first();

        // $key_contact_number = explode(',',$vendor_details_by_id->key_contact_number);

        return response()->json([
            'status'=>200,
            'termscond_details_by_id' => $termscond_details_by_id
        ]);
    }
    /* find terms and condition details by id fucntion ending */

    /* update terms and condition details function starting */
    public function updateTermsAndConditions(Request $request,$id)
    {
        try{

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
            $updated_by = $request->input('updated_by');

            $hotel_id = $request->input('hotel_id');
            $general_tc = $request->input('general_tc');
            $cancellation_policy = $request->input('cancellation_policy');
            $cancellation_deadline = $request->input('cancellation_deadline');

            $validator = Validator::make($request->all(),[
                'general_tc'=>'required',
                'cancellation_policy'=>'required',
                'cancellation_deadline'=>'required',
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

            $update_vendor_details = DB::select(DB::raw("UPDATE tbl_hotel_terms_conditions SET general_tc='$general_tc',cancellation_policy='$cancellation_policy',
            cancellation_deadline='$cancellation_deadline',updated_at='$currentTime',updated_by='$updated_by' WHERE id='$id'"));

            return response()->json([
                'status'=>200,
                'message'=> 'terms and conditions updated'
            ]);

        }catch(\Exception $exception){

            return response()->json([
                'status'=>401,
                'message'=>$exception->getMessage()
            ]);

        }
    }
    /* update terms and condition details function ending */
}
