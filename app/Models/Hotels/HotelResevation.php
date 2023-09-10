<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class HotelResevation extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_resevation';

    protected $fillable = [
        'rate_key',
        'resevation_no',
        'resevation_name',
        'resevation_date',
        'hotel_name',
        'checkin_time',
        'checkout_time',
        'baby_crib',
        'no_of_adults',
        'no_of_childs',
        'child_withbed',
        'child_nobed',
        'bed_type',
        'room_type',
        'no_of_rooms',
        'board_code',
        'special_notice',
        'resevation_platform',
        'resevation_status',
        'currency',
        'cancelation',
        'modification',
        'cancelation_amount',
        'cancelation_deadline',
        'cancellation_remarks',
        'other_remarks',
        'booking_remarks',
        'status',
        'user_id',
        'cartStatus',
        'hotel_image',
        'pre_id'
    ];

    public $timestamps = false;

    //create a booking
    public function makeHotelReservation($rateKey, $bookingRef, $HolderFullName, $resevationDate, $hotelName, $checkinTime, $checkoutTime, $noOfAD, $noOfCH, $bedType, $roomType, $noOfRooms, $boardCode, $remarks, $resevationPlatform, $resevationStatus, $currency, $cancelation, $modification, $cancelation_amount, $cancelation_deadline, $user_Id, $preId)
    {
        try {

            set_time_limit(0);

            $hotelRes = HotelResevation::create([
                'rate_key' => $rateKey,
                'resevation_no' => $bookingRef,
                'resevation_name' => $HolderFullName,
                'resevation_date' => $resevationDate,
                'hotel_name' => $hotelName,
                'checkin_time' => $checkinTime,
                'checkout_time' => $checkoutTime,
                'baby_crib' => '-',
                'no_of_adults' => $noOfAD,
                'no_of_childs' => $noOfCH,
                'bed_type' => $bedType,
                'room_type' => $roomType,
                'no_of_rooms' => $noOfRooms,
                'board_code' => $boardCode,
                'special_notice' => $remarks,
                'resevation_platform' => $resevationPlatform,
                'resevation_status' => $resevationStatus,
                'currency' => $currency,
                'cancelation' => $cancelation,
                'modification' => $modification,
                'cancelation_amount' => $cancelation_amount,
                'cancelation_deadline' => $cancelation_deadline,
                'booking_remarks' => $remarks,
                'status' => 'New',
                'user_id' => $user_Id,
                'pre_id' => $preId
            ]);

            return $hotelRes->id;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //reservation dataset for send email to customer functions STARTING #####################
    public function reservationFullDataset($bookingId)
    {
        try {
            $dataJoinOne = DB::table('tbl_hotel_resevation')
                ->where('tbl_hotel_resevation.resevation_no', $bookingId)
                ->where('tbl_hotel_resevation.status', 'New')
                ->leftJoin('users', 'tbl_hotel_resevation.user_id', '=', 'users.id')
                ->leftJoin('tbl_hotel_roomdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_roomdetails.resevation_no')
                ->leftJoin('tbl_hotel_resevation_payments', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_resevation_payments.resevation_no')
                ->leftJoin('tbl_hotel', 'tbl_hotel_resevation.hotel_name', '=', 'tbl_hotel.id')
                ->leftJoin('tbl_hotel_vendor', 'tbl_hotel_resevation.hotel_name', '=', 'tbl_hotel_vendor.hotel_id')
                ->select(
                    'users.email',
                    'tbl_hotel_resevation.*',
                    'tbl_hotel_resevation.hotel_name AS ResHotelName',
                    'tbl_hotel_resevation.id AS InoiceId',
                    'tbl_hotel_roomdetails.*',
                    'tbl_hotel_resevation_payments.*',
                    'tbl_hotel.hotel_name',
                    'tbl_hotel.hotel_address',
                    'tbl_hotel_vendor.hotel_email'
                )->first();

            return $dataJoinOne;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function reservationDatasetSecond($bookingId)
    {
        try {
            $dataJoinTwo = DB::table('tbl_hotel_resevation')->where('tbl_hotel_resevation.resevation_no', '=', $bookingId)->select('*')->get();

            return $dataJoinTwo;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function reservationDatasetDetail($bookingId)
    {
        try {
            $detailJoin = DB::table('tbl_hotel_resevation')
                ->where('tbl_hotel_resevation.resevation_no', $bookingId)
                ->where('tbl_hotel_resevation.status', 'New')
                ->leftJoin('tbl_hotel_roomdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_roomdetails.resevation_no')
                ->leftJoin('tbl_hotel_mealdetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_mealdetail.resevation_no')
                ->leftJoin('tbl_hotel_servicedetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_servicedetail.resevation_no')
                ->leftJoin('tbl_hotel_travellerdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_travellerdetails.resevation_no')
                ->select(
                    'tbl_hotel_roomdetails.adult_count AS AdultCount',
                    'tbl_hotel_roomdetails.child_count AS ChildCount',
                    'tbl_hotel_mealdetail.meal_plan AS MealPlan',
                    'tbl_hotel_mealdetail.adult_count AS MealAdult',
                    'tbl_hotel_mealdetail.child_count AS MealChild',
                    'tbl_hotel_mealdetail.date AS MealDate',
                    'tbl_hotel_mealdetail.special_request AS MealSpeReq',
                    'tbl_hotel_mealdetail.unit_price AS MealPrice',
                    'tbl_hotel_servicedetail.service_type AS SerType',
                    'tbl_hotel_servicedetail.unit_price AS ServicePrice',
                    'tbl_hotel_servicedetail.child_count AS SerChildCount',
                    'tbl_hotel_servicedetail.date AS SerDate',
                    'tbl_hotel_servicedetail.unit_price AS SerPerPrice',
                    'tbl_hotel_travellerdetails.type AS PaxType'
                )->get();

            return $detailJoin;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function reservationDatasetThird($bookingId)
    {
        try {
            $dataJoinThree = DB::table('tbl_hotel_resevation')
                ->where('tbl_hotel_resevation.resevation_no', $bookingId)
                ->where('tbl_hotel_resevation.status', 'New')
                ->leftJoin('tbl_hotel_mealdetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_mealdetail.resevation_no')
                ->select('*')->get();

            return $dataJoinThree;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //reservation dataset for send email to customer functions ENDING #####################
}
