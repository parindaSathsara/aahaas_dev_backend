<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Hotels\HotelBeds\HotelBedsController;
use App\Models\Customer\CheckoutID;
use App\Models\Customer\MainCheckout;
use App\Models\CustomerCustomCarts;
use App\Models\Education\EducationBookings;
use App\Models\Hotels\HotelsPreBookings;
use App\Models\Lifestyle\LifeStyleBook;
use App\Http\Controllers\ProductListingController;
use App\Models\ProductListingRates;
use App\Models\ProductPreOrder;
use App\Models\ProductsOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CustomerCartCheckout extends Controller
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
                    'each_item_price' => $request['eachitem'],
                    'total_price' => $request->input('discountMrp'),
                    'discount_price' => $request->input('discount'),
                    'bogof_item_name' => null,
                    'delivery_charge' => $request['deliverycharge'],
                    'discount_type' => null,
                    'child_rate' => null,
                    'adult_rate' => null,
                    'discountable_child_rate' => null,
                    'discountable_adult_rate' => null,
                    'flight_trip_type' => null,
                    'flight_total_price' => null,
                    'related_order_id' => $request['preid'],
                    'currency' => $request['currency'],
                    'status' => 'Booked',
                    'delivery_status' => 'Pending',
                    'delivery_date' => $request['preffered_date'],
                    'delivery_address' => $request['address'],
                    'cx_id' => $request->input('user_id'),
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
                    'each_item_price' => $request['eachitem'],
                    'total_price' => $request->input('discountMrp'),
                    'discount_price' => $request->input('discount'),
                    'bogof_item_name' => null,
                    'delivery_charge' => $request['deliverycharge'],
                    'discount_type' => null,
                    'child_rate' => null,
                    'adult_rate' => null,
                    'discountable_child_rate' => null,
                    'discountable_adult_rate' => null,
                    'flight_trip_type' => null,
                    'flight_total_price' => null,
                    'related_order_id' => $request['preid'],
                    'currency' => $request['currency'],
                    'status' => 'Booked',
                    'delivery_status' => 'Pending',
                    'delivery_date' => $request['preffered_date'],
                    'delivery_address' => $request['address'],
                    'cx_id' => $request->input('user_id'),
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
        $currentTime = \Carbon\Carbon::now()->toDateTimeString();

        $educationBookings = EducationBookings::create([
            'education_id' => $request->input('edupreid'),
            'session_id' => $request->input('session_id'),
            'discount_id' => $request->input('discount_id'),
            'booking_date' => $currentTime,
            'preffered_booking_date' => $request->input('preffered_booking_date'),
            'totalPrice' => $request->input('totalPrice'),
            'discount_amount' => $request->input('discount_amount'),
            'student_name' => $request->input('student_name'),
            'student_age' => $request->input('student_age'),
            'student_type' => $request->input('student_type'),
            'status' => $request->input('status'),
            'user_id' => $request->input('user_id'),
            'rate_id' => $request->input('rate_id'),
        ]);

        MainCheckout::create([
            'checkout_id' => $request['oid'],
            'essnoness_id' => null,
            'lifestyle_id' => null,
            'education_id' => $request->input('edupreid'),
            'hotel_id' => null,
            'flight_id' => null,
            'main_category_id' => '5',
            'quantity' => null,
            'each_item_price' => null,
            'total_price' => $request->input('totamount'),
            'discount_price' => $request->input('discountMrp'),
            'bogof_item_name' => null,
            'delivery_charge' => null,
            'discount_type' => null,
            'child_rate' => $request->input('childfee'),
            'adult_rate' => $request->input('adultfee'),
            'discountable_child_rate' => null,
            'discountable_adult_rate' => null,
            'flight_trip_type' => null,
            'flight_total_price' => null,
            'related_order_id' => $educationBookings->id,
            'currency' => $request['currency'],
            'status' => 'Booked',
            'delivery_status' => null,
            'delivery_date' => null,
            'delivery_address' => null,
            'cx_id' => $request->input('user_id'),
        ]);

        // $this->sendCartEmail($request['oid']);

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

        $newLifeStyleBooking = LifeStyleBook::create([
            'lifestyle_id' => $request->input('lifeid'),
            'lifestyle_inventory_id' => $request->input('lifestyle_inventory_id'),
            'lifestyle_rate_id' => $request->input('lifestyle_rate_id'),
            'lifestyle_discount_id' => $request->input('lifestyle_discount_id'),
            'lifestyle_children_details' => $request->input('lifestyle_children_details'),
            'lifestyle_children_ages' => $request->input('lifestyle_children_ages'),
            'lifestyle_adult_details' => $request->input('lifestyle_adult_details'),
            'booking_date' => $request->input('booking_date'),
            'lifestyle_children_count' => $request->input('lifestyle_children_count'),
            'lifestyle_adult_count' => $request->input('lifestyle_adult_count'),
            'booking_status' => 'Booked',
            'user_id' => $request->input('user_id'),
        ]);


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
            'related_order_id' => $newLifeStyleBooking->id,
            'currency' => $request['currency'],
            'status' => 'Booked',
            'delivery_status' => null,
            'delivery_date' => null,
            'delivery_address' => null,
            'cx_id' => $user_id,
        ]);

        // $this->sendCartEmail($request['oid']);

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
                ->leftJoin('tbl_customer', 'users.id', '=', 'tbl_customer.customer_id')
                ->select('users.email AS UserEmail', 'tbl_customer.contact_number AS UserContact')
                ->first();

            $QueryNew = DB::table('tbl_checkout_ids')->where('tbl_checkout_ids.id', '=', $OrderId)
                ->select('*')
                ->first();

            $QueryDelivery = DB::table('tbl_checkouts')->where('tbl_checkouts.checkout_id', '=', $OrderId)
                ->select('*')
                ->first();

            $QueryEss = DB::table('tbl_checkouts')->where('tbl_checkouts.checkout_id', '=', $OrderId)
                ->join('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                ->join('users', 'tbl_checkouts.cx_id', '=', 'users.id')
                ->join('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
                ->join('tbl_product_listing', 'tbl_checkouts.essnoness_id', '=', 'tbl_product_listing.id')
                ->select(
                    'tbl_checkouts.checkout_id AS Order Id',
                    'tbl_checkouts.delivery_address AS DeliveryAddress',
                    'tbl_maincategory.maincat_type AS MainCat',
                    'tbl_product_listing.listing_title AS EssNonTitle',
                    'tbl_checkouts.delivery_date AS DeliveryDate',
                    'tbl_checkouts.delivery_status AS DeliveryStatus',
                    'tbl_checkouts.delivery_charge AS DeliveryCharge',
                    'tbl_checkouts.quantity AS EssQuantity',
                    'tbl_checkouts.total_price AS TotPrice',
                    'tbl_checkouts.each_item_price AS EachPrice',
                    'tbl_product_listing.sku AS SKU',
                    'tbl_product_listing.unit AS SKUUNIT',
                    'tbl_checkouts.essnoness_id AS EssId',
                    'tbl_checkouts.currency AS Currency',
                )->get();

            $QueryLS = DB::table('tbl_checkouts')->where('tbl_checkouts.checkout_id', '=', $OrderId)
                ->join('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                ->join('users', 'tbl_checkouts.cx_id', '=', 'users.id')
                ->join('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
                ->join('tbl_lifestyle_bookings', 'tbl_checkouts.related_order_id', '=', 'tbl_lifestyle_bookings.lifestyle_booking_id')
                ->join('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->join('tbl_lifestyle_rates', 'tbl_lifestyle_bookings.lifestyle_rate_id', '=', 'tbl_lifestyle_rates.lifestyle_rate_id')
                ->join('tbl_lifestyle_inventory', 'tbl_lifestyle_bookings.lifestyle_inventory_id', '=', 'tbl_lifestyle_inventory.lifestyle_inventory_id')
                ->select(
                    'tbl_checkouts.checkout_id AS Order Id',
                    'tbl_checkouts.delivery_address AS DeliveryAddress',
                    'tbl_maincategory.maincat_type AS MainCat',
                    'tbl_checkouts.adult_rate AS AdultStuFee',
                    'tbl_checkouts.child_rate AS ChildStuFee',
                    'tbl_checkouts.delivery_date AS DeliveryDate',
                    'tbl_checkouts.delivery_status AS DeliveryStatus',
                    'tbl_checkouts.total_price AS TotPrice',
                    'tbl_checkouts.each_item_price AS EachPrice',
                    'tbl_checkouts.lifestyle_id AS LsId',
                    'tbl_lifestyle.lifestyle_name AS LSName',
                    'tbl_lifestyle_bookings.booking_date AS LSBookDate',
                    'tbl_lifestyle_bookings.lifestyle_children_details AS LSChildDetails',
                    'tbl_lifestyle_bookings.lifestyle_adult_details AS LSAdultDetails',
                    'tbl_lifestyle_bookings.lifestyle_children_count AS LSChildCount',
                    'tbl_lifestyle_bookings.lifestyle_adult_count AS LSAdultCount',
                    'tbl_lifestyle_rates.adult_rate AS LSAdultRate',
                    'tbl_lifestyle_rates.child_rate AS LSChildRate',
                    'tbl_lifestyle_inventory.pickup_time AS LSStartEndTime',
                    'tbl_checkouts.currency AS Currency',
                )->get();

            $QueryEdu = DB::table('tbl_checkouts')->where('tbl_checkouts.checkout_id', '=', $OrderId)
                ->join('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                ->join('users', 'tbl_checkouts.cx_id', '=', 'users.id')
                ->join('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
                ->join('edu_tbl_booking', 'tbl_checkouts.related_order_id', '=', 'edu_tbl_booking.booking_id')
                ->join('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')
                ->join('edu_tbl_rate', 'edu_tbl_booking.education_id', '=', 'edu_tbl_rate.edu_id')
                ->join('edu_tbl_sessions', 'edu_tbl_booking.session_id', '=', 'edu_tbl_sessions.session_id')
                ->select(
                    // '*'
                    'tbl_checkouts.checkout_id AS Order Id',
                    'tbl_checkouts.delivery_address AS DeliveryAddress',
                    'tbl_maincategory.maincat_type AS MainCat',
                    'edu_tbl_education.course_name',
                    'edu_tbl_booking.student_type AS StudentType',
                    'edu_tbl_booking.booking_date AS EduBookDate',
                    'tbl_checkouts.adult_rate AS AdultStuFee',
                    'tbl_checkouts.child_rate AS ChildStuFee',
                    'tbl_checkouts.delivery_date AS DeliveryDate',
                    'tbl_checkouts.delivery_status AS DeliveryStatus',
                    'tbl_checkouts.total_price AS TotPrice',
                    'tbl_checkouts.each_item_price AS EachPrice',
                    'edu_tbl_sessions.start_date AS EduStartDate',
                    'edu_tbl_sessions.start_time AS EduStartTime',
                    'edu_tbl_sessions.end_time AS EduEndTime',
                    'tbl_checkouts.education_id AS EduId',
                    'tbl_checkouts.currency AS Currency',
                )->get();

            // return $QueryEdu;

            // return $QueryEdu;

            $QueryHotel = DB::table('tbl_checkouts')->where('tbl_checkouts.checkout_id', '=', $OrderId)
                ->join('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                ->join('users', 'tbl_checkouts.cx_id', '=', 'users.id')
                ->join('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')
                ->join('tbl_hotel_resevation', 'tbl_checkouts.hotel_id', '=', 'tbl_hotel_resevation.id')
                ->select(
                    'tbl_checkouts.checkout_id AS Order Id',
                    'tbl_checkouts.delivery_address AS DeliveryAddress',
                    'tbl_maincategory.maincat_type AS MainCat',
                    'tbl_checkouts.adult_rate AS AdultStuFee',
                    'tbl_checkouts.child_rate AS ChildStuFee',
                    'tbl_checkouts.delivery_date AS DeliveryDate',
                    'tbl_checkouts.delivery_status AS DeliveryStatus',
                    'tbl_checkouts.quantity AS EssQuantity',
                    'tbl_checkouts.total_price AS TotPrice',
                    'tbl_checkouts.each_item_price AS EachPrice',
                    'tbl_checkouts.hotel_id AS HotelId',
                    'tbl_hotel_resevation.hotel_name AS HotelName',
                    'tbl_hotel_resevation.checkin_time AS CheckInHotel',
                    'tbl_hotel_resevation.checkout_time AS CheckOutHotel',
                    'tbl_hotel_resevation.no_of_adults AS HotelNoAdults',
                    'tbl_hotel_resevation.no_of_childs AS HotelNoChilds',
                    'tbl_hotel_resevation.resevation_date AS HotelBookDate',
                    'tbl_hotel_resevation.room_type AS HotelRoomType',
                    'tbl_checkouts.total_price AS HotelRate',
                    'tbl_checkouts.currency AS Currency',
                )->get();


            $Query3 = DB::table('tbl_checkouts')->where('tbl_checkouts.checkout_id', '=', $OrderId)
                ->leftJoin('tbl_checkout_ids', 'tbl_checkouts.checkout_id', '=', 'tbl_checkout_ids.id')
                ->leftJoin('tbl_maincategory', 'tbl_checkouts.main_category_id', '=', 'tbl_maincategory.id')->select('tbl_maincategory.maincat_type AS MainCat')->get();

            $catArray = array();

            foreach ($Query3 as $cat) {
                $catArray[] = $cat->MainCat;
            }


            $UserEmail = $Query2->UserEmail;
            $UserContact = $Query2->UserContact;
            $CheckoutDate = $QueryNew->checkout_date;
            $PaymentType = $QueryNew->payment_type;
            $TotalAmount = $QueryNew->total_amount;
            $PaidAmount = $QueryNew->paid_amount;
            $BalanceAmount = $QueryNew->balance_amount;
            $Currency = $QueryDelivery->currency;
            $DeliCharge = $QueryDelivery->delivery_charge;


            $dataset = [
                'user_contact' => $UserContact, 'orderid' => $OrderId, 'categories' => $catArray, 'essData' => $QueryEss, 'lsData' => $QueryLS,
                'eduData' => $QueryEdu, 'hotelData' => $QueryHotel, 'orderDate' => $CheckoutDate, 'payType' => $PaymentType, 'total_amount' => $TotalAmount, 'paid_amount' => $PaidAmount, 'bal_amount' => $BalanceAmount, 'currency_' => $Currency, 'deli_charge' => $DeliCharge
            ];

            // return $QueryEdu;

            // return view('Mails.CartCheckout', $dataset);

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
