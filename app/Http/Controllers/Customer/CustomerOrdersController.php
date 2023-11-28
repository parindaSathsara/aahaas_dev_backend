<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrdersController extends Controller
{
    /* Fetch Customer Essential Orders by Id*/
    public function fetchEssentialsByCusId($id, $catID)
    {
        try {

            $OrderData = DB::table('tbl_products_orders')->where('customer_id', '=', $id)
                ->join('tbl_listing_inventory', 'tbl_products_orders.inventory_id', '=', 'tbl_listing_inventory.id')
                ->join('tbl_product_listing', 'tbl_products_orders.listing_id', '=', 'tbl_product_listing.id')
                ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
                ->where('tbl_product_details.category1', $catID)
                ->get();

            return response(['status' => 200, 'order_data' => $OrderData]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }

    /* Fetch Customer Education Orders by Id */
    public function fetchEducationByCusId($id)
    {
        try {

            $EducationData = DB::table('edu_tbl_booking')->where('user_id', '=', $id)
                ->join('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')
                ->select('*', 'edu_tbl_booking.status AS booking_status')->get();

            return response(['status' => 200, 'edu_data' => $EducationData]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }


    public function getCustomerCardData($id)
    {
        $pendingCount = DB::table('tbl_checkout_ids')->where('user_id', $id)->where('checkout_status', "CustomerOrdered")->count();
        $ongoingCount = DB::table('tbl_checkout_ids')->where('user_id', $id)->where('checkout_status', "Approved")->count();
        $completedCount = DB::table('tbl_checkout_ids')->where('user_id', $id)->where('checkout_status', "Completed")->count();

        $pendingBalance = DB::table('tbl_checkout_ids')->selectRaw("SUM(balance_amount) as total_amount")->where('user_id', $id)->where('payment_type', "MinimumPayment")->whereNot('checkout_status', "Completed")->first();


        return response(["status" => 200, 'pending' => $pendingCount, 'ongoing' => $ongoingCount, 'completed' => $completedCount, 'pendingBalance' => $pendingBalance->total_amount == null ? 0 : $pendingBalance->total_amount]);
    }






    public function getCustomerCancelledOrders($id)
    {
        $Query = DB::table('tbl_checkouts')->where('tbl_checkouts.cx_id', '=', $id)
            ->where('tbl_checkouts.status', '=', 'Cancel')
            // ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
            ->leftJoin('tbl_product_listing', 'tbl_checkouts.essnoness_id', '=', 'tbl_product_listing.id')
            ->leftJoin('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
            ->leftJoin('tbl_lifestyle_bookings', 'tbl_checkouts.related_order_id', '=', 'tbl_lifestyle_bookings.lifestyle_booking_id')
            ->leftJoin('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
            ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle_bookings.lifestyle_rate_id', '=', 'tbl_lifestyle_rates.lifestyle_rate_id')
            ->leftJoin('edu_tbl_booking', 'tbl_checkouts.related_order_id', '=', 'edu_tbl_booking.booking_id')
            ->leftJoin('edu_tbl_rate', 'edu_tbl_booking.rate_id', '=', 'edu_tbl_rate.id')
            ->leftJoin('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')

            ->leftJoin('tbl_hotels_pre_booking', 'tbl_checkouts.related_order_id', '=', 'tbl_hotels_pre_booking.booking_id')
            ->leftJoin('tbl_hotel_resevation', 'tbl_hotels_pre_booking.booking_id', '=', 'tbl_hotel_resevation.pre_id')
            ->leftJoin('tbl_flight_resevation', 'tbl_checkouts.flight_id', '=', 'tbl_flight_resevation.id')
            ->leftJoin('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', '=', 'tbl_essentials_preorder.essential_pre_order_id')
            ->select(
                '*',

                'tbl_checkouts.*',
                'tbl_checkouts.currency AS ItemCurrency',
                'tbl_checkouts.quantity AS ReqQTy',
                'tbl_maincategory.maincat_type AS CategoryType',
                'tbl_essentials_preorder.preffered_date AS EssPrefDelDate',
                'tbl_lifestyle_rates.cancellation_days AS LifeStyleCancel',
                'tbl_lifestyle_bookings.booking_date AS LifeStylePrefDate',
                'tbl_lifestyle_bookings.booking_status AS LifeStyleBookStatus',
                'tbl_lifestyle.image AS LSImage',
                'tbl_hotel_resevation.cancelation_deadline AS HotelCancelDate',
                'tbl_hotels_pre_booking.booking_total AS HotelTotAmount',

                'tbl_hotel_resevation.no_of_adults AS NoAdults',
                'tbl_hotel_resevation.no_of_childs AS NoChilds',
                'tbl_hotel_resevation.bed_type AS BedType',
                'tbl_hotel_resevation.room_type AS RoomType',
                'tbl_hotel_resevation.board_code AS BoardType',
                'tbl_hotel_resevation.status AS HotelResStatus',
                'edu_tbl_booking.status AS EduBookStatus',
                'edu_tbl_booking.booking_id AS EduBookId',
                'edu_tbl_booking.booking_date AS EduBookDate',
                'tbl_flight_resevation.booking_status AS FlightBookStat',
                'edu_tbl_rate.deadline_no_ofdays AS DeadlineNoDays',


            )
            ->orderBy('tbl_checkouts.checkout_id', 'DESC')
            ->get();


        return response(['status' => 200, 'dataSet' => $Query]);
    }


    public function getCustomerRecentOrders($id)
    {
        $Query = DB::table('tbl_checkouts')->where('tbl_checkouts.cx_id', '=', $id)
            // ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
            ->leftJoin('tbl_product_listing', 'tbl_checkouts.essnoness_id', '=', 'tbl_product_listing.id')
            ->leftJoin('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
            ->leftJoin('tbl_lifestyle_bookings', 'tbl_checkouts.related_order_id', '=', 'tbl_lifestyle_bookings.lifestyle_booking_id')
            ->leftJoin('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
            ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle_bookings.lifestyle_rate_id', '=', 'tbl_lifestyle_rates.lifestyle_rate_id')
            ->leftJoin('edu_tbl_booking', 'tbl_checkouts.related_order_id', '=', 'edu_tbl_booking.booking_id')
            ->leftJoin('edu_tbl_rate', 'edu_tbl_booking.rate_id', '=', 'edu_tbl_rate.id')
            ->leftJoin('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')

            ->leftJoin('tbl_hotels_pre_booking', 'tbl_checkouts.related_order_id', '=', 'tbl_hotels_pre_booking.booking_id')
            ->leftJoin('tbl_hotel_resevation', 'tbl_hotels_pre_booking.booking_id', '=', 'tbl_hotel_resevation.pre_id')
            ->leftJoin('tbl_flight_resevation', 'tbl_checkouts.flight_id', '=', 'tbl_flight_resevation.id')
            ->leftJoin('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', '=', 'tbl_essentials_preorder.essential_pre_order_id')
            ->select(
                '*',

                'tbl_checkouts.*',
                'tbl_checkouts.currency AS ItemCurrency',
                'tbl_checkouts.quantity AS ReqQTy',
                'tbl_maincategory.maincat_type AS CategoryType',
                'tbl_essentials_preorder.preffered_date AS EssPrefDelDate',
                'tbl_lifestyle_rates.cancellation_days AS LifeStyleCancel',
                'tbl_lifestyle_bookings.booking_date AS LifeStylePrefDate',
                'tbl_lifestyle_bookings.booking_status AS LifeStyleBookStatus',
                'tbl_lifestyle.image AS LSImage',
                'tbl_hotel_resevation.cancelation_deadline AS HotelCancelDate',
                'tbl_hotels_pre_booking.booking_total AS HotelTotAmount',

                'tbl_hotel_resevation.no_of_adults AS NoAdults',
                'tbl_hotel_resevation.no_of_childs AS NoChilds',
                'tbl_hotel_resevation.bed_type AS BedType',
                'tbl_hotel_resevation.room_type AS RoomType',
                'tbl_hotel_resevation.board_code AS BoardType',
                'tbl_hotel_resevation.status AS HotelResStatus',
                'edu_tbl_booking.status AS EduBookStatus',
                'edu_tbl_booking.booking_id AS EduBookId',
                'edu_tbl_booking.booking_date AS EduBookDate',
                'tbl_flight_resevation.booking_status AS FlightBookStat',
                'edu_tbl_rate.deadline_no_ofdays AS DeadlineNoDays',


            )
            ->orderBy('tbl_checkouts.checkout_id', 'DESC')
            ->get();


        return response(['status' => 200, 'dataSet' => $Query]);
    }



    /* Fetch Customer LifeStyle Order by Id */
    public function fetchLifeStyleCusId($id)
    {
        try {

            $LifeStyleData = DB::table('tbl_lifestyle_bookings')->where('user_id', '=', $id)
                ->join('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->select('*')
                ->get();

            return response(['status' => 200, 'ls_data' => $LifeStyleData]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }

    /* Fetch Customer Hotel Order by Id */
    public function fetchHotelCusId($id)
    {
        try {

            $HotelData = DB::table('tbl_hotel_resevation')->where('user_id', '=', $id)
                // ->join('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->select('*')
                ->get();

            return response(['status' => 200, 'hotel_data' => $HotelData]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }


    public function getOrderDetailsByCusIDOrderID($oid)
    {
        $Query = DB::table('tbl_checkouts')
            ->where('tbl_checkouts.checkout_id', '=', $oid)
            ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')

            ->leftJoin('tbl_product_listing', 'tbl_checkouts.essnoness_id', '=', 'tbl_product_listing.id')
            ->leftJoin('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
            ->leftJoin('tbl_lifestyle_bookings', 'tbl_checkouts.related_order_id', '=', 'tbl_lifestyle_bookings.lifestyle_booking_id')
            ->leftJoin('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
            ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle_bookings.lifestyle_rate_id', '=', 'tbl_lifestyle_rates.lifestyle_rate_id')
            ->leftJoin('edu_tbl_booking', 'tbl_checkouts.related_order_id', '=', 'edu_tbl_booking.booking_id')
            ->leftJoin('edu_tbl_rate', 'edu_tbl_booking.rate_id', '=', 'edu_tbl_rate.id')
            ->leftJoin('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')

            ->leftJoin('tbl_hotels_pre_booking', 'tbl_checkouts.related_order_id', '=', 'tbl_hotels_pre_booking.booking_id')
            ->leftJoin('tbl_hotel_resevation', 'tbl_hotels_pre_booking.booking_id', '=', 'tbl_hotel_resevation.pre_id')
            ->leftJoin('tbl_flight_resevation', 'tbl_checkouts.flight_id', '=', 'tbl_flight_resevation.id')
            ->leftJoin('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', '=', 'tbl_essentials_preorder.essential_pre_order_id')
            ->select(
                '*',

                'tbl_checkouts.*',
                'tbl_checkouts.currency AS ItemCurrency',
                'tbl_checkouts.quantity AS ReqQTy',
                'tbl_maincategory.maincat_type AS CategoryType',
                'tbl_essentials_preorder.preffered_date AS EssPrefDelDate',
                'tbl_lifestyle_rates.cancellation_days AS LifeStyleCancel',
                'tbl_lifestyle_bookings.booking_date AS LifeStylePrefDate',
                'tbl_lifestyle_bookings.booking_status AS LifeStyleBookStatus',
                'tbl_lifestyle.image AS LSImage',
                'tbl_hotel_resevation.cancelation_deadline AS HotelCancelDate',
                'tbl_hotels_pre_booking.booking_total AS HotelTotAmount',

                'tbl_hotel_resevation.no_of_adults AS NoAdults',
                'tbl_hotel_resevation.no_of_childs AS NoChilds',
                'tbl_hotel_resevation.bed_type AS BedType',
                'tbl_hotel_resevation.room_type AS RoomType',
                'tbl_hotel_resevation.board_code AS BoardType',
                'tbl_hotel_resevation.status AS HotelResStatus',
                'edu_tbl_booking.status AS EduBookStatus',
                'edu_tbl_booking.booking_id AS EduBookId',
                'edu_tbl_booking.booking_date AS EduBookDate',
                'tbl_flight_resevation.booking_status AS FlightBookStat',
                'edu_tbl_rate.deadline_no_ofdays AS DeadlineNoDays',


            )
            ->orderBy('tbl_checkouts.checkout_id', 'DESC')
            ->get();


        return response(['status' => 200, 'dataSet' => $Query]);
    }

    /* Fetch all order by user id */
    public function fetchAllOrderByUserId($id, $status)
    {

        if ($status == "All") {
            try {

                $Query = DB::table('tbl_checkouts')->where('tbl_checkouts.cx_id', '=', $id)
                    // ->where('tbl_checkouts.status', $status)
                    ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                    ->leftJoin('tbl_product_listing', 'tbl_checkouts.essnoness_id', '=', 'tbl_product_listing.id')
                    ->leftJoin('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
                    ->leftJoin('tbl_lifestyle_bookings', 'tbl_checkouts.related_order_id', '=', 'tbl_lifestyle_bookings.lifestyle_booking_id')
                    ->leftJoin('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                    ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle_bookings.lifestyle_rate_id', '=', 'tbl_lifestyle_rates.lifestyle_rate_id')
                    ->leftJoin('edu_tbl_booking', 'tbl_checkouts.related_order_id', '=', 'edu_tbl_booking.booking_id')
                    ->leftJoin('edu_tbl_rate', 'edu_tbl_booking.rate_id', '=', 'edu_tbl_rate.id')
                    ->leftJoin('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')

                    ->leftJoin('tbl_hotels_pre_booking', 'tbl_checkouts.related_order_id', '=', 'tbl_hotels_pre_booking.booking_id')
                    ->leftJoin('tbl_hotel_resevation', 'tbl_hotels_pre_booking.booking_id', '=', 'tbl_hotel_resevation.pre_id')
                    ->leftJoin('tbl_flight_resevation', 'tbl_checkouts.flight_id', '=', 'tbl_flight_resevation.id')
                    ->leftJoin('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', '=', 'tbl_essentials_preorder.essential_pre_order_id')


                    ->select(
                        '*',
                        'tbl_checkout_ids.id AS OrderId',
                        'tbl_checkouts.id AS MainTId',
                        'tbl_checkouts.*',
                        'tbl_checkouts.currency AS ItemCurrency',
                        'tbl_checkouts.quantity AS ReqQTy',
                        'tbl_maincategory.maincat_type AS CategoryType',
                        'tbl_essentials_preorder.preffered_date AS EssPrefDelDate',
                        'tbl_lifestyle_rates.cancellation_days AS LifeStyleCancel',
                        'tbl_lifestyle_bookings.booking_date AS LifeStylePrefDate',
                        'tbl_lifestyle_bookings.booking_status AS LifeStyleBookStatus',
                        'tbl_hotel_resevation.cancelation_deadline AS HotelCancelDate',
                        'tbl_hotels_pre_booking.booking_total AS HotelTotAmount',

                        'tbl_hotel_resevation.no_of_adults AS NoAdults',
                        'tbl_hotel_resevation.no_of_childs AS NoChilds',
                        'tbl_hotel_resevation.bed_type AS BedType',
                        'tbl_hotel_resevation.room_type AS RoomType',
                        'tbl_hotel_resevation.board_code AS BoardType',
                        'tbl_hotel_resevation.status AS HotelResStatus',
                        'edu_tbl_booking.status AS EduBookStatus',
                        'edu_tbl_booking.booking_id AS EduBookId',
                        'edu_tbl_booking.booking_date AS EduBookDate',
                        'tbl_flight_resevation.booking_status AS FlightBookStat',
                        'edu_tbl_rate.deadline_no_ofdays AS DeadlineNoDays',


                    )
                    ->orderBy('tbl_checkouts.checkout_id', 'DESC')
                    ->get();

                $Query2 = DB::table('tbl_checkouts')->where('tbl_checkouts.cx_id', '=', $id)
                    ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                    ->select('tbl_checkouts.checkout_id AS OrderId', 'tbl_checkout_ids.checkout_date AS BookedDay', 'tbl_checkout_ids.checkout_status AS BookStatus', 'tbl_checkout_ids.*', 'tbl_checkouts.*')
                    ->orderBy('tbl_checkouts.checkout_id', 'DESC')
                    ->groupBy('tbl_checkouts.checkout_id')->get();

                return response([
                    'status' => 200,
                    'query_data1' => $Query,
                    'query_data2' => $Query2
                ]);
            } catch (\Exception $ex) {
                throw $ex;
            }
        } else {
            try {

                $Query = DB::table('tbl_checkouts')->where('tbl_checkouts.cx_id', '=', $id)
                    ->where('tbl_checkouts.status', $status)
                    ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                    ->leftJoin('tbl_product_listing', 'tbl_checkouts.essnoness_id', '=', 'tbl_product_listing.id')
                    ->leftJoin('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
                    ->leftJoin('tbl_lifestyle_bookings', 'tbl_checkouts.related_order_id', '=', 'tbl_lifestyle_bookings.lifestyle_booking_id')
                    ->leftJoin('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                    ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle_bookings.lifestyle_rate_id', '=', 'tbl_lifestyle_rates.lifestyle_rate_id')
                    ->leftJoin('edu_tbl_booking', 'tbl_checkouts.related_order_id', '=', 'edu_tbl_booking.booking_id')
                    ->leftJoin('edu_tbl_rate', 'edu_tbl_booking.rate_id', '=', 'edu_tbl_rate.id')
                    ->leftJoin('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')

                    ->leftJoin('tbl_hotels_pre_booking', 'tbl_checkouts.related_order_id', '=', 'tbl_hotels_pre_booking.booking_id')
                    ->leftJoin('tbl_hotel_resevation', 'tbl_hotels_pre_booking.booking_id', '=', 'tbl_hotel_resevation.pre_id')
                    ->leftJoin('tbl_flight_resevation', 'tbl_checkouts.flight_id', '=', 'tbl_flight_resevation.id')
                    ->leftJoin('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', '=', 'tbl_essentials_preorder.essential_pre_order_id')


                    ->select(
                        '*',
                        'tbl_checkout_ids.id AS OrderId',
                        'tbl_checkouts.id AS MainTId',
                        'tbl_checkouts.*',
                        'tbl_checkouts.currency AS ItemCurrency',
                        'tbl_checkouts.quantity AS ReqQTy',
                        'tbl_maincategory.maincat_type AS CategoryType',
                        'tbl_essentials_preorder.preffered_date AS EssPrefDelDate',
                        'tbl_lifestyle_rates.cancellation_days AS LifeStyleCancel',
                        'tbl_lifestyle_bookings.booking_date AS LifeStylePrefDate',
                        'tbl_lifestyle_bookings.booking_status AS LifeStyleBookStatus',
                        'tbl_hotel_resevation.cancelation_deadline AS HotelCancelDate',
                        'tbl_hotels_pre_booking.booking_total AS HotelTotAmount',

                        'tbl_hotel_resevation.no_of_adults AS NoAdults',
                        'tbl_hotel_resevation.no_of_childs AS NoChilds',
                        'tbl_hotel_resevation.bed_type AS BedType',
                        'tbl_hotel_resevation.room_type AS RoomType',
                        'tbl_hotel_resevation.board_code AS BoardType',
                        'tbl_hotel_resevation.status AS HotelResStatus',
                        'edu_tbl_booking.status AS EduBookStatus',
                        'edu_tbl_booking.booking_id AS EduBookId',
                        'edu_tbl_booking.booking_date AS EduBookDate',
                        'tbl_flight_resevation.booking_status AS FlightBookStat',
                        'edu_tbl_rate.deadline_no_ofdays AS DeadlineNoDays',


                    )
                    ->orderBy('tbl_checkouts.checkout_id', 'DESC')
                    ->get();

                $Query2 = DB::table('tbl_checkouts')->where('tbl_checkouts.cx_id', '=', $id)
                    ->where('tbl_checkouts.status', $status)
                    ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                    ->select('tbl_checkouts.checkout_id AS OrderId', 'tbl_checkout_ids.checkout_date AS BookedDay', 'tbl_checkout_ids.checkout_status AS BookStatus', 'tbl_checkout_ids.*', 'tbl_checkouts.*')
                    ->orderBy('tbl_checkouts.checkout_id', 'DESC')
                    ->groupBy('tbl_checkouts.checkout_id')->get();

                return response([
                    'status' => 200,
                    'query_data1' => $Query,
                    'query_data2' => $Query2
                ]);
            } catch (\Exception $ex) {
                throw $ex;
            }
        }

    }

    public function getDetailsByOrderId($id)
    {
        try {

            $Query = DB::table('tbl_checkouts')->where('tbl_checkouts.checkout_id', '=', $id)
                ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                ->leftJoin('tbl_product_listing', 'tbl_checkouts.essnoness_id', '=', 'tbl_product_listing.id')
                ->leftJoin('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
                ->leftJoin('tbl_lifestyle_bookings', 'tbl_checkouts.related_order_id', '=', 'tbl_lifestyle_bookings.lifestyle_booking_id')
                ->leftJoin('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle_bookings.lifestyle_rate_id', '=', 'tbl_lifestyle_rates.lifestyle_rate_id')
                ->leftJoin('edu_tbl_booking', 'tbl_checkouts.related_order_id', '=', 'edu_tbl_booking.booking_id')
                ->leftJoin('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')

                ->leftJoin('tbl_hotels_pre_booking', 'tbl_checkouts.related_order_id', '=', 'tbl_hotels_pre_booking.booking_id')
                ->leftJoin('tbl_hotel_resevation', 'tbl_hotels_pre_booking.booking_id', '=', 'tbl_hotel_resevation.pre_id')

                ->leftJoin('tbl_flight_resevation', 'tbl_checkouts.flight_id', '=', 'tbl_flight_resevation.id')
                ->leftJoin('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', '=', 'tbl_essentials_preorder.essential_pre_order_id')
                ->select(
                    '*',
                    'tbl_checkout_ids.id AS OrderId',
                    'tbl_checkouts.id AS MainTId',
                    'tbl_checkouts.*',
                    'tbl_checkouts.currency AS ItemCurrency',
                    'tbl_checkouts.quantity AS ReqQTy',
                    'tbl_maincategory.maincat_type AS CategoryType',
                    'tbl_essentials_preorder.preffered_date AS EssPrefDelDate',
                    'tbl_lifestyle_rates.cancellation_days AS LifeStyleCancel',
                    'tbl_lifestyle_bookings.booking_date AS LifeStylePrefDate',
                    'tbl_lifestyle_bookings.booking_status AS LifeStyleBookStatus',
                    'tbl_lifestyle_bookings.lifestyle_booking_id AS LifeStyleBookId',
                    'tbl_hotel_resevation.cancelation_deadline AS HotelCancelDate',
                    'tbl_hotels_pre_booking.booking_total AS HotelTotAmount',
                    'tbl_hotel_resevation.no_of_adults AS NoAdults',
                    'tbl_hotel_resevation.no_of_childs AS NoChilds',
                    'tbl_hotel_resevation.bed_type AS BedType',
                    'tbl_hotel_resevation.room_type AS RoomType',
                    'tbl_hotel_resevation.board_code AS BoardType',
                    'tbl_hotel_resevation.status AS HotelResStatus',
                    'edu_tbl_booking.status AS EduBookStatus',
                    'edu_tbl_booking.booking_id AS EduBookId',
                    'edu_tbl_booking.booking_date AS EduBookDate',
                    'tbl_flight_resevation.booking_status AS FlightBookStat'
                )
                ->orderBy('tbl_checkouts.checkout_id', 'DESC')
                ->get();

            return response([
                'status' => 200,
                'q_data' => $Query,
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getStatusCountByUserId($id)
    {
        try {
            $PendingCount = DB::table('tbl_checkouts')->where('tbl_checkouts.cx_id', '=', $id)
                ->where('tbl_checkouts.delivery_status', 'Pending')
                ->count();

            $OngoingCount = DB::table('tbl_checkouts')->where('tbl_checkouts.cx_id', '=', $id)
                ->where('tbl_checkouts.status', 'Booked')
                ->count();

            $CompletedCount = DB::table('tbl_checkouts')->where('tbl_checkouts.cx_id', '=', $id)
                ->where('tbl_checkouts.delivery_status', 'Confirmed', )
                ->count();

            $Products = DB::table('tbl_checkouts')->where('tbl_checkouts.cx_id', '=', $id)
                ->leftJoin('tbl_lifestyle', 'tbl_checkouts.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->leftJoin('edu_tbl_education', 'tbl_checkouts.education_id', '=', 'edu_tbl_education.education_id')
                ->leftJoin('tbl_hotel', 'tbl_checkouts.hotel_id', '=', 'tbl_hotel.id')
                ->leftJoin('tbl_essentials_preorder', 'tbl_checkouts.essnoness_id', '=', 'tbl_essentials_preorder.essential_pre_order_id')
                ->select(
                    'tbl_checkouts.id AS MainTId',
                    'tbl_checkouts.*',
                    'tbl_checkouts.currency AS ItemCurrency',
                    'tbl_checkouts.status AS CheckoutStatus',
                    'tbl_checkouts.quantity AS ReqQTy',
                    'tbl_checkouts.child_rate AS ChckoutChildRate',
                    'tbl_checkouts.adult_rate AS ChckoutAdultdRate',
                    'tbl_lifestyle.*',
                    'tbl_lifestyle.vendor_id AS LifestyleVendor',
                    'tbl_lifestyle.latitude AS LifestyleLatitude',
                    'tbl_lifestyle.longitude AS LifestyleLongitude',
                    'edu_tbl_education.status AS EduStatus',
                    'edu_tbl_education.vendor_id AS EduVendor',
                    'edu_tbl_education.user_active AS EduActive',
                    'edu_tbl_education.*',
                    'tbl_hotel.*',
                    'tbl_hotel.longtitude AS HtlLong',
                    'tbl_hotel.latitude AS HtlLat',
                    'tbl_hotel.trip_advisor_link AS HtlTripAdvisor',
                    'tbl_hotel.country AS HtlCountry',
                    'tbl_hotel.city AS HtlCity',
                    'tbl_hotel.micro_location AS HtlMicroLocation',
                    'tbl_hotel.vendor_id AS HtlVendor',
                    'tbl_essentials_preorder.address AS EssenAddress',
                    'tbl_essentials_preorder.city AS EssenCity',
                    'tbl_essentials_preorder.quantity AS EssenQuantity',
                    'tbl_essentials_preorder.status AS EssenStatus',
                    'tbl_essentials_preorder.*'
                )
                ->get();



            return response([
                'status' => 200,
                'PendingCount' => $PendingCount,
                'OngoingCount' => $OngoingCount,
                'CompletedCount' => $CompletedCount,
                'Products' => $Products
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
