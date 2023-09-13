<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;

class Hotel extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel';

    protected $fillable = [
        'hotel_name',
        'hotel_description',
        'hotel_level',
        'category1',
        'longtitude',
        'latitude',
        'provider',
        'hotel_address',
        'trip_advisor_link',
        'hotel_image',
        'country',
        'city',
        'micro_location',
        'hotel_status',
        'startdate',
        'enddate',
        'vendor_id',
        'updated_by'
    ];

    public $timestamps = false;

    public function createNewHotel($hotel_name, $hotel_des, $hotel_level, $hotel_cat, $long, $lat, $provider, $address, $trip_ad_link, $hotel_img, $country, $city, $micro_location, $status, $start_date, $end_date, $vendor)
    {
        try {

            Hotel::create([
                'hotel_name' => $hotel_name,
                'hotel_description' => $hotel_des,
                'hotel_level' => $hotel_level,
                'category1' => $hotel_cat,
                'longtitude' => $long,
                'latitude' => $lat,
                'provider' => $provider,
                'hotel_address' => $address,
                'trip_advisor_link' => $trip_ad_link,
                'hotel_image' => implode('|', $hotel_img),
                'country' => $country,
                'city' => $city,
                'micro_location' => $micro_location,
                'hotel_status' => $status,
                'startdate' => $start_date,
                'enddate' => $end_date,
                'vendor_id' => $vendor,
                'updated_by' => "user",
            ]);

            return response([
                'status' => 200,
                'response' => 'Hotel created success'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //ROOMS BY HOTEL LOCAL
    public function getRoomTypesById($id)
    {
        try {

            $QueryData = DB::table('tbl_hotel_inventory')->where('hotel_id', $id)->select('*')->groupBy('room_category')->get();

            return $QueryData;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //AAHAAS HOTELS BY LAT LON
    public function getHotelsByLatLon()
    {
        try {

            $Query = DB::select(DB::raw("SELECT *, SQRT(
	                    POW(69.1 * (latitude - 7.2905715), 2) + 
	                    POW(69.1 * (80.6337262 - longtitude) * COS(latitude / 57.3), 2)) AS distance
                        FROM aahaasv2.tbl_hotel HAVING distance < 20 ORDER BY distance;"));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //send confirmation email
    public function sendEmailConfirmation($id)
    {
        try {

            $dataJoinOne = DB::table('tbl_hotel_resevation')
                ->where('tbl_hotel_resevation.resevation_no', $id)
                ->where('tbl_hotel_resevation.status', 'New')
                ->join('users', 'tbl_hotel_resevation.user_id', '=', 'users.id')
                ->join('tbl_hotel', 'tbl_hotel_resevation.hotel_name', '=', 'tbl_hotel.id')
                ->select('*')->first();

            $rateKeyNew = json_decode($dataJoinOne->rate_key);
            // return $dataJoinOne;
            $userEmail = $dataJoinOne->email;
            $invoice_no = $dataJoinOne->resevation_no;
            $resevationNumber = $dataJoinOne->resevation_no;
            $resevation_name = $dataJoinOne->resevation_name;
            $resevation_date = $dataJoinOne->resevation_date;
            $checkin_time = date('Y-m-d', strtotime($dataJoinOne->checkin_time));
            $checkout_time = date('Y-m-d', strtotime($dataJoinOne->checkout_time));
            $no_of_adults = $dataJoinOne->no_of_adults;
            $no_of_childs = $dataJoinOne->no_of_childs;
            $bed_type = $dataJoinOne->bed_type;
            $room_type = $dataJoinOne->room_type;
            $no_of_rooms = $dataJoinOne->no_of_rooms;
            $board_code = $dataJoinOne->board_code;
            $special_notice = $dataJoinOne->special_notice;
            $currency = $dataJoinOne->currency;
            $cancelation_deadline = $dataJoinOne->cancelation_deadline;
            // $room_code = $dataJoinOne->room_code;
            $net_amount = $rateKeyNew->total_amount;
            $resevation_status = $dataJoinOne->resevation_status;
            $hotel_name = $dataJoinOne->hotel_name;
            $hotel_address = $dataJoinOne->hotel_address;
            // $hotel_email = $dataJoinOne->hotel_email;
            $pax = (int)$no_of_adults + (int)$no_of_childs;

            $adultRate = $rateKeyNew->adult_rate;

            // ************************ Calculating Nights ************
            $datetime1 = new \DateTime($checkin_time);
            $datetime2 = new \DateTime($checkout_time);
            $interval = $datetime1->diff($datetime2);
            $days = $interval->format('%a');

            $nightsCount = $days;

            $total_amount = currency($net_amount, $currency, $currency, true);

            $dataSet = [
                'invoice_id' => $invoice_no, 'resevation_no' => $resevationNumber, 'resevation_name' => $resevation_name, 'resevation_date' => $resevation_date,
                'checkin_date' => $checkin_time, 'checkout_time' => $checkout_time, 'no_of_adults' => $no_of_adults, 'no_of_childs' => $no_of_childs, 'bed_type' => $bed_type, 'room_type' => $room_type,
                'no_of_rooms' => $no_of_rooms, 'board_code' => $board_code, 'special_notice' => $special_notice, 'resevation_status' => $resevation_status, 'pax_count' => $pax,
                'cancel_dealine' => $cancelation_deadline, 'total_amount' => $total_amount, 'nights' => $nightsCount, 'hotelName' => $hotel_name, 'hotelName' => $hotel_name,
                'hotelAddress' => $hotel_address, 'otherData' => $rateKeyNew, 'currency' => $currency
            ];

            // return view('Mails.AahaasRecipt', $dataSet);
            // $pdf = Pdf::loadView('pdf_view', $dataSet);
            // $pdf = PDF::loadView('Mails.AahaasRecipt', $dataSet);


            $pdf = app('dompdf.wrapper');
            $pdf->loadView('Mails.AahaasRecipt', $dataSet);
            // return $pdf->download('pdf_file.pdf');

            try {
                $done = Mail::send('Mails.ReciptBody', $dataSet, function ($message) use ($userEmail, $resevationNumber, $pdf) {
                    $message->to($userEmail);
                    $message->subject('Confirmation Email on your Booking Reference: #' . $resevationNumber . '.');
                    $message->attachData($pdf->output(), $resevationNumber . '_' . 'Recipt.pdf', ['mime' => 'application/pdf',]);
                });

                return response()->json([
                    'status' => 200,
                    'message' => 'Booking Confirmed and Confirmation Mail sent your email'
                ]);
            } catch (\Exception $ex) {
                throw $ex;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
