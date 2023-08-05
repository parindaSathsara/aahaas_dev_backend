<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use App\Models\Education\EducationInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class EducationInventoryController extends Controller
{
    public function addNewEducationInventory(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            EducationInventory::create([
                'edu_id' => $request->input('edu_id'),
                'service_location_typeid'=> $request->input('service_location_typeid'),
                'course_inv_startdate'=> $request->input('course_inv_startdate'),
                'course_inv_enddate'=> $request->input('course_inv_enddate'),
                'course_day'=> $request->input('course_day'),
                'session_no'=> $request->input('session_no'),
                'common_session'=> $request->input('common_session'),
                'course_startime'=> $request->input('course_startime'),
                'course_endtime'=> $request->input('course_endtime'),
                'max_adult_occupancy'=> $request->input('max_adult_occupancy'),
                'max_child_occupancy'=> $request->input('max_child_occupancy'),
                'max_total_occupancy'=> $request->input('max_total_occupancy'),
                'total_inventory'=> $request->input('total_inventory'),
                'used_inventory'=> $request->input('used_inventory'),
                'blackout_date'=> $request->input('blackout_date'),
                'blackout_day'=> $request->input('blackout_day'),
                'inclusions'=> $request->input('inclusions'),
                'exclusions'=> $request->input('exclusions'),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
                'updated_by' => 'User 1',
            ]);


            return response()->json([
                'status' => 200,
                'message' => 'Education Inventory Created'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'error_message' => $exception->getMessage()

            ]);
        }
    }


    public function getEducationDataByID($id)
    {
        try {
            $education = DB::table('edu_tbl_servicelocation')->where('edu_vendor_id')->get();

            $educationVendors = DB::table('edu_tbl_servicelocation')->where('edu_vendor_id')->get();

            return response()->json([
                'status' => 200,
                'course_name' => $educationVendors
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }


    public function getInventoryIds($id){
        try{
            $educationInventoryIDs=DB::table('edu_tbl_inventory')
            ->where('edu_id',$id)
            ->get();

            return response()->json([
                'status' => 200,
                'inventoryData' => $educationInventoryIDs
            ]);

        }catch(\Exception $exception){
            return response()->json([
                'status'=>400,
                'error_message'=>throw $exception
            ]);
        }
    }


    
}
