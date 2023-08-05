<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerNotificationController extends Controller
{


    public function getReminders($id)
    {
        $user_id = $id;
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d');

        $time = \Carbon\Carbon::now('Asia/Kolkata')->toTimeString();

        $education_data1 = [];
        $education_data2 = [];
        // $currentDate = \Carbon\Carbon::now('Asia/Kolkata')->toDateString();
        // $currentDate = '2022-11-21';

        try {
            //------------------------------------------------------------------------------------------------------------------
            $education_data = DB::table('edu_tbl_booking')
                ->join('edu_tbl_sessions', 'edu_tbl_booking.session_id', '=', 'edu_tbl_sessions.session_id')
                ->join('edu_tbl_education', 'edu_tbl_sessions.education_id', '=', 'edu_tbl_education.education_id')

                ->select(
                    'edu_tbl_sessions.start_date',
                    'edu_tbl_sessions.end_date',
                    'edu_tbl_sessions.day',
                    'edu_tbl_education.education_id'
                )
                ->get();

            // return $education_data;

            $daySchedule = [];
            foreach ($education_data as $val) {
                if ($val->day == "Monday") {
                    $startDate = Carbon::parse($val->start_date)->subDays(1)->next(Carbon::MONDAY);
                } else if ($val->day == "Tuesday") {
                    $startDate = Carbon::parse($val->start_date)->subDays(1)->next(Carbon::TUESDAY);
                } else if ($val->day == "Wednesday") {
                    $startDate = Carbon::parse($val->start_date)->subDays(1)->next(Carbon::WEDNESDAY);
                } else if ($val->day == "Thursday") {
                    $startDate = Carbon::parse($val->start_date)->subDays(1)->next(Carbon::THURSDAY);
                } else if ($val->day == "Friday") {
                    $startDate = Carbon::parse($val->start_date)->subDays(1)->next(Carbon::FRIDAY);
                } else if ($val->day == "Saturday") {
                    $startDate = Carbon::parse($val->start_date)->subDays(1)->next(Carbon::SATURDAY);
                } else {
                    $startDate = Carbon::parse($val->start_date)->subDays(1)->next(Carbon::SUNDAY);
                }

                $endDate = Carbon::parse($val->end_date);

                for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {


                    $daySchedule[] = $date->format('Y-m-d');
                }
            }


            // return $daySchedule;

            // return $currentDate;

            //------------------------------------------------------------------------------------------------------------------
            if (in_array($currentTime, $daySchedule)) {
                $education_data1 = DB::table('edu_tbl_booking')
                    ->join('edu_tbl_sessions', 'edu_tbl_booking.session_id', '=', 'edu_tbl_sessions.session_id')
                    ->join('edu_tbl_education', 'edu_tbl_sessions.education_id', '=', 'edu_tbl_education.education_id')

                    ->where('edu_tbl_booking.user_id', $user_id)
                    ->where('edu_tbl_sessions.start_date', '<=', $currentTime)
                    ->where('edu_tbl_sessions.end_date', '>=', $currentTime)
                    ->where('edu_tbl_sessions.start_time', '>=', $time)
                    ->where('edu_tbl_booking.status','Completed')
                    ->whereRaw("TIMEDIFF(edu_tbl_sessions.start_time,'" . $time . "')  < '00:05:00'")
                    ->select(
                        '*',
                        DB::raw("TIMEDIFF(edu_tbl_sessions.start_time,'" . $time . "')")
                    )

                    ->get();


                $education_data2 = DB::table('edu_tbl_booking')
                    ->join('edu_tbl_sessions', 'edu_tbl_booking.session_id', '=', 'edu_tbl_sessions.session_id')
                    ->join('edu_tbl_education', 'edu_tbl_sessions.education_id', '=', 'edu_tbl_education.education_id')

                    ->where('edu_tbl_booking.user_id', $user_id)
                    ->where('edu_tbl_sessions.start_date', '<=', $currentTime)
                    ->where('edu_tbl_sessions.end_date', '>=', $currentTime)
                    ->where('edu_tbl_sessions.start_time', '>=', $time)
                    ->where('edu_tbl_booking.status','Completed')
                    ->whereRaw("TIMEDIFF(edu_tbl_sessions.start_time,'" . $time . "')  < '00:15:00'")
                    ->select(
                        '*',
                        DB::raw("TIMEDIFF(edu_tbl_sessions.start_time,'" . $time . "')")
                    )

                    ->get();
            }


            $LifeStyleData = DB::table('tbl_lifestyle_bookings')
                ->where('user_id', '=', $id)
                ->where('tbl_lifestyle_bookings.booking_date', $currentTime)
                ->where('tbl_lifestyle_bookings.booking_status', 'Completed')
                ->join('tbl_lifestyle_inventory', 'tbl_lifestyle_bookings.lifestyle_inventory_id', 'tbl_lifestyle_inventory.lifestyle_inventory_id')
                ->join('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->select("*", 'tbl_lifestyle_inventory.latitude as lFLatitiude', 'tbl_lifestyle_inventory.longitude as lFLongitude')
                ->whereRaw("TIMEDIFF(SUBSTRING_INDEX(pickup_time, '-', 1),'" . $time . "')  < '01:00:00.000000'")
                ->get();

            return response()->json([
                'status' => 200,
                'education' => $education_data1,
                'education2' => $education_data2,
                'LifeStyleData' => $LifeStyleData
                // 'education_next_date' => $daySchedule[0]
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }


    public function fetchHotelsNotifications($id)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateString();

        try {

            $HotelData = DB::table('tbl_hotel_resevation')
                ->where('user_id', '=', $id)
                ->where('checkin_time', $currentTime)
                // ->join('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->select('*')
                ->get();

            return response(['status' => 200, 'hotel_data' => $HotelData]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }


    public function fetchLifeStyleNotifications($id)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateString();

        try {

            $LifeStyleData = DB::table('tbl_lifestyle_bookings')
                ->where('user_id', '=', $id)
                ->where('tbl_lifestyle_bookings.booking_date', $currentTime)
                ->where('tbl_lifestyle_bookings.booking_status', 'Completed')
                ->join('tbl_lifestyle_inventory', 'tbl_lifestyle_bookings.lifestyle_inventory_id', 'tbl_lifestyle_inventory.lifestyle_inventory_id')
                ->join('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->select('*')

                ->get();

            return response(['status' => 200, 'ls_data' => $LifeStyleData]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }


    public function fetchEssentialsNotifications($id, $catID)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateString();

        try {

            $OrderData = DB::table('tbl_products_orders')
                ->where('customer_id', '=', $id)
                ->where('tbl_product_details.category1', $catID)
                ->where('tbl_products_orders.preffered_delivery_date', $currentTime)
                ->join('tbl_listing_inventory', 'tbl_products_orders.inventory_id', '=', 'tbl_listing_inventory.id')
                ->join('tbl_product_listing', 'tbl_products_orders.listing_id', '=', 'tbl_product_listing.id')
                ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')

                ->get();

            return response(['status' => 200, 'order_data' => $OrderData]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }


    public function fetchEducationNotifications($id)
    {
        try {
            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateString();

            $EducationData = DB::table('edu_tbl_booking')
                ->where('user_id', '=', $id)
                ->where('status', 'Completed')
                ->where('preffered_booking_date', $currentTime)
                ->join('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')
                ->select('*', 'edu_tbl_booking.status AS booking_status')->get();

            return response(['status' => 200, 'edu_data' => $EducationData]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 401,
                'message' => throw $ex
            ]);
        }
    }
}
