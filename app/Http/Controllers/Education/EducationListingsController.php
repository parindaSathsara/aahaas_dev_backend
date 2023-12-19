<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use App\Models\Customer\MainCheckout;
use App\Models\CustomerCustomCarts;
use App\Models\Education\EducationBookings;
use App\Models\Education\EducationDetails;
use App\Models\Education\EducationListings;
use App\Models\Education\EducationServiceLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class EducationListingsController extends Controller
{







    public function createEducationListing(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'course_name' => 'required',
                'education_type' => 'required',
                'medium' => 'required',
                // 'image_path' => 'required',
                'intro_video_id' => 'required',
                'vendor_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {
                $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

                $code = random_int(100000, 999999);
                $prod_images = array();
                $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

                if ($request->hasFile('image0')) {
                    $filesLength = $request->input('imageLength');
                    $intLength = (int)$filesLength - 1;

                    for ($x = 0; $x <= $intLength; $x++) {
                        $file = $request->file('image' . $x);
                        $fileExtension = $file->getClientOriginalExtension();
                        $fileName = $code . $file->getClientOriginalName();
                        $upload_path = 'uploads/education_images/';
                        $image_url = $upload_path . $fileName;
                        $file->move($upload_path, $fileName);
                        $prod_images[] = $image_url;
                    }
                }



                EducationListings::create([
                    'education_id' => $request->input('education_id'),
                    'course_name' => $request->input('course_name'),
                    'course_description' => $request->input('course_description'),
                    'education_type' => $request->input('education_type'),
                    'medium' => $request->input('medium'),
                    'course_mode' => $request->input('course_mode'),
                    'couse_type' => $request->input('couse_type'),
                    'group_type' => $request->input('group_typ'),
                    'sessions' => $request->input('sessions'),
                    'free_session' => $request->input('free_session'),
                    'payment_method' => $request->input('payment_method'),
                    'status' => $request->input('status'),
                    'image_path' => implode(',', $prod_images),
                    'intro_video_id' => $request->input('intro_video_id'),
                    'user_active' => $request->input('user_active'),
                    'vendor_id' => $request->input('vendor_id'),
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'updated_by' => 'User1'
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Education Data Created'
                ]);
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'error_message' => throw $exception

            ]);
        }
    }


    public function addEducationBooking(Request $request)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

        $viewStatus = $request->input('viewStatus');
        $preID = $request->input('preId');


        if ($viewStatus == "update") {

            $educationBookings = EducationBookings::where('booking_id', $preID)->update([

                'education_id' => $request->input('education_id'),
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


            CustomerCustomCarts::where('education_pre_id', $preID)->update([
                'main_category_id' => 5,
                'cart_id' => $request->input('cart_id'),
                'listing_pre_id' => '',
                'lifestyle_pre_id' => '',
                'hotels_pre_id' => '',
                // 'education_pre_id' => $educationBookings->id,
                'cart_status' => 'InCart',
                'cart_added_date' => $currentTime,
                'customer_id' => $request->input('user_id'),
                'order_preffered_date' => $request->input('preffered_booking_date'),
            ]);
        } else {

            $educationBookings = EducationBookings::create([
                'education_id' => $request->input('education_id'),
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


            CustomerCustomCarts::create([
                'main_category_id' => 5,
                'cart_id' => $request->input('cart_id'),
                'listing_pre_id' => '',
                'lifestyle_pre_id' => '',
                'hotels_pre_id' => '',
                'education_pre_id' => $educationBookings->id,
                'cart_status' => 'InCart',
                'cart_added_date' => $currentTime,
                'customer_id' => $request->input('user_id'),
                'order_preffered_date' => $request->input('preffered_booking_date'),
            ]);

            if ($request->input('status') == 'Booked') {
                MainCheckout::create([
                    'checkout_id' => $request['orderid'],
                    'essnoness_id' => null,
                    'lifestyle_id' => null,
                    'education_id' => $request->input('education_id'),
                    'hotel_id' => null,
                    'flight_id' => null,
                    'main_category_id' => '5',
                    'quantity' => null,
                    'each_item_price' => null,
                    'total_price' => $request->input('totalPrice'),
                    'discount_price' => $request->input('discount_amount'),
                    'bogof_item_name' => null,
                    'delivery_charge' => null,
                    'discount_type' => null,
                    'child_rate' => '-',
                    'adult_rate' => '-',
                    'discountable_child_rate' => null,
                    'discountable_adult_rate' => null,
                    'flight_trip_type' => null,
                    'flight_total_price' => null,
                    'related_order_id' => $request->input('education_id'),
                    'status' => 'CustomerOrdered',
                    'delivery_status' => null,
                    'cx_id' => $educationBookings->user_id,
                ]);
            }
        }



        return response()->json([
            'status' => 200,
            'message' => $educationBookings
        ]);
    }

    public function createEducationDetails(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $opening_time = date('H:i:s', strtotime($request->input('opening_time')));
            $closing_time = date('H:i:s', strtotime($request->input('closing_time')));

            EducationDetails::create([
                'edu_id' => $request->input('edu_id'),
                'curriculum' => $request->input('curriculum'),
                'no_of_hours_session' => $request->input('no_of_hours_session'),
                'duration' => $request->input('duration'),
                'max_age_of_course' => $request->input('max_age_of_course'),
                'min_age_of_course' => $request->input('min_age_of_course'),
                'opening_time' => $opening_time,
                'closing_time' => $closing_time,
                'closed_days' => $request->input('closed_days'),
                'closed_dates' => $request->input('closed_dates'),
                'location_type_id' => $request->input('location_type_id'),

                'created_at' => $currentTime,
                'updated_at' => $currentTime,
                'updated_by' => 'User1'
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Education Details Created'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }


    public function getEducationCourseNames()
    {
        try {
            $courses = EducationListings::get();

            return response()->json([
                'status' => 200,
                'course_name' => $courses
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }

    //Educations has Service Locations

    public function getEducationServiceLocations($id)
    {
        try {
            $serviceLocations = EducationServiceLocation::where('edu_vendor_id', $id)->get();

            return response()->json([
                'status' => 200,
                'service_location' => $serviceLocations
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }

    // get-session-video-by-lesson-id
    public function getSessionVideoByLessonID(Request $request)
    {
        $user_id = $request->input('user_id');
        $session_id = $request->input('session_id');
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d');

        // $currentDate = \Carbon\Carbon::now('Asia/Kolkata')->toDateString();
        $currentDate = '2022-11-21';

        try {
            //------------------------------------------------------------------------------------------------------------------
            $education_data = DB::table('edu_tbl_booking')
                ->join('edu_tbl_sessions', 'edu_tbl_booking.session_id', '=', 'edu_tbl_sessions.session_id')
                ->join('edu_tbl_education', 'edu_tbl_sessions.education_id', '=', 'edu_tbl_education.education_id')

                ->where('edu_tbl_booking.session_id', $session_id)
                ->select(
                    'edu_tbl_sessions.start_date',
                    'edu_tbl_sessions.end_date',
                    'edu_tbl_sessions.day',
                )
                ->get();




            $daySchedule = [];
            foreach ($education_data as $val) {
                if ($val->day == "Monday") {
                    $startDate = Carbon::parse($val->start_date)->next(Carbon::MONDAY);
                } else if ($val->day == "Tuesday") {
                    $startDate = Carbon::parse($val->start_date)->next(Carbon::TUESDAY);
                } else if ($val->day == "Wednesday") {
                    $startDate = Carbon::parse($val->start_date)->next(Carbon::WEDNESDAY);
                } else if ($val->day == "Thursday") {
                    $startDate = Carbon::parse($val->start_date)->next(Carbon::THURSDAY);
                } else if ($val->day == "Friday") {
                    $startDate = Carbon::parse($val->start_date)->next(Carbon::FRIDAY);
                } else if ($val->day == "Saturday") {
                    $startDate = Carbon::parse($val->start_date)->next(Carbon::SATURDAY);
                } else {
                    $startDate = Carbon::parse($val->start_date)->next(Carbon::SUNDAY);
                }

                $endDate = Carbon::parse($val->end_date);

                for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {


                    $daySchedule[] = $date->format('Y-m-d');
                }
            }


            //------------------------------------------------------------------------------------------------------------------
            if (in_array($currentTime, $daySchedule)) {
                $education_data = DB::table('edu_tbl_booking')
                    ->join('edu_tbl_sessions', 'edu_tbl_booking.session_id', '=', 'edu_tbl_sessions.session_id')
                    ->join('edu_tbl_education', 'edu_tbl_sessions.education_id', '=', 'edu_tbl_education.education_id')

                    ->where('edu_tbl_booking.user_id', $user_id)
                    ->where('edu_tbl_booking.session_id', $session_id)
                    ->where('edu_tbl_sessions.start_date', '<=', $currentTime)
                    ->where('edu_tbl_sessions.end_date', '>=', $currentTime)
                    ->select('*')
                    ->get();

                return response()->json([
                    'status' => 200,
                    'education' => $education_data
                ]);
            } else {
                return response()->json([
                    'status' => 250,
                    'education' => "No Educations"

                ]);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }








    public function getAllEducations($category1, $category2, $category3, $category4)
    {

        $whereArray = array();

        if ($category1 != 0 && $category2 != 0 && $category3 != 0 && $category4 != 0) {
            $whereArray = [['edu_tbl_education.category1', '=', $category1], ['edu_tbl_education.category2', '=', $category2], ['edu_tbl_education.category3', '=', $category3], ['edu_tbl_education.category4', '=', $category4]];
        } else if ($category3 != 0 && $category2 != 0 && $category3 != 0) {
            $whereArray = [['edu_tbl_education.category1', '=', $category1], ['edu_tbl_education.category2', '=', $category2], ['edu_tbl_education.category3', '=', $category3]];
        } else if ($category1 != 0 && $category2 != 0) {
            $whereArray = [['edu_tbl_education.category1', '=', $category1], ['edu_tbl_education.category2', '=', $category2]];
        } else {
            $whereArray = [['edu_tbl_education.category1', '=', $category1]];
        }



        $latestPosts = DB::table('posts')
            ->select('user_id', DB::raw('MAX(created_at) as last_post_created_at'))
            ->where('is_published', true)
            ->groupBy('user_id');

        try {
            $educationListings = DB::table('edu_tbl_education')
                // ->join('edu_tbl_vendor', 'edu_tbl_education.vendor_id', '=', 'edu_tbl_vendor.id')
                // ->join('edu_tbl_details', 'edu_tbl_education.education_id', '=', 'edu_tbl_details.edu_id')
                ->join('edu_tbl_inventory', 'edu_tbl_education.education_id', '=', 'edu_tbl_inventory.edu_id')
                ->join('edu_tbl_rate', 'edu_tbl_inventory.id', '=', 'edu_tbl_rate.edu_inventory_id')
                ->leftJoin('edu_tbl_termscond', 'edu_tbl_education.education_id', 'edu_tbl_termscond.edu_id')
                ->leftJoin('edu_tbl_discount', 'edu_tbl_education.education_id', '=', 'edu_tbl_discount.edu_id')
                ->select(
                    'edu_tbl_education.*',
                    // 'edu_tbl_vendor.*',
                    // 'edu_tbl_details.*',
                    'edu_tbl_inventory.*',
                    'edu_tbl_rate.currency',
                    'edu_tbl_rate.adult_course_fee',
                    'edu_tbl_rate.child_course_fee',
                    'edu_tbl_rate.deadline_no_ofdays',
                    'edu_tbl_rate.course_admission_deadline',
                    'edu_tbl_rate.sale_start',
                    'edu_tbl_termscond.cancel_deadline',
                    'edu_tbl_discount.discount_type',
                    'edu_tbl_discount.value'
                )
                ->where($whereArray)
                ->get();


            return $educationListings;

            $groupType = DB::table('edu_tbl_education')
                ->join('edu_tbl_vendor', 'edu_tbl_education.vendor_id', '=', 'edu_tbl_vendor.id')
                ->join('edu_tbl_details', 'edu_tbl_education.education_id', '=', 'edu_tbl_details.edu_id')
                ->join('edu_tbl_inventory', 'edu_tbl_education.education_id', '=', 'edu_tbl_inventory.edu_id')
                ->join('edu_tbl_rate', 'edu_tbl_inventory.id', '=', 'edu_tbl_rate.edu_inventory_id')
                ->select(
                    'edu_tbl_education.group_type',
                )
                ->where($whereArray)
                ->groupBy('group_type')
                ->get();


            $courseMode = DB::table('edu_tbl_education')
                ->join('edu_tbl_vendor', 'edu_tbl_education.vendor_id', '=', 'edu_tbl_vendor.id')
                ->join('edu_tbl_details', 'edu_tbl_education.education_id', '=', 'edu_tbl_details.edu_id')
                ->join('edu_tbl_inventory', 'edu_tbl_education.education_id', '=', 'edu_tbl_inventory.edu_id')
                ->join('edu_tbl_rate', 'edu_tbl_inventory.id', '=', 'edu_tbl_rate.edu_inventory_id')
                ->select(
                    'edu_tbl_education.course_mode',
                )
                ->where($whereArray)
                ->groupBy('course_mode')
                ->get();

            $sessionMode = DB::table('edu_tbl_education')
                ->join('edu_tbl_vendor', 'edu_tbl_education.vendor_id', '=', 'edu_tbl_vendor.id')
                ->join('edu_tbl_details', 'edu_tbl_education.education_id', '=', 'edu_tbl_details.edu_id')
                ->join('edu_tbl_inventory', 'edu_tbl_education.education_id', '=', 'edu_tbl_inventory.edu_id')
                ->join('edu_tbl_rate', 'edu_tbl_inventory.id', '=', 'edu_tbl_rate.edu_inventory_id')
                ->select(
                    'edu_tbl_education.sessions',
                )
                ->where($whereArray)
                ->groupBy('edu_tbl_education.sessions')
                ->get();

            $curriculumType = DB::table('edu_tbl_education')
                ->join('edu_tbl_vendor', 'edu_tbl_education.vendor_id', '=', 'edu_tbl_vendor.id')
                ->join('edu_tbl_details', 'edu_tbl_education.education_id', '=', 'edu_tbl_details.edu_id')
                ->join('edu_tbl_inventory', 'edu_tbl_education.education_id', '=', 'edu_tbl_inventory.edu_id')
                ->join('edu_tbl_rate', 'edu_tbl_inventory.id', '=', 'edu_tbl_rate.edu_inventory_id')
                ->select(
                    'edu_tbl_details.curriculum',
                )
                ->orderBy('edu_tbl_details.curriculum', 'DESC')
                ->where($whereArray)
                ->groupBy('edu_tbl_details.curriculum')
                ->get();

            return response()->json([
                'status' => 200,
                'educationListings' => $educationListings,
                'groupType' => $groupType,
                'courseMode' => $courseMode,
                'sessionMode' => $sessionMode,
                'curriculumType' => $curriculumType
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }


    public function getAllEducationsByID($id)
    {
        try {
            $educationListings = DB::table('edu_tbl_education')
                ->join('edu_tbl_vendor', 'edu_tbl_education.vendor_id', '=', 'edu_tbl_vendor.id')
                ->join('edu_tbl_servicelocation', 'edu_tbl_education.vendor_id', '=', 'edu_tbl_servicelocation.edu_vendor_id')
                ->join('edu_tbl_details', 'edu_tbl_education.education_id', '=', 'edu_tbl_details.edu_id')
                ->join('edu_tbl_inventory', 'edu_tbl_education.education_id', '=', 'edu_tbl_inventory.edu_id')
                ->join('edu_tbl_rate', 'edu_tbl_education.education_id', '=', 'edu_tbl_rate.edu_id')
                ->join('edu_tbl_termscond', 'edu_tbl_education.education_id', '=', 'edu_tbl_termscond.edu_id')
                ->leftJoin('edu_tbl_discount', 'edu_tbl_rate.id', '=', 'edu_tbl_discount.edu_rate_id')
                ->select(
                    'edu_tbl_education.*',
                    'edu_tbl_vendor.*',
                    'edu_tbl_details.*',
                    'edu_tbl_inventory.*',
                    'edu_tbl_rate.currency',
                    'edu_tbl_rate.adult_course_fee',
                    'edu_tbl_rate.child_course_fee',
                    'edu_tbl_rate.id AS rateID',
                    'edu_tbl_rate.deadline_no_ofdays AS DeadNoOfDays',
                    'edu_tbl_rate.course_admission_deadline AS DeadDate',
                    'edu_tbl_rate.sale_start',
                    'edu_tbl_discount.id as discountID',
                    'edu_tbl_discount.edu_id',
                    'edu_tbl_discount.edu_inventory_id',
                    'edu_tbl_discount.edu_rate_id',
                    'edu_tbl_discount.value',
                    'edu_tbl_discount.discount_type',
                    'edu_tbl_servicelocation.*',
                    'edu_tbl_termscond.*'
                )
                ->where('edu_tbl_education.education_id', $id)
                ->get();

            $educationDates = DB::table('edu_tbl_inventory')
                ->select('edu_tbl_inventory.course_inv_startdate', 'edu_tbl_inventory.course_startime', 'edu_tbl_inventory.course_endtime', 'edu_tbl_inventory.id')
                ->where('edu_tbl_inventory.edu_id', $id)
                ->get();



            $educationDatesList = [];

            foreach ($educationDates as $dates) {
                $educationDatesList[] = $dates->course_inv_startdate;
            }


            // $educationDiscounts = DB::table('edu_tbl_discount')
            //     ->where('edu_tbl_discount.edu_id', $id)
            //     ->get();

            return response()->json([
                'status' => 200,
                'educationDates' => $educationDatesList,
                'educationListings' => $educationListings,
                'educationInventory' => $educationDates
                // 'educationDiscounts' => $educationDiscounts
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }



    public function updateBlackoutDays(Request $request)
    {
        try {

            $blackoutDays = implode(',', $request->input('closed_days'));
            $blackoutDates = implode(',', $request->input('closed_dates'));

            DB::table('edu_tbl_details')
                ->where('edu_id', $request->input('edu_id'))
                ->update(['closed_dates' => $blackoutDates, 'closed_days' => $blackoutDays]);

            return response()->json([
                'status' => 200,
                'message' => "Connection Success"
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }


    public function getEducationsByID(Request $request)
    {
    }
}
