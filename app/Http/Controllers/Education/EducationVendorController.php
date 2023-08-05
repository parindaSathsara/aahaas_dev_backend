<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use App\Models\Education\EducationVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EducationVendorController extends Controller
{
    public function getAllEducationVendors(Request $request)
    {
        try {
            $educationVendors = DB::table('edu_tbl_vendor')
                ->select('edu_tbl_vendor.vendor_name')
                ->get();

            return response()->json([
                'status' => 200,
                'educationVendorTable' => $educationVendors
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'error_message' => throw $exception

            ]);
        }
    }

    public function createNewEduVendor(Request $request)
    {

        $VendorName = $request['vendor__Name'];
        $VendorEmail = $request['vendor__Email'];
        $VendorContact = $request['vendor__ContactNumber'];
        $VendorExperiance = $request['vendor__NOofEx'];
        $VendorEduQual = $request['vendor__EduQual'];
        $VendorWeb = $request['vendor__Website'];
        $VendorResProcess = $request['vendor__ReseProce'];
        $VendorAddDetails = $request['vendor__AddDetails'];
        $VendorId = $request['userid'];


        $validator = Validator::make($request->all(), [
            'vendor__Name' => 'required',
            'vendor__Email' => 'required|email|',
            'vendor__ContactNumber' => 'required',
            'vendor__NOofEx' => 'required',
            'vendor__EduQual' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'educationVendorTable' => $validator->messages()
            ]);
        }


        $currentTime = \Carbon\Carbon::now()->toDateTimeString();

        try {
            EducationVendor::create([
                'vendor_name' => $VendorName,
                'vendor_email' => $VendorEmail,
                'vendor_tel_no' => $VendorContact,
                'vendor_website' => $VendorWeb,
                'vendor_reservation_process' => $VendorResProcess,
                'vendor_type' => 'Lecturer',
                'no_of_experiance' => $VendorExperiance,
                'edu_qualifications' => $VendorEduQual,
                'additional_details' => $VendorAddDetails,
                'user_id' => $VendorId,
                'mark_up' => '-',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
                'updated_by' => $VendorEmail,
            ]);

            // return 'Test';

            return response()->json([
                'status' => 200,
                'message' => 'New Educational Vendor Created'

            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'error_message' => throw $ex

            ]);
        }
    }
}
