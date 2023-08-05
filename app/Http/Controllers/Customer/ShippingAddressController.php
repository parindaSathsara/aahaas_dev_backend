<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ShippingAddressController extends Controller
{
    public function createNewShippingAddress(Request $request)
    {
        $ContactName = $request['contact_name'];
        $MobileNumber = $request['mobile_number'];
        $Country = $request['country'];
        $LatLong = explode(',', $request['latlong']);
        $AddressFull = $request['address_full'];
        $UserId = $request['user_id'];

        $Validator = Validator::make($request->all(), [
            'contact_name',
            'mobile_number',
            'country',
            'address_full'
        ]);

        try {
            if ($Validator->fails()) {
                return response()->json([
                    'status' => 403,
                    'validation_error' => $Validator->messages()
                ]);
            } else {
                $NewAddr = ShippingAddress::create([
                    'contact_name' => $ContactName,
                    'mobile_number' => $MobileNumber,
                    'country' => $Country,
                    'latitude' => $LatLong[0],
                    'longtitude' => $LatLong[1],
                    'address_full' => $AddressFull,
                    'user_id' => $UserId
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'New Shipping Address Created',
                    'data' => $NewAddr
                ]);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getShippingDataByUser($id)
    {
        try {

            $Query = DB::table('shipping_address')->where('user_id', '=', $id)->select('*')->get();

            return response(['status' => 200, 'shipping_data' => $Query]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function deleteShippingData($id)
    {
        try {

            $Query = DB::table('shipping_address')->where('id', '=', $id)->delete();

            return response(['status' => 200]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
