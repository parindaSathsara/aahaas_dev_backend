<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class HotelsPreBookings extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotels_pre_booking';

    protected $fillable = [
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
    ];

    public $timestamps = false;

    //create single checkout prebooking
    public function createSingleCheckoutPreBook($refId, $hotelName, $rateKey, $fName, $lName, $roomId, $type, $name, $surname, $roomType, $remark, $status, $uid, $cartImg, $checkin, $checkout, $paxCount, $noAdults, $noChilds, $title, $fare, $provider, $total)
    {
        try {

            $query = HotelsPreBookings::create([
                'rate_key' => $rateKey,
                'hotel_name' => $hotelName,
                'holderFirstName' => $fName,
                'holderLastName' => $lName,
                'hotelRoomTypes' => $roomType,
                'roomId' => $roomId,
                'type' => $type,
                'name' => $name,
                'surname' => $surname,
                'remarks' => $remark,
                'cartStatus' => $status,
                'userID' => $uid,
                'cart_image' => $cartImg,
                'checkingDate' => $checkin,
                'checkoutDate' => $checkout,
                'paxCount' => $paxCount,
                'noOfAdults' => $noAdults,
                'noOfChilds' => $noChilds,
                'cusTitle' => $title,
                'totalFare' => $fare,
                'provider' => $provider,
                'ref_id' => $refId,
                'booking_total' => $total,
            ]);

            return $query;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //update pre booking status hotels
    public function updateCartStatus($id, $status)
    {
        try {

            DB::table('tbl_hotels_pre_booking')
                ->where('booking_id', $id)
                ->update(['cartStatus' => $status]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //get pre booking data by id
    public function getPreBookingDataById($id)
    {
        try {

            $query = DB::table('tbl_hotels_pre_booking')
                ->where('booking_id', '=', $id)
                ->get();

            return $query;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
