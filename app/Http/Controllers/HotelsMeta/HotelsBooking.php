<?php

namespace App\Http\Controllers\HotelsMeta;

use App\Http\Controllers\Controller;
use App\Models\CustomerCustomCarts;
use App\Models\HotelsMeta\HotelPreBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HotelsBooking extends Controller
{
    public function hotelsPreBooking(Request $request)
    {
        $hotelBlockingDataSet = $request->input('blockData');

        $hotelBlockingDataSetDecoded = json_decode($hotelBlockingDataSet);


        // return $hotelBlockingDataSetDecoded;


        $hotelPreBooking = HotelPreBooking::create([
            'hotel_id' => $hotelBlockingDataSetDecoded->HotelID,
            'bookingdataset' => $hotelBlockingDataSet,
            'provider' => $hotelBlockingDataSetDecoded->Provider,
            'user_id' => $hotelBlockingDataSetDecoded->user_id,
        ]);

        $currentTime = \Carbon\Carbon::now()->toDateTimeString();

        CustomerCustomCarts::create([
            'main_category_id' => 4,
            'cart_id' => $hotelBlockingDataSetDecoded->cart_id,
            'listing_pre_id' => '',
            'lifestyle_pre_id' => '',
            'hotels_pre_id' => $hotelPreBooking->id,
            'cart_status' => 'InCart',
            'cart_added_date' => $currentTime,
            'customer_id' => $hotelBlockingDataSetDecoded->user_id,
            'order_preffered_date' => Carbon::createFromFormat('d/m/Y', $hotelBlockingDataSetDecoded->CheckInDate)->format('Y-m-d'),
        ]);


        return $hotelPreBooking;
    }
}
