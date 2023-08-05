<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use App\Models\CustomerCustomCarts;
use App\Models\Hotels\HotelsPreBookings as HotelsHotelsPreBookings;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HotelsPreBookings extends Controller
{
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

        /*
            'rate_key',
        'hotel_name',
        'holderFirstName',
        'holderLastName',
        'hotelRoomTypes',
        'roomId',
        'type',
        'name',
        'surname',
        'remarks',
        'cartStatus',
        'userID',
        'cart_image',
        'checkingDate',
        'checkoutDate',
        'paxCount',
        'noOfAdults',
        'noOfChilds',
        'cusTitle',
        'totalFare',
        'provider',
        'ref_id',
        'booking_total'
        */

        $hotelDetail = HotelsHotelsPreBookings::create([
            'rate_key' => $request->input('rateKey'),
            'hotel_name' => $request->input('hotel_name'),
            'holderFirstName' => $request->input('holderFirstName'),
            'holderLastName' => $request->input('holderLastName'),
            'hotelRoomTypes' => $request->input('hotelRoomTypes'),
            'roomId' => $request->input('roomId'),
            'type' => $request->input('type'),
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'remarks' => $request->input('remarks'),
            'cartStatus' => $request->input('cartStatus'),
            'userID' => $request->input('userId'),
            'cart_image' => $request->input('cart_image'),
            'checkingDate' => $request->input('checkingDate'),
            'checkoutDate' => $request->input('checkoutDate'),
            'paxCount' => $request->input('paxCount'),
            'noOfAdults' => $request->input('noOfAdults'),
            'noOfChilds' => $request->input('noOfChilds'),
            'cusTitle' => $request->input('cusTitle'),
            'totalFare' => $request->input('totalFare'),
            'provider' => $request->input('provider'),
            'ref_id' => $request->input('ref_id'),
        ]);

        return response()->json([
            'status' => 200,
            'bookid' => $hotelDetail->id
        ]);
    }
}
