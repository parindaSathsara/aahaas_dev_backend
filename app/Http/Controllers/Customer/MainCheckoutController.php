<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainCheckoutController extends Controller
{
    public function fetchOrderDataByUser($id)
    {
        try {

            $OrderData = DB::table('tbl_checkouts')
                ->where('tbl_checkouts.cx_id', '=', $id)
                ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                ->leftJoin('users', 'tbl_checkouts.cx_id', '=', 'users.id')
                ->leftJoin('tbl_product_listing', 'tbl_checkouts.essnoness_id', '=', 'tbl_product_listing.id')
                ->leftJoin('edu_tbl_booking', 'tbl_checkouts.education_id', '=', 'edu_tbl_booking.booking_id')
                ->leftJoin('edu_tbl_rate', 'edu_tbl_booking.rate_id', '=', 'edu_tbl_rate.id')
                ->leftJoin('edu_tbl_education', 'edu_tbl_rate.edu_id', '=', 'edu_tbl_education.education_id')
                ->leftJoin('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
                ->leftJoin('tbl_lifestyle_bookings', 'tbl_checkouts.lifestyle_id', '=', 'tbl_lifestyle_bookings.lifestyle_booking_id')
                ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle_bookings.lifestyle_rate_id', '=', 'tbl_lifestyle_rates.lifestyle_rate_id')
                ->leftJoin('tbl_lifestyle', 'tbl_lifestyle_rates.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->leftJoin('tbl_hotel_resevation', 'tbl_checkouts.hotel_id', '=', 'tbl_hotel_resevation.id')
                ->select('*')
                ->get();


            return response(['status' => 200, 'responseData' => $OrderData]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
