<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use App\Models\Customer\MainCheckout;
use App\Models\CustomerCustomCarts;
use App\Models\Hotels\HotelResevation;
use App\Models\Hotels\HotelsPreBookings as HotelsHotelsPreBookings;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HotelsPreBookings extends Controller
{

    public $prebooking;
    public $hotelreservation;
    public $maincheckouttable;

    public function __construct()
    {
        $this->prebooking = new HotelsHotelsPreBookings();
        $this->hotelreservation = new HotelResevation();
        $this->maincheckouttable = new MainCheckout();
    }

    public function addPreBooking(Request $request)
    {

        $hotelDetail = HotelsHotelsPreBookings::create([
            'ref_id' => $request->input('ref_id'),
            'hotel_name' => $request->input('hotel_name'),
            'rate_key' => $request->input('rate_key'),
            'holderFirstName' => $request->input('holderFirstName'),
            'holderLastName' => $request->input('holderLastName'),
            'roomId' => $request->input('roomId'),
            'type' => $request->input('type'),
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'hotelRoomTypes' => $request->input('hotelRoomTypes'),
            'remarks' => $request->input('remarks'),
            'cartStatus' => $request->input('cartStatus'),
            'userID' => $request->input('userID'),
            'cart_image' => $request->input('cart_image'),
            'checkingDate' => $request->input('checkingDate'),
            'checkoutDate' => $request->input('checkoutDate'),
            'paxCount' => $request->input('paxCount'),
            'noOfAdults' => $request->input('noOfAdults'),
            'noOfChilds' => $request->input('noOfChilds'),
            'cusTitle' => $request->input('cusTitle'),
            'totalFare' => $request->input('totalFare'),
            'provider' => $request->input('provider'),
            'booking_total' => $request->input('booking_total'),
        ]);

        $todayDate = Carbon::now()->format('Y-m-d');

        CustomerCustomCarts::create([
            'customer_id' => $request->input('userID'),
            'main_category_id' => 4,
            'cart_id' => $request->input('cart_id'),
            'listing_pre_id' => '',
            'lifestyle_pre_id' => '',
            'hotels_pre_id' => $hotelDetail->id,
            'cart_status' => 'InCart',
            'cart_added_date' => $todayDate,
            'order_preffered_date' => $request->input('checkingDate'),
        ]);

        return response()->json([
            'status' => 200,
            'cart' => $hotelDetail
        ]);
    }

    public function singleHotelCheckout(Request $request)
    {

        $refId =  $request['ref_id'];
        $hotelName = $request['hotel_name'];
        $rateKey = $request['rate_key'];
        $fName = $request['holderFirstName'];
        $lName = $request['holderLastName'];
        $roomId = $request['roomId'];
        $type = $request['type'];
        $name = $request['name'];
        $surname = $request['surname'];
        $roomType = $request['room_type'];
        $remark = $request['remarks'];
        $status = $request['cartStatus'];
        $uid = $request['cart_image'];
        $cartImg = $request['cart_image'];
        $checkin = $request['check_in'];
        $checkout = $request['check_out'];
        $paxCount = $request['paxCount'];
        $noAdults = $request['no_of_adults'];
        $noChilds = $request['no_of_childs'];
        $title = $request['cusTitle'];
        $fare = $request['totalFare'];
        $provider = $request['provider'];
        $total = $request['total_amount'];
        $currency = $request['currency'];

        $orderid = $request['oid'];

        $response = $this->prebooking->createSingleCheckoutPreBook($refId, $hotelName, $rateKey, $fName, $lName, $roomId, $type, $name, $surname, $roomType, $remark, $status, $uid, $cartImg, $checkin, $checkout, $paxCount, $noAdults, $noChilds, $title, $fare, $provider, $total);

        $jsonKey = json_decode($request->rate_key);
        $preId = $response->id;

        return $this->confirmBookingApple($request, $refId, $currency, $preId, $orderid, $total);;
    }

    public function confirmBookingApple($request, $id, $curcy, $preid, $orderid, $total)
    {
        try {
            $current_timestamp = Carbon::now()->timestamp;
            $randNumber = rand(2, 50);
            $bookingRef = "AHBK" . $current_timestamp . "_" . $randNumber;
            $roomType = explode(',', $request->hotelRoomTypes);
            // return $request;

            $rateKey = $request->rate_key;
            $HolderFullName = $request->first_name . ' ' . $request->last_name;
            $resevationDate = Carbon::now()->format('Y-m-d');
            $hotelName = $request->hotelCode;
            $checkinTime = $request->check_in;
            $checkoutTime = $request->check_out;
            $noOfAD = $request->no_of_adults;
            $noOfCH = $request->no_of_childs;
            $bedType = $request->bed_type;
            $roomType = $request->hotelRoomTypes;
            $noOfRooms = $request->room_count;
            $boardCode = $request->board_code;
            $remarks = $request->special_remarks;
            $resevationPlatform = $request->provider;
            $resevationStatus = 'Confirm';
            $currency = $curcy;
            $cancelation = true;
            $modification = true;
            $cancelation_amount = null;
            $cancelation_deadline = Carbon::now()->format('Y-m-d');
            $user_Id = $request->userID;

            $this->hotelreservation->makeHotelReservation($rateKey, $bookingRef, $HolderFullName, $resevationDate, $hotelName, $checkinTime, $checkoutTime, $noOfAD, $noOfCH, $bedType, $roomType, $noOfRooms, $boardCode, $remarks, $resevationPlatform, $resevationStatus, $currency, $cancelation, $modification, $cancelation_amount, $cancelation_deadline, $user_Id, $preid);
            $this->maincheckouttable->checkoutOrderHotel($orderid, $id, $total, $currency, $user_Id, $preid);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 501,
                'error_message' => throw $ex
            ]);
        }
    }
}
