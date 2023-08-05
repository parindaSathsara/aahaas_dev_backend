<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /* Creating address field for each client function starting */
    public function createAddress(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'street' => 'required',
                'city' => 'required',
                'province' => 'required',
                'latitude' => 'required',
                'longtitude' => 'required',
                'zip_code' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {
                $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

                $address = Address::create([
                    'customer_id' => $request->input('customer_id'),
                    'street' => $request->input('street'),
                    'other' => $request->input('other'),
                    'city' => $request->input('city'),
                    'province' => $request->input('province'),
                    'latitude' => $request->input('latitude'),
                    'longtitude' => $request->input('longtitude'),
                    'zip_code' => $request->input('zip_code'),
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Customer Address Data Created !'
                ]);
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }

    /* Creating address field for each client function ending */

    /* Updating Address field for each client function Starting */

    public function updateAddress(Request $request, $id)
    {

        try {

            $validator = Validator::make($request->all(), [
                'street' => 'required',
                'city' => 'required',
                'province' => 'required',
                'latitude' => 'required',
                'longtitude' => 'required',
                'zip_code' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {
                $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

                // $id = $request->input('id');
                $street = $request->input('street');
                $other = $request->input('other');
                $city = $request->input('city');
                $province = $request->input('province');
                $latitude = $request->input('latitude');
                $longtitude = $request->input('longtitude');
                $zip_code = $request->input('zip_code');

                $addressUpdate = DB::select(DB::raw("UPDATE tbl_addresses SET 
                                        street='$street',other='$other',city='$city',province='$province',
                                        latitude='$latitude',longtitude='$longtitude', zip_code='$zip_code', updated_at='$currentTime' WHERE customer_id='$id'"));


                return response()->json([
                    'status' => 200,
                    'message' => 'Customer Address Information Updated'
                ]);
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'error_message' => $exception->getMessage()
            ]);
        }
    }

    /* Updating Address field for each client function Ending */

    public function getAllCities()
    {
        $cities = DB::table('cities')->get();

        return response()->json([
            'status' => 200,
            'cities' => $cities
        ]);
    }


    public function getAddressByID($id)
    {
        $addresses = DB::table('shipping_address')->where('user_id', $id)->get();

        return response()->json([
            'status' => 200,
            'addresses' => $addresses
        ]);
    }

    public function getAddressByAddressID($id)
    {
        $addresses = DB::table('shipping_address')->where('id', $id)->get();

        return response()->json([
            'status' => 200,
            'addresses' => $addresses
        ]);
    }
}
