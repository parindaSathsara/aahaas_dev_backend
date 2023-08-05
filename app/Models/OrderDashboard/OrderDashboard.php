<?php

namespace App\Models\OrderDashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderDashboard extends Model
{
    use HasFactory;

    //get counts for each category
    public function getCountForEachCat()
    {
        try {

            // $EssCount = DB::table('tbl_checkout_ids')
            //     ->join('tbl_checkouts','tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
            //     ->join('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', 'tbl_essentials_preorder.essential_pre_order_id')
            //     ->join('tbl_product_listing', 'tbl_essentials_preorder.essential_listing_id', 'tbl_product_listing.id')
            //     ->get();

            $EssCount = DB::table('tbl_checkout_ids')->where('tbl_checkouts.main_category_id', '1')
                ->join('tbl_checkouts', 'tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
                ->groupBy('tbl_checkouts.checkout_id')
                ->get();

            $NonEssCount = DB::table('tbl_checkout_ids')->where('tbl_checkouts.main_category_id', '2')
                ->join('tbl_checkouts', 'tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
                ->groupBy('tbl_checkouts.checkout_id')
                ->get();

            $LifeStyleCount = DB::table('tbl_checkout_ids')->where('tbl_checkouts.main_category_id', '3')
                ->join('tbl_checkouts', 'tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
                ->groupBy('tbl_checkouts.checkout_id')
                ->get();

            $HotelCount = DB::table('tbl_checkout_ids')->where('tbl_checkouts.main_category_id', '4')
                ->join('tbl_checkouts', 'tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
                ->groupBy('tbl_checkouts.checkout_id')
                ->get();

            $EduCount = DB::table('tbl_checkout_ids')->where('tbl_checkouts.main_category_id', '5')
                ->join('tbl_checkouts', 'tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
                ->groupBy('tbl_checkouts.checkout_id')
                ->get();

            $FlightCount = DB::table('tbl_flight_resevation')
                // ->join('tbl_flight_payment', 'tbl_flight_resevation.booking_ref', 'tbl_flight_payment.booking_ref')
                // ->count
                ->groupBy('tbl_flight_resevation.booking_ref')
                ->get();

            return response([
                'status' => 200,
                'ess_count' => count($EssCount),
                'noness_count' => count($NonEssCount),
                'lifestyle_count' => count($LifeStyleCount),
                'hotel_count' => count($HotelCount),
                'edu_count' => count($EduCount),
                'flight_count' => count($FlightCount)
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //essential checkout orders
    public function getEssentialRecentOrders()
    {
        try {
            $EssData = DB::table('tbl_checkout_ids')->where([
                // 'tbl_checkouts.delivery_status' => 'Pending',
                'tbl_checkouts.main_category_id' => '1'
            ])
                ->join('tbl_checkouts', 'tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
                ->join('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', 'tbl_essentials_preorder.essential_pre_order_id')
                ->join('tbl_product_listing', 'tbl_essentials_preorder.essential_listing_id', 'tbl_product_listing.id')
                ->orderBy('tbl_checkout_ids.checkout_date', 'DESC')
                ->select('*', 'tbl_checkout_ids.id AS OrderId', 'tbl_product_listing.listing_title')
                ->get();

            return response([
                'status' => 200,
                'ess_data' => $EssData
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //nonessential checkout orders
    public function getNonEssentialRecentOrders()
    {
        try {
            $EssData = DB::table('tbl_checkout_ids')->where([
                // 'tbl_checkouts.delivery_status' => 'Pending',
                'tbl_checkouts.main_category_id' => '2'
            ])
                ->join('tbl_checkouts', 'tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
                ->join('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', 'tbl_essentials_preorder.essential_pre_order_id')
                ->join('tbl_product_listing', 'tbl_essentials_preorder.essential_listing_id', 'tbl_product_listing.id')
                ->orderBy('tbl_checkout_ids.checkout_date', 'DESC')
                ->select('*', 'tbl_checkout_ids.id AS OrderId', 'tbl_product_listing.listing_title')
                ->get();

            return response([
                'status' => 200,
                'ess_data' => $EssData
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //life style checkout orders
    public function getLifeStyleRecentOrders()
    {
        try {

            $LifeStyleData = DB::table('tbl_checkout_ids')->where('tbl_checkouts.main_category_id', '3')
                ->join('tbl_checkouts', 'tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
                ->join('tbl_lifestyle_bookings', 'tbl_checkouts.related_order_id', 'tbl_lifestyle_bookings.lifestyle_booking_id')
                ->join('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', 'tbl_lifestyle.lifestyle_id')
                ->orderBy('tbl_checkout_ids.checkout_date', 'DESC')
                ->select('*', 'tbl_checkout_ids.id AS OrderId')
                ->get();

            return response([
                'status' => 200,
                'ls_data' => $LifeStyleData
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //education checkout orders
    public function getEducationRecentOrders()
    {
        try {

            $EducationData = DB::table('tbl_checkout_ids')->where('tbl_checkouts.main_category_id', '5')
                ->join('tbl_checkouts', 'tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
                ->join('edu_tbl_booking', 'tbl_checkouts.related_order_id', 'edu_tbl_booking.booking_id')
                ->join('edu_tbl_education', 'edu_tbl_booking.education_id', 'edu_tbl_education.education_id')
                ->orderBy('tbl_checkout_ids.checkout_date', 'DESC')
                ->select('*', 'tbl_checkout_ids.id AS OrderId', 'tbl_checkouts.status AS EduBookStatus')
                ->get();

            return response([
                'status' => 200,
                'edu_data' => $EducationData
            ]);
        } catch (\Exception $ex) {
        }
    }

    //get all essential non essential orders
    public function getAllEssentialNonEssentialOrders()
    {
        try {

            $SqlQuery = DB::table('tbl_checkouts')
                ->where('tbl_checkouts.main_category_id', '1')
                ->orWhere('tbl_checkouts.main_category_id', '2')
                ->join('tbl_checkout_ids', 'tbl_checkouts.checkout_id', 'tbl_checkout_ids.id')
                // ->join('tbl_online_transfers', 'tbl_checkout_ids.id', 'tbl_online_transfers.checkout_id')
                // ->join('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', 'tbl_essentials_preorder.essential_pre_order_id')
                // ->join('tbl_product_listing', 'tbl_essentials_preorder.essential_listing_id', 'tbl_product_listing.id')
                ->groupBy('tbl_checkouts.checkout_id')
                ->select('*', 'tbl_checkouts.checkout_id AS OrderId')->orderBy('tbl_checkouts.checkout_id', 'DESC')->get();

            $SqlQuery2 = DB::table('tbl_checkout_ids')
                ->where('tbl_checkouts.main_category_id', '1')
                ->orWhere('tbl_checkouts.main_category_id', '2')
                ->leftJoin('tbl_checkouts', 'tbl_checkout_ids.id', 'tbl_checkouts.checkout_id')
                // ->join('tbl_online_transfers', 'tbl_checkout_ids.id', 'tbl_online_transfers.checkout_id')
                ->leftJoin('tbl_product_listing', 'tbl_checkouts.essnoness_id', 'tbl_product_listing.id')
                ->select('*', 'tbl_checkouts.checkout_id AS OrderIdCheck', 'tbl_product_listing.id AS ProdId')
                ->get();

            return response([
                'status' => 200,
                'group_data' => $SqlQuery,
                'prod_data' => $SqlQuery2
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //get order payment transaction data
    public function getOrderPaymentTransactionData($paycat, $orderid)
    {
        try {

            if ($paycat === 'Online Transfer') {
                $OnlineQuery = DB::table('tbl_online_transfers')
                    ->where('tbl_online_transfers.checkout_id', $orderid)
                    ->join('tbl_customer', 'tbl_online_transfers.user_id', 'tbl_customer.customer_id')
                    ->first();

                return response([
                    'status' => 200,
                    'type' => 'Online',
                    'dataset' => $OnlineQuery
                ]);
            } else if ($paycat === 'Card Payment') {

                $CardQuery = DB::table('ahs_transactions')
                    ->where('ahs_transactions.pay_id', $orderid)
                    ->first();

                return response([
                    'status' => 200,
                    'type' => 'Card',
                    'dataset' => $CardQuery
                ]);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //chnage order delivery status
    public function changeDeliveryStatus($value, $orderid)
    {
        try {

            DB::table('tbl_checkouts')
                ->where([
                    'tbl_checkouts.checkout_id' => $orderid,
                    'tbl_checkouts.main_category_id' => '1',
                ])
                ->update([
                    'tbl_checkouts.delivery_status' => $value
                ]);

            DB::table('tbl_checkouts')
                ->where([
                    'tbl_checkouts.checkout_id' => $orderid,
                    'tbl_checkouts.main_category_id' => '2'
                ])
                ->update([
                    'tbl_checkouts.delivery_status' => $value
                ]);

            return response([
                'status' => 200,
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //fetch flight reservations
    public function getAllFLightReservations()
    {
        try {

            $SqlQuery = DB::table('tbl_flight_resevation')->select('*')->get();

            return response([
                'status' => 200,
                'dataset' => $SqlQuery
            ]);
        } catch (\Exception $ex) {
        }
    }
}
