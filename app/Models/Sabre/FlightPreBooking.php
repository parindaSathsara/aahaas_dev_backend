<?php

namespace App\Models\Sabre;

use App\Models\Customer\MainCheckout;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class FlightPreBooking extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'flight_prebooking';

    protected $fillable = [
        'order_id',
        'user_id',
        'reservation_number',
        'user_email',
        'first_name',
        'surname',
        'reservation_name',
        'contact_number',
        'flight_code',
        'flight_number',
        'ori_loccation',
        'dest_loccation',
        'departure_datetime',
        'currency',
        'basefair',
        'taxfair',
        'total_amount',
        'baggeges',
        'pax_count',
        'session',
    ];

    public $timestamps = false;

    public $mainCheckout;

    public function __construct()
    {
        $this->mainCheckout = new MainCheckout();
    }

    //get pre booking data by id
    public function getPreBookDataById($id)
    {
        try {

            $getData = DB::table('flight_prebooking')->where('order_id', $id)->first();

            return response([
                'status' => 200,
                'data_response' => $getData
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //create new flight pre booking
    public function createNewFlightPreBooking($orderid, $userid, $useremail, $firstname, $lastname, $bookingname, $contact_num, $flightcode, $flightnumber, $orilocation, $declocation, $depdatetime, $currency, $basefair, $taxfair, $totamount, $baggeges, $paxcount, $session)
    {
        try {

            $prebooking = FlightPreBooking::create([
                'order_id' => $orderid,
                'user_id' => $userid,
                'reservation_number'=>'-',
                'user_email' => $useremail,
                'first_name' => $firstname,
                'surname' => $lastname,
                'reservation_name' => $bookingname,
                'contact_number' => $contact_num,
                'flight_code' => $flightcode,
                'flight_number' => $flightnumber,
                'ori_loccation' => $orilocation,
                'dest_loccation' => $declocation,
                'departure_datetime' => $depdatetime,
                'currency' => $currency,
                'basefair' => $basefair,
                'taxfair' => $taxfair,
                'total_amount' => $totamount,
                'baggeges' => $baggeges,
                'pax_count' => $paxcount,
                'session' => $session,
            ]);

            $this->mainCheckout->createNewRow($orderid, $basefair, $currency, $userid, $prebooking->id);

            return response([
                'status' => 200,
                'data_response' => 'Success'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
