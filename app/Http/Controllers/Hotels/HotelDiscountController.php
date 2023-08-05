<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotels\HotelDiscount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HotelDiscountController extends Controller
{
    /* Get all the hotel discount data function starting */
    public function index()
    {
        $get_all_hotel_discounts = DB::table('tbl_hotel_discount')->get();

        return response()->json([
            'status' => 200,
            'hotel_discount_data' => $get_all_hotel_discounts
        ]);
    }
    /* Get all the hotel discount data function ending */

    /* create new discount for hotel function starting */
    public function createNewDiscount(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
            $updated_by = $request->input('updated_by');

            $hotel_id = $request->input('hotel_id');
            $discount_type = $request->input('discount_type');
            $room_category = $request->input('room_category');
            $offer_product = $request->input('offered_product');
            $user_level = $request->input('user_level');
            $discount_limit = (int)$request->input('discount_limit');
            $offer_type = $request->input('offer_type');
            $offer_value = $request->input('offer_value');
            $sale_start_date = date('Y-m-d H:i:s', strtotime($request->input('sale_start_date')));
            $sale_end_date = date('Y-m-d H:i:s', strtotime($request->input('sale_end_date')));

            $validator = Validator::make($request->all(), [
                'discount_type' => 'required',
                'room_category' => 'required',
                'offered_product' => 'required',
                'user_level' => 'required',
                'discount_limit' => 'required',
                'offer_value' => 'required',
                'sale_start_date' => 'required',
                'sale_end_date' => 'required',
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

            $new_hotel_discount = HotelDiscount::create([
                'hotel_id' => $hotel_id,
                'discount_type' => $discount_type,
                'room_category' => $room_category,
                'offered_product' => $offer_product,
                'user_level' => $user_level,
                'discount_limit' => $discount_limit,
                'offer_type' => $offer_type,
                'offer_value' => $offer_value,
                'sale_start_date' => $sale_start_date,
                'sale_end_date' => $sale_end_date,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
                'updated_by' => $updated_by
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'New discount added'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* create new discount for hotel function ending */

    /* Fetch discount data by id function starting */
    public function fetchDiscountById($id)
    {
        $fetch_discount_byid = HotelDiscount::find($id)->first();

        return response()->json([
            'status' => 200,
            'discount_byid' => $fetch_discount_byid
        ]);
    }
    /* Fetch discount data by id function ending */

    /* update hotel discount data by id function starting */
    public function updateHotelDiscountData(Request $request, $id)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
            $updated_by = $request->input('updated_by');

            // $hotel_id = $request->input('hotel_id');
            $discount_type = $request->input('discount_type');
            $room_category = $request->input('room_category');
            $offer_product = $request->input('offered_product');
            $user_level = $request->input('user_level');
            $discount_limit = (int)$request->input('discount_limit');
            $offer_type = $request->input('offer_type');
            $offer_value = $request->input('offer_value');
            $sale_start_date = date('Y-m-d H:i:s', strtotime($request->input('sale_start_date')));
            $sale_end_date = date('Y-m-d H:i:s', strtotime($request->input('sale_end_date')));

            $validator = Validator::make($request->all(), [
                'discount_type' => 'required',
                'room_category' => 'required',
                'offered_product' => 'required',
                'user_level' => 'required',
                'discount_limit' => 'required',
                'offer_value' => 'required',
                'sale_start_date' => 'required',
                'sale_end_date' => 'required',
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

            $update_DiscountData = DB::select(DB::raw("UPDATE tbl_hotel_discount SET discount_type='$discount_type',room_category='$room_category',offered_product='$offer_product',user_level='$user_level',
            discount_limit='$discount_limit',offer_type='$offer_type',offer_value='$offer_value',sale_start_date='$sale_start_date',
            sale_end_date='$sale_end_date',updated_at='$currentTime',updated_by='$updated_by' WHERE id='$id'"));

            return response()->json([
                'status' => 200,
                'message' => 'hotel discount data updated'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* update hotel discount data by id function ending */
}
