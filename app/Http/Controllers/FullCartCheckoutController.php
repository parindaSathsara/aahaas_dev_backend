<?php

namespace App\Http\Controllers;

use App\Models\ProductListingRates;
use App\Models\ProductPreOrder;
use App\Models\ProductsOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Lifestyle\LifeStyleBook;
use App\Models\Customer\MainCheckout;
use App\Models\CustomerCustomCarts;
use App\Models\Education\EducationBookings;


class FullCartCheckoutController extends Controller
{
    public function updateEssentialsStatus(Request $request)
    {
        try {
            $id = $request->input('id');
            $status = $request->input('status');
            ProductPreOrder::where('essential_pre_order_id', $id)->update(['status' => $status]);

            if ($request['cat'] === 'Essential') {
                MainCheckout::create([
                    'checkout_id' => $request['oid'],
                    'essnoness_id' => $request['listid'],
                    'lifestyle_id' => null,
                    'education_id' => null,
                    'hotel_id' => null,
                    'flight_id' => null,
                    'main_category_id' => '1',
                    'quantity' => $request['qty'],
                    'each_item_price' => $request['unit_price'],
                    'total_price' => $request->input('totamount'),
                    'discount_price' => $request->input('discount_amount'),
                    'bogof_item_name' => null,
                    'delivery_charge' => $request['deliveryCharge'],
                    'discount_type' => null,
                    'child_rate' => null,
                    'adult_rate' => null,
                    'discountable_child_rate' => null,
                    'discountable_adult_rate' => null,
                    'flight_trip_type' => null,
                    'flight_total_price' => null,
                    'related_order_id' => $request['preid'],
                    'currency' => $request['currency'],
                    'status' => 'CustomerOrdered',
                    'delivery_status' => 'Pending',
                    'delivery_date' => $request['preffered_delivery_date'],
                    'delivery_address' => $request['address'],
                    'cx_id' => $request->input('customer_id'),
                    'balance_amount' => $request->input('balance_amount'),
                    'paid_amount' => $request->input('paid_amount'),
                    'total_amount' => $request->input('totamount'),
                ]);
            } else {
                MainCheckout::create([
                    'checkout_id' => $request['oid'],
                    'essnoness_id' => $request['listid'],
                    'lifestyle_id' => null,
                    'education_id' => null,
                    'hotel_id' => null,
                    'flight_id' => null,
                    'main_category_id' => '2',
                    'quantity' => $request['qty'],
                    'each_item_price' => $request['unit_price'],
                    'total_price' => $request->input('totamount'),
                    'discount_price' => $request->input('discount_amount'),
                    'bogof_item_name' => null,
                    'delivery_charge' => $request['deliveryCharge'],
                    'discount_type' => null,
                    'child_rate' => null,
                    'adult_rate' => null,
                    'discountable_child_rate' => null,
                    'discountable_adult_rate' => null,
                    'flight_trip_type' => null,
                    'flight_total_price' => null,
                    'related_order_id' => $request['preid'],
                    'currency' => $request['currency'],
                    'status' => 'CustomerOrdered',
                    'delivery_status' => 'Pending',
                    'delivery_date' => $request['preffered_delivery_date'],
                    'delivery_address' => $request['address'],
                    'cx_id' => $request->input('customer_id'),
                    'balance_amount' => $request->input('balance_amount'),
                    'paid_amount' => $request->input('paid_amount'),
                    'total_amount' => $request->input('totamount'),
                ]);
            }

            $this->confirmProductOrder($request);
            // $this->sendCartEmail($request['oid']);
            // $essNess = ProductListingController::confirmProductOrder($request);

            return response([
                'status' => 200,
            ]);
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }

    public function confirmProductOrder(Request $request)
    {
        $currentDate = \Carbon\Carbon::now()->toDateString();
        try {

            $rateID = $request->input('rate_id');

            $getCarts = ProductListingRates::where('rate_id', $rateID)
                ->select("tbl_product_listing_rates.qty")
                ->get();


            $availableQty = $getCarts[0]['qty'];

            // $availableQty = $this->getAvailableQty($request->input('rate_id'));
            $reqQty = $request->input('order_quantity');


            if ($availableQty >= $reqQty) {
                $productOrder = ProductsOrders::create([
                    'listing_id' => $request->input('listing_id'),
                    'rate_id' => $request->input('rate_id'),
                    'inventory_id' => $request->input('inventory_id'),
                    'discount_id' => $request->input('discount_id'),
                    'payment_option_id' => $request->input('payment_option_id'),
                    'customer_id' => $request->input('customer_id'),
                    'order_number' => $request->input('order_number'),
                    'order_quantity' => $request->input('order_quantity'),
                    'unit_price' => $request->input('unit_price'),
                    'total_price' => $request->input('total_price'),
                    'discount_amount' => $request->input('discount_amount'),
                    'ship_to' => $request->input('ship_to'),
                    'address' => $request->input('address'),
                    'preffered_delivery_date' => $request->input('preffered_delivery_date'),
                    'message_to_seller' => $request->input('message_to_seller'),
                    'order_date' => $currentDate,
                    'order_status' => 'Complete',
                    'addressType' => $request->input('addressType')
                ]);

                DB::table('tbl_product_listing_rates')
                    ->where('rate_id', $request->input('rate_id'))
                    ->where('inventory_id', $request->input('inventory_id'))
                    ->update(['qty' => $availableQty - $reqQty]);

                return response()->json([
                    'status' => 200,
                    'qty' => $productOrder,
                    'message' => "Order Success",
                    'inventory' => 'Available'
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => "Order Unsuccessful",
                    'inventory' => 'Unavailable',
                    'qty' => 0,

                ]);
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }


    public function updateHotelsStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        // HotelsPreBookings::where('booking_id', $id)->update(['cartStatus' => $status]);

        $hotelPreBooking = DB::table('tbl_hotels_pre_booking')->where('booking_id', '=', $id)->get();

        $oid = $request['oid'];


        $hotelDataSet = $hotelPreBooking[0];

        $request->merge([

            'mrp' => $hotelDataSet->totalFare,
            'holderFirstName' => $hotelDataSet->holderFirstName,
            'holderLastName' => $hotelDataSet->holderLastName,
            'roomId' => $hotelDataSet->roomId,
            'type' => $hotelDataSet->type,
            'name' => $hotelDataSet->name,
            'surname' => $hotelDataSet->surname,
            'remarks' => $hotelDataSet->remarks,
        ]);

        // $hotelBeds = HotelBedsController::confirmBooking($request, $oid);


        return response()->json([
            'status' => 200,
            'hotel' => $hotelDataSet,
            // 'hotelBeds' => $hotelBeds
        ]);
    }

    public function updateEducationStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        EducationBookings::where('booking_id', $id)->update(['status' => $status]);

        MainCheckout::create([
            'checkout_id' => $request['oid'],
            'essnoness_id' => null,
            'lifestyle_id' => null,
            'education_id' => $request->input('eduid'),
            'hotel_id' => null,
            'flight_id' => null,
            'main_category_id' => '5',
            'quantity' => null,
            'each_item_price' => null,
            'total_price' => $request->input('totamount'),
            'discount_price' => $request->input('discount'),
            'bogof_item_name' => null,
            'delivery_charge' => null,
            'discount_type' => null,
            'child_rate' => $request->input('childfee'),
            'adult_rate' => $request->input('adultfee'),
            'discountable_child_rate' => null,
            'discountable_adult_rate' => null,
            'flight_trip_type' => null,
            'flight_total_price' => null,
            'related_order_id' => $request->input('id'),
            'currency' => $request['currency'],
            'status' => 'CustomerOrdered',
            'delivery_status' => null,
            'delivery_date' => null,
            'delivery_address' => null,
            'cx_id' => $request->input('user_id'),
            'balance_amount' => 0.00,
            'paid_amount' => $request->input('totamount'),
            'total_amount' => $request->input('totamount'),
        ]);

        return response()->json([
            'status' => 200,
        ]);
    }

    public function updateLifeStyleStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $user_id = $request->input('user_id');
        // LifeStyleBook
        LifeStyleBook::where('lifestyle_booking_id', $id)->update(['booking_status' => $status]);


        MainCheckout::create([
            'checkout_id' => $request['oid'],
            'essnoness_id' => null,
            'lifestyle_id' => $request->input('lifeid'),
            'education_id' => null,
            'hotel_id' => null,
            'flight_id' => null,
            'main_category_id' => '3',
            'quantity' => null,
            'each_item_price' => null,
            'total_price' => $request->input('totamount'),
            'discount_price' => $request->input('discount'),
            'bogof_item_name' => null,
            'delivery_charge' => null,
            'discount_type' => null,
            'child_rate' => $request->input('child'),
            'adult_rate' => $request->input('adult'),
            'discountable_child_rate' => null,
            'discountable_adult_rate' => null,
            'flight_trip_type' => null,
            'flight_total_price' => null,
            'related_order_id' => $request->input('lifeid'),
            'currency' => $request['currency'],
            'status' => 'CustomerOrdered',
            'delivery_status' => null,
            'delivery_date' => null,
            'delivery_address' => null,
            'cx_id' => $user_id,
            'balance_amount' => 0.00,
            'paid_amount' => $request->input('totamount'),
            'total_amount' => $request->input('totamount'),
        ]);

        return response()->json([
            'status' => 200,
        ]);
    }

    public function updateCartStatus(Request $request)
    {
        try {
            $id = $request->input('id');
            $status = $request->input('status');

            CustomerCustomCarts::where('customer_cart_id', $id)->update(['cart_status' => $status]);
            // LifeStyleBook::where('lifestyle_booking_id', $id)->update(['booking_status' => $status]);

            // $checkout = DB::select(DB::raw("INSERT INTO tbl_checkout_ids (user_id,checkout_status) VALUES ($user_id,'Completed')"));

            return response()->json([
                'status' => 200,
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function sendCartEmail($orderId)
    {
        // $OrderId = $request['orderid'];
        $OrderId = $orderId;
        // $UserId = $request['userid'];

        try {

            $Query2 = DB::table('tbl_checkout_ids')->where('tbl_checkout_ids.id', '=', $OrderId)
                ->leftJoin('users', 'tbl_checkout_ids.user_id', '=', 'users.id')
                ->select('users.email AS UserEmail')
                ->first();

            // sleep(5);

            // ->leftJoin('tbl_essentials_preorder', 'tbl_checkouts.related_order_id', '=', 'tbl_essentials_preorder.essential_pre_order_id')

            $Query = DB::table('tbl_checkouts')->where('tbl_checkouts.checkout_id', '=', $OrderId)
                ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                ->leftJoin('users', 'tbl_checkouts.cx_id', '=', 'users.id')
                ->leftJoin('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
                ->leftJoin('tbl_product_listing', 'tbl_checkouts.essnoness_id', '=', 'tbl_product_listing.id')
                ->leftJoin('tbl_lifestyle_bookings', 'tbl_checkouts.lifestyle_id', '=', 'tbl_lifestyle_bookings.lifestyle_booking_id')
                ->leftJoin('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle_bookings.lifestyle_rate_id', '=', 'tbl_lifestyle_rates.lifestyle_rate_id')
                ->leftJoin('edu_tbl_booking', 'tbl_checkouts.related_order_id', '=', 'edu_tbl_booking.booking_id')
                ->leftJoin('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')
                ->leftJoin('edu_tbl_rate', 'edu_tbl_booking.rate_id', '=', 'edu_tbl_rate.edu_id')
                ->leftJoin('tbl_hotel_resevation', 'tbl_checkouts.hotel_id', '=', 'tbl_hotel_resevation.id')
                ->leftJoin('edu_tbl_sessions', 'edu_tbl_booking.session_id', '=', 'edu_tbl_sessions.session_id')
                ->select(
                    'tbl_checkouts.checkout_id AS Order Id',
                    'tbl_checkouts.delivery_address AS DeliveryAddress',
                    'tbl_maincategory.maincat_type AS MainCat',
                    'tbl_product_listing.listing_title AS EssNonTitle',
                    'edu_tbl_education.course_name',
                    'edu_tbl_booking.student_type AS StudentType',
                    'edu_tbl_booking.booking_date AS EduBookDate',
                    'edu_tbl_rate.adult_course_fee AS AdultStuFee',
                    'edu_tbl_rate.child_course_fee AS ChildStuFee',
                    'tbl_checkouts.delivery_date AS DeliveryDate',
                    'tbl_checkouts.delivery_status AS DeliveryStatus',
                    'tbl_checkouts.quantity AS EssQuantity',
                    'tbl_checkouts.total_price AS TotPrice',
                    'tbl_checkouts.each_item_price AS EachPrice',
                    'tbl_product_listing.sku AS SKU',
                    'tbl_product_listing.unit AS SKUUNIT',
                    'edu_tbl_sessions.start_date AS EduStartDate',
                    'edu_tbl_sessions.start_time AS EduStartTime',
                    'edu_tbl_sessions.end_time AS EduEndTime',
                    'tbl_checkouts.essnoness_id AS EssId',
                    'tbl_checkouts.lifestyle_id AS LsId',
                    'tbl_checkouts.education_id AS EduId',
                    'tbl_checkouts.hotel_id AS HotelId',
                    'tbl_checkouts.flight_id AS FlightId',
                    'tbl_lifestyle.lifestyle_name AS LSName',
                    'tbl_lifestyle_bookings.booking_date AS LSBookDate',
                    'tbl_lifestyle_bookings.lifestyle_children_details AS LSChildDetails',
                    'tbl_lifestyle_bookings.lifestyle_adult_details AS LSAdultDetails',
                    'tbl_lifestyle_bookings.lifestyle_children_count AS LSChildCount',
                    'tbl_lifestyle_bookings.lifestyle_adult_count AS LSAdultCount',
                    'tbl_lifestyle_rates.adult_rate AS LSAdultRate',
                    'tbl_lifestyle_rates.child_rate AS LSChildRate',
                    'tbl_hotel_resevation.hotel_name AS HotelName',
                    'tbl_hotel_resevation.checkin_time AS CheckInHotel',
                    'tbl_hotel_resevation.checkout_time AS CheckOutHotel',
                    'tbl_hotel_resevation.no_of_adults AS HotelNoAdults',
                    'tbl_hotel_resevation.no_of_childs AS HotelNoChilds',
                    'tbl_hotel_resevation.resevation_date AS HotelBookDate',
                    'tbl_hotel_resevation.room_type AS HotelRoomType',
                    'tbl_checkouts.total_price AS HotelRate',
                )->get();


            // return $Query;

            $Query3 = DB::table('tbl_checkouts')->where('tbl_checkouts.checkout_id', '=', $OrderId)
                ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                ->leftJoin('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')->select('tbl_maincategory.maincat_type AS MainCat')->get();

            $catArray = array();

            foreach ($Query3 as $cat) {
                $catArray[] = $cat->MainCat;
            }


            $UserEmail = $Query2->UserEmail;

            $dataset = ['fullData' => $Query, 'orderid' => $OrderId, 'categories' => $catArray];

            $pdf = app('dompdf.wrapper');
            $pdf->loadView('Mails.CartCheckout', $dataset);

            Mail::send('Mails.CartCheckout', $dataset, function ($message) use ($UserEmail, $OrderId, $pdf) {
                $message->to($UserEmail);
                $message->subject('Confirmation Email on your Order Referance: #' . $OrderId . '.');
                $message->attachData($pdf->output(), $OrderId . '_' . 'Recipt.pdf', ['mime' => 'application/pdf',]);
                // $message->attachData($pdf2->output(), $resevationNumber . '_' . 'Beds_Recipt.pdf', ['mime' => 'application/pdf',]);
            });

            return response()->json([
                'status' => 200,
                'message' => 'Order Confirmed and Confirmation Mail sent your email'
            ]);

            // return view('Mails.CartCheckout');
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
}
