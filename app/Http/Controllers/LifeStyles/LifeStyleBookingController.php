<?php

namespace App\Http\Controllers\LifeStyles;

use App\Http\Controllers\Controller;
use App\Models\Customer\MainCheckout;
use App\Models\CustomerCustomCarts;
use App\Models\Lifestyle\LifeStyleBook;
// use App\Models\LifeStyle\LifeStyleBook;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LifeStyleBookingController extends Controller
{
    public function addNewLifeStyleBooking(Request $request)
    {
        try {
            $currentTime = \Carbon\Carbon::now()->toDateTimeString();
            $validator = Validator::make($request->all(), [
                'lifestyle_id' => 'required',
                'lifestyle_inventory_id' => 'required',
                'lifestyle_rate_id' => 'required',
                // 'lifestyle_discount_id' => 'required',
            ]);



            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {

                if ($request->input('viewStatus') == "update") {
                    $newLifeStyleBooking = LifeStyleBook::where('lifestyle_booking_id', $request->input('preId'))->update([
                        'lifestyle_id' => $request->input('lifestyle_id'),
                        'lifestyle_inventory_id' => $request->input('lifestyle_inventory_id'),
                        'lifestyle_rate_id' => $request->input('lifestyle_rate_id'),
                        'lifestyle_discount_id' => $request->input('lifestyle_discount_id'),
                        'lifestyle_children_details' => $request->input('lifestyle_children_details'),
                        'lifestyle_children_ages' => $request->input('lifestyle_children_ages'),
                        'lifestyle_adult_details' => $request->input('lifestyle_adult_details'),
                        'booking_date' => $request->input('booking_date'),
                        'lifestyle_children_count' => $request->input('lifestyle_children_count'),
                        'lifestyle_adult_count' => $request->input('lifestyle_adult_count'),
                        'booking_status' => 'Pending',

                        'user_id' => $request->input('user_id'),
                    ]);


                    CustomerCustomCarts::where('lifestyle_pre_id', $request->input('preId'))->update([
                        'main_category_id' => $request->input('main_category_id'),
                        'cart_id' => $request->input('cart_id'),
                        'listing_pre_id' => '',
                        // 'lifestyle_pre_id' => $newLifeStyleBooking->id,
                        'hotels_pre_id' => '',
                        'cart_status' => 'InCart',
                        'cart_added_date' => $currentTime,
                        'customer_id' => $request->input('user_id'),
                        'order_preffered_date' => $request->input('booking_date'),
                    ]);
                } else {

                    $LifeStyleBookingCount = DB::table('tbl_lifestyle_bookings')->where([
                        'lifestyle_inventory_id' => $request->input('lifestyle_inventory_id'),
                        'lifestyle_rate_id' => $request->input('lifestyle_rate_id'),
                        'user_id' => $request->input('user_id')
                    ])->count();

                    if ($LifeStyleBookingCount > 0) {
                        return response([
                            'status' => 300,
                            'message' => 'Product already added to cart'
                        ]);
                    } else {

                        $newLifeStyleBooking = LifeStyleBook::create([
                            'lifestyle_id' => $request->input('lifestyle_id'),
                            'lifestyle_inventory_id' => $request->input('lifestyle_inventory_id'),
                            'lifestyle_rate_id' => $request->input('lifestyle_rate_id'),
                            'lifestyle_discount_id' => $request->input('lifestyle_discount_id'),
                            'lifestyle_children_details' => $request->input('lifestyle_children_details'),
                            'lifestyle_children_ages' => $request->input('lifestyle_children_ages'),
                            'lifestyle_adult_details' => $request->input('lifestyle_adult_details'),
                            'booking_date' => $request->input('booking_date'),
                            'lifestyle_children_count' => $request->input('lifestyle_children_count'),
                            'lifestyle_adult_count' => $request->input('lifestyle_adult_count'),
                            'booking_status' => 'Pending',
                            'user_id' => $request->input('user_id'),
                        ]);


                        CustomerCustomCarts::create([
                            'main_category_id' => $request->input('main_category_id'),
                            'cart_id' => $request->input('cart_id'),
                            'listing_pre_id' => '',
                            'lifestyle_pre_id' => $newLifeStyleBooking->id,
                            'hotels_pre_id' => '',
                            'cart_status' => 'InCart',
                            'cart_added_date' => $currentTime,
                            'customer_id' => $request->input('user_id'),
                            'order_preffered_date' => $request->input('booking_date'),
                        ]);

                        if ($request->input('booking_status') == 'Booked') {
                            MainCheckout::create([
                                'checkout_id' => $request['orderid'],
                                'essnoness_id' => null,
                                'lifestyle_id' => $request->input('lifestyle_id'),
                                'education_id' => null,
                                'hotel_id' => null,
                                'flight_id' => null,
                                'main_category_id' => '3',
                                'quantity' => null,
                                'each_item_price' => null,
                                'total_price' => $request->input('discountMrp'),
                                'discount_price' => $request->input('discount'),
                                'bogof_item_name' => null,
                                'delivery_charge' => null,
                                'discount_type' => null,
                                'child_rate' => $request['childrate'],
                                'adult_rate' => $request['adultrate'],
                                'discountable_child_rate' => null,
                                'discountable_adult_rate' => null,
                                'flight_trip_type' => null,
                                'flight_total_price' => null,
                                'related_order_id' => $request->input('lifestyle_id'),
                                'status' => 'CustomerOrdered',
                                'delivery_status' => null,
                                'cx_id' => $newLifeStyleBooking->user_id,
                            ]);
                        }

                        return response([
                            'status' => 200,
                            'message' => 'Successfull',
                            'booking' => $newLifeStyleBooking
                        ]);
                    }
                }
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }

    public function get_lifestyles()
    {
        $lifeStyles = DB::table('tbl_lifestyle')
            ->join('tbl_lifestyle_detail', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_detail.lifestyle_id')
            ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_rates.lifestyle_id')
            ->select(
                DB::raw("min(tbl_lifestyle_rates.adult_rate) AS adult_rate"),
                DB::raw("min(tbl_lifestyle_rates.child_rate) AS child_rate"),
                'tbl_lifestyle.lifestyle_city',
                'tbl_lifestyle.lifestyle_attraction_type',
                'tbl_lifestyle.lifestyle_name',
                'tbl_lifestyle.lifestyle_description',
                'tbl_lifestyle.image',
                'tbl_lifestyle.lifestyle_id'
            )


            ->groupBy('tbl_lifestyle.lifestyle_id')
            ->get();


        return response()->json([
            'status' => 200,
            'lifeStylesData' => $lifeStyles,

        ]);
    }
}
