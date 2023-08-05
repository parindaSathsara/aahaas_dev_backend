<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotels\HotelVendor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HotelVendorController extends Controller
{
    /* Fetch All vendor details function starting */
    public function index()
    {
        $all_vendor_data = DB::table('tbl_hotel_vendor')->get();

        return response()->json([
            'status' => 200,
            'all_vendor_data' => $all_vendor_data
        ]);
    }
    /* Fetch All vendor details function ending */

    /* create new vendor details function starting */
    public function createNewVendorDetails(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now()->toDateTimeString();
            $updated_by = $request->input('updated_by');

            $hotel_id = $request['hotelname'];
            $hotel_email = $request['hotelemail'];
            $official_address = $request['hoteladdress'];
            $hotel_contact = $request['hotelcontactnumber'];
            $key_contact_number = $request['keycontactnumber'];
            $key_contact_email = $request['keycontactemail'];
            $keyperson = $request['keyperson'];
            $keyperson = $request['keyperson'];
            $UserId = $request['userid'];

            $validator = Validator::make($request->all(), [
                'hotelname' => 'required',
                'hotelemail' => 'required',
                'hoteladdress' => 'required',
                'hotelcontactnumber' => 'required',
                'keycontactnumber' => 'required',
                'keycontactemail' => 'required',
                'keyperson' => 'required'
            ]);

            $Query = HotelVendor::where('updated_by', '=', $UserId)->count();

            // else if (!Auth::check()) {
            //     return response()->json([
            //         'status' => 403,
            //         'login_error' => 'Session Expired, Please log again'
            //     ]);
            // } 

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {

                if ($Query > 0) {
                    return response([
                        'status' => 401,
                        'message' => 'User already registered'
                    ]);
                } else {
                    $new_vendor_details = HotelVendor::create([
                        'hotel_id' => $hotel_id,
                        'hotel_email' => $hotel_email,
                        'official_address' => $official_address,
                        'hotel_contact' => $hotel_contact,
                        'key_person' => $keyperson,
                        'key_contact_number' => $key_contact_number,
                        'key_contact_email' => $key_contact_email,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                        'updated_by' => $UserId
                    ]);
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'New vendor details added',
                'details' => $new_vendor_details
            ]);
        } catch (\Exception $exception) {

            throw $exception;
        }
    }
    /* create new vendor details function ending */

    /* find vendor details by id fucntion starting */
    public function findVendorDetailsById($id)
    {
        $vendor_details_by_id = HotelVendor::find($id)->first();

        $key_contact_number = explode(',', $vendor_details_by_id->key_contact_number);

        return response()->json([
            'status' => 200,
            'vendor_details_by_id' => $vendor_details_by_id
        ]);
    }
    /* find vendor details by id fucntion ending */

    /* update vendor details function starting */
    public function updateVendorDetails(Request $request, $id)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
            $updated_by = $request->input('updated_by');

            $hotel_id = $request->input('hotel_id');
            $hotel_email = $request->input('hotel_email');
            $official_address = $request->input('official_address');
            $hotel_contact = $request->input('hotel_contact');
            $key_contact_number = $request->input('key_contact_number');
            $key_contact_email = $request->input('key_contact_email');

            $validator = Validator::make($request->all(), [
                'hotel_email' => 'required',
                'official_address' => 'required',
                'hotel_contact' => 'required',
                'key_contact_number' => 'required',
                'key_contact_email' => 'required',
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

            $update_vendor_details = DB::select(DB::raw("UPDATE tbl_hotel_vendor SET hotel_email='$hotel_email',official_address='$official_address',hotel_contact='$hotel_contact',key_contact_number='$key_contact_number',
            key_contact_email='$key_contact_email',updated_at='$currentTime',updated_by='$updated_by' WHERE id='$id'"));

            return response()->json([
                'status' => 200,
                'message' => 'vendor details updated'
            ]);
        } catch (\Exception $exception) {

            throw $exception;
        }
    }
    /* update vendor details function ending */

    public function getDetailsByUserId($id)
    {
        try {

            $Query = DB::table('tbl_hotel_vendor')->where('tbl_hotel_vendor.updated_by', '=', $id)->leftJoin('tbl_hotel', 'tbl_hotel_vendor.hotel_id', '=', 'tbl_hotel.id')->select('*')->get();

            return response([
                'status' => 200,
                'hotelvendordata' => $Query
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
