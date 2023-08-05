<?php

namespace App\Http\Controllers\Hotels\HotelTBO;

use App\Http\Controllers\Controller;
use App\Models\Customer\MainCheckout;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Hotels\HotelTBO\HotelCodes;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Hotels\HotelResevation;
use App\Models\Hotels\HotelRoomDetails;
use App\Models\Hotels\HotelResevationPayment;
use App\Models\Hotels\HotelsPreBookings;
use App\Models\Hotels\HotelTravellerDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class HotelTBOController extends Controller
{

    // var $Username = config('services.hoteltbo.username');
    // var $Password = config('services.hoteltbo.password');

    // Username and Password for HOTEL TBO
    function userAuth()
    {

        $Username = config('services.hoteltbo.username');
        $Password = config('services.hoteltbo.password');

        $Credentials = ['Username' => $Username, 'Password' => $Password];

        return $Credentials;
    }

    // Using Headers for Authorized the Connection
    function getHeaders()
    {

        $Headers = [];

        $Username = config('services.hoteltbo.username');
        $Password = config('services.hoteltbo.password');

        $Header['Authorization'] = 'Basic ' . base64_encode($Username . ':' . $Password);
        $Header['Accept'] = 'application/json';
        $Header['Content-Type'] = 'application/json';


        return $Headers;
    }

    // ******************* Getting all the country list in HOTEL TBO *******************
    public function getCountryList()
    {
        $URL = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/CountryList';

        $response = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))->get($URL)->json();

        return $response;
    }

    // ******************* Getting all the country list in HOTEL TBO End *******************

    // ******************* Getting Hotel Details *******************
    public function getHotelDetails()
    {
        // $hotelCode = new HotelCodes();
        ini_set('max_execution_time', 300);
        $testRead = Arr::random($this->getAllHotelCodesTBO(), 10);
        $finalCodes = implode(',', $testRead);

        $URL = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/Hoteldetails';

        $hotelDetail = [];

        $hotelDetail['Hotelcodes'] = preg_replace('/\r|\n/', '', $finalCodes);

        $hotelDetail['Language'] = 'en';

        try {
            $response = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))->post($URL, $hotelDetail)->json();

            return response()->json([
                'status_api' => 200,
                'data_fetch' => $response
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
    // ******************* Getting Hotel Details End *******************

    // ******************* Getting Hotel By Id *******************
    public function getHotelByIdTBO($id)
    {
        $DataArray = [];
        $DataArray['Hotelcodes'] = $id;
        $DataArray['Language'] = 'en';

        $URL = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/Hoteldetails';

        try {

            $response = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))->post($URL, $DataArray)->json();

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    // ******************* Getting Hotel By Id End *******************

    // ******************* Getting Hotel Code *******************
    public function getAllHotelCodesTBO()
    {
        $URL = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/hotelcodelist';

        $response = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))->get($URL)->json();

        // $hotelCodeList = HotelCodes::getHotelCodesTBO();

        return $response['HotelCodes'];
    }
    // ******************* Getting Hotel Code End *******************

    // ******************* Booking Availability Checking *******************
    public function searchRoomForAvailable(Request $request)
    {
        // ini_set('max_execution_time', 0);
        $DataArray = [];
        $URL = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/search';

        $CheckInDate = date('Y-m-d', strtotime($request->input('checkInDate')));
        $CheckOutDate = date('Y-m-d', strtotime($request->input('checkOutDate')));
        $hotelCode = $request->input('hotelCode');
        $guestNationality = $request->input('guestNationality');
        $numOfRooms = (int)$request->input('num_of_rooms');
        $childAges = explode(',', $request->input('childAges'));
        $numOfAdults = $request->input('no_of_adults');
        $numOfChilds = $request->input('no_of_childs');

        $DataArray['CheckIn'] = $CheckInDate;
        $DataArray['CheckOut'] = $CheckOutDate;
        $DataArray['HotelCodes'] = $hotelCode;
        $DataArray['GuestNationality'] = $guestNationality;
        $DataArray['Filters']['Refundable'] = 'false';
        $DataArray['Filters']['NoOfRooms'] = $numOfRooms;

        for ($c = 1; $c <= $DataArray['Filters']['NoOfRooms']; $c++) {
            // var_dump($c);
            // $DataArray['Filters']['NoOfRooms'] = 1;
            $DataArray['PaxRooms']['Adults'] = $numOfAdults;
            $DataArray['PaxRooms']['Children'] = $numOfChilds;
        }

        if ($DataArray['PaxRooms']['Children'] >= 0) {
            for ($cc = 0; $cc < $DataArray['PaxRooms']['Children']; $cc++) {
                $DataArray['PaxRooms']['ChildrenAges'][] = $childAges[$cc];
            }
        }

        $DataArray['Filters']['Refundable'] = false;
        $DataArray['Filters']['NoOfRooms'] = $numOfRooms;
        $DataArray['Filters']['MealType'] = 'All';

        $sub_array = [];

        $sub_array['CheckIn'] = $DataArray['CheckIn'];
        $sub_array['CheckOut'] = $DataArray['CheckOut'];
        $sub_array['HotelCodes'] = $DataArray['HotelCodes'];
        $sub_array['GuestNationality'] = $DataArray['GuestNationality'];
        $sub_array['PaxRooms'] = [$DataArray['PaxRooms']];
        $sub_array['Filters'] = $DataArray['Filters'];

        $response = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))->post($URL, $sub_array)->json();

        return $response;
    }

    // ******************* Booking Availability Checking End *******************

    // public function preBookHotelTbo($rateKey)
    // {
    //     $preBookingArray = [];
    //     $preBookingArray['BookingCode'] = $rateKey;
    //     $preBookingArray['PaymentMode'] = 'Limit';

    //     $urlPrebook = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/PreBook';

    //     $responsePreBook = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))
    //         ->post($urlPrebook, $preBookingArray)->json();


    //     return $responsePreBook;
    // }


    public function updateHotelsStatus(Request $request)
    {

        $id = $request->input('hotels_pre_id');
        $status = $request->input('status');

        // return $id;
        HotelsPreBookings::where('booking_id', $id)->update(['cartStatus' => $status]);

        $hotelPreBooking = DB::table('tbl_hotels_pre_booking')->where('booking_id', '=', $id)->get();

        $oid = $request['oid'];
        // return $hotelPreBooking;


        $hotelDataSet = $hotelPreBooking[0];

        $request = ([
            'holderfname' => $hotelDataSet->holderFirstName,
            'holderlname' => $hotelDataSet->holderLastName,
            'paxCount' => $hotelDataSet->paxCount,
            'no_of_adults' => $hotelDataSet->noOfAdults,
            'no_of_childs' => $hotelDataSet->noOfChilds,
            'checkin' => $hotelDataSet->checkingDate,
            'checkout' => $hotelDataSet->checkoutDate,
            'remarks' => $hotelDataSet->remarks,
            'cusTitle' => $hotelDataSet->cusTitle,
            'firstName' => $hotelDataSet->name,
            'lastName' => $hotelDataSet->surname,
            'type' => $hotelDataSet->type,
            'totalFare' => $hotelDataSet->totalFare,
            'rateKey' => $hotelDataSet->rate_key,
            'booking_total' => $hotelDataSet->booking_total,
            'HotelName' => $hotelDataSet->hotel_name,
            'hotelCode' => $hotelDataSet->ref_id,
            'userID' => $hotelDataSet->userID,
        ]);




        // $this->bookHotelRoomTbo($request, $oid);

        // return $data;

        return response()->json([
            'status' => 200,
            'hotel' => $hotelDataSet,
            'hotelTBO' => $this->bookHotelRoomTbo($request, $oid)
        ]);
    }
    // ******************* Booking Hotel *******************
    public function bookHotelRoomTbo($request, $orderid) //$orderid
    {
        $finalBookingArray = [];
        // return $request;

        $rateKey = $request['rateKey'];

        $preBookingArray = [];
        $preBookingArray['BookingCode'] = $rateKey;
        $preBookingArray['PaymentMode'] = 'Limit';

        try {
            $urlPrebook = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/PreBook';

            $responsePreBook = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))
                ->post($urlPrebook, $preBookingArray)->json();

            // return $responsePreBook;

            // *************  *************

            $todayDate = Carbon::now()->format('Y-m-d H:i:s');

            $currentTime = \Carbon\Carbon::now()->toDateTimeString();

            $url_booking = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/Book';

            $HolderFName = $request['holderfname'];
            $HolderlName = $request['holderlname'];

            $paxCount = $request['paxCount'];
            $no_of_adults = $request['no_of_adults'];
            $no_of_childs = $request['no_of_childs'];
            $checkin = $request['checkin'];
            $checkout = $request['checkout'];
            $remarks = $request['remarks'];

            $CusTitle = explode(',', $request['cusTitle']); //;
            $CusFName = explode(',', $request['firstName']); //;
            $CusLName = explode(',', $request['lastName']); //;
            $CusType = explode(',', $request['type']); //;
            $TotalFair = explode(',', $request['totalFare']); //;
            $bookingfair = $request['booking_total'];

            $finalBookingArray['BookingCode'] = $rateKey;

            $resevationFullName = $HolderFName . ' ' . $HolderlName;

            if ($paxCount >= 1) {
                for ($c = 0; $c < $paxCount; $c++) {
                    $finalBookingArray['CustomerDetails']['CustomerNames'][] = ['Title' => $CusTitle[$c], 'FirstName' => $CusFName[$c], 'LastName' => $CusLName[$c], 'Type' => $CusType[$c]];
                }
            }


            // return $finalBookingArray;


            // return $finalBookingArray;
            // *************** Client Reference Key Generating ***************

            $cus = '';

            if (strlen($CusLName[0]) >= 4)
                $cus = strtoupper(substr($CusLName[0], 0, 4));
            else if (strlen($CusLName[0]) == 3)
                $cus = strtoupper(substr($CusLName[0], 0, 3)) . strtoupper(substr($CusLName[0], 0, 1));
            else if (strlen($CusLName[0]) == 2)
                $cus = strtoupper(substr($CusLName[0], 0, 2)) . strtoupper(substr($CusLName[0], 0, 2));
            else if (strlen($CusLName[0]) == 1 && strlen($CusFName[0]) > 1)
                $cus = strtoupper(substr($CusLName[0], 0, 1)) . strtoupper(substr($CusFName[0], 0, 1)) . strtoupper(substr($CusLName[0], 0, 1)) . strtoupper(substr($CusFName[0], 0, 1));


            $ref = substr($todayDate, 8, 2) . substr($todayDate, 5, 2) . substr($todayDate, 2, 2) . substr($todayDate, 11, 2) . substr($todayDate, 14, 2) . substr($todayDate, 17, 2) . '000#' . $cus;

            $finalBookingArray['ClientReferenceId'] = $ref;
            $finalBookingArray['BookingReferenceId'] = $ref;
            $finalBookingArray['TotalFare'] = (float)$bookingfair;
            $finalBookingArray['EmailId'] = 'apisupport@tboholidays.com';
            $finalBookingArray['PhoneNumber'] = '918448780621';
            $finalBookingArray['BookingType'] = 'Voucher';
            $finalBookingArray['PaymentMode'] = 'Limit';

            $subArray = [];
            $subArray['BookingCode'] = $finalBookingArray['BookingCode'];
            $subArray['CustomerDetails'][]['CustomerNames'] = $finalBookingArray['CustomerDetails']['CustomerNames'];
            $subArray['ClientReferenceId'] = $finalBookingArray['ClientReferenceId'];
            $subArray['BookingReferenceId'] = $finalBookingArray['BookingReferenceId'];
            $subArray['TotalFare'] = $finalBookingArray['TotalFare'];
            $subArray['EmailId'] = $finalBookingArray['EmailId'];
            $subArray['PhoneNumber'] = $finalBookingArray['PhoneNumber'];
            $subArray['BookingType'] = $finalBookingArray['BookingType'];
            $subArray['PaymentMode'] = $finalBookingArray['PaymentMode'];

            // return $subArray;

            $responseBook = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))->post($url_booking, $subArray)->json();


            // return $responseBook;

            // return $responseBook;
            if ($responseBook['Status']['Code'] === 405) {
                return response([
                    'status' => 405,
                    'message' => 'Booking Fail'
                ]);
            } else {

                $hotelDetailsArray = [];

                $hotelDetailsArray['Hotelcodes'] = $request['hotelCode'];
                $hotelDetailsArray['Language'] = 'en';

                $hotel_Name = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))
                    ->post('http://api.tbotechnology.in/TBOHolidays_HotelAPI/Hoteldetails', $hotelDetailsArray)->json();



                // ******** Aahaas DB saving part ********

                $BookId = $responseBook['ConfirmationNumber'];

                // return $BookId;
                $ClientReference = $responseBook['ClientReferenceId'];
                $Currency = $responsePreBook['HotelResult'][0]['Currency'];
                $HotelName = $request['HotelName'];
                $RoomName = $responsePreBook['HotelResult'][0]['Rooms'][0]['Name'][0];
                $TotalFair = $responsePreBook['HotelResult'][0]['Rooms'][0]['TotalFare'];
                $CancellationDate = $responsePreBook['HotelResult'][0]['Rooms'][0]['CancelPolicies'][0]['FromDate'];
                $CancellationFair = $TotalFair;
                $MealType = $responsePreBook['HotelResult'][0]['Rooms'][0]['MealType'];

                $cancelDate = date('Y-m-d', strtotime($CancellationDate));


                $created_at = $currentTime;
                $updated_at = $currentTime;
                $user_Id = $request['userID'];

                $hotelRes = HotelResevation::create([
                    'rate_key' => $rateKey,
                    'resevation_no' => $BookId,
                    'resevation_name' => $resevationFullName,
                    'resevation_date' => $todayDate,
                    'hotel_name' => $HotelName,
                    'checkin_time' => $checkin,
                    'checkout_time' => $checkout,
                    'baby_crib' => '-',
                    'no_of_adults' => $no_of_adults,
                    'no_of_childs' => $no_of_childs,
                    'bed_type' => '-',
                    'room_type' => $RoomName,
                    'no_of_rooms' => 0,
                    'board_code' => $MealType,
                    'special_notice' => $remarks,
                    'resevation_platform' => 'HOTELTBO',
                    'resevation_status' => 'CONFIRMED',
                    'currency' => $Currency,
                    'cancelation' => '-',
                    'modification' => '-',
                    'cancelation_amount' => $CancellationFair,
                    'cancelation_deadline' => $cancelDate,
                    'booking_remarks' => $remarks,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
                    'user_id' => $user_Id
                ]);
                // return $RoomName;

                MainCheckout::create([
                    'checkout_id' => $orderid,
                    'essnoness_id' => null,
                    'lifestyle_id' => null,
                    'education_id' => null,
                    'hotel_id' => $hotelRes->id,
                    'flight_id' => null,
                    'main_category_id' => '4',
                    'quantity' => null,
                    'each_item_price' => null,
                    'total_price' => $TotalFair,
                    'discount_price' => 0.00,
                    'bogof_item_name' => null,
                    'delivery_charge' => null,
                    'discount_type' => null,
                    'child_rate' => '-',
                    'adult_rate' => '-',
                    'discountable_child_rate' => null,
                    'discountable_adult_rate' => null,
                    'flight_trip_type' => null,
                    'flight_total_price' => null,
                    'related_order_id' => $hotelRes->id,
                    'currency' => $Currency,
                    'status' => 'Booked',
                    'delivery_status' => null,
                    'delivery_date' => null,
                    'delivery_address' => null,
                    'cx_id' => $user_Id,
                ]);

                HotelResevationPayment::create([
                    'resevation_no' => $BookId,
                    'total_amount' => $TotalFair,
                    'paid_amount' => 0.00,
                    'balance_payment' => $TotalFair,
                    'payment_method' => '-',
                    'payment_status' => 'Not Paid',
                    'payment_slip_image' => '-'
                ]);

                if ($finalBookingArray['CustomerDetails']['CustomerNames'] >= 1) {
                    foreach ($finalBookingArray['CustomerDetails']['CustomerNames'] as $pax) {

                        $fName = $pax['FirstName'];
                        $sName = $pax['LastName'];
                        HotelRoomDetails::create([
                            'resevation_no' => $BookId,
                            'room_code' => $RoomName,
                            'adult_count' => $no_of_adults,
                            'child_count' => $no_of_childs,
                            'adult_rate' => 0.00,
                            'child_withbed_rate' => 0.00,
                            'child_nobed_rate' => 0.00
                        ]);
                    }
                }

                if ($finalBookingArray['CustomerDetails']['CustomerNames'] >= 1) {
                    foreach ($finalBookingArray['CustomerDetails']['CustomerNames'] as $pax) {

                        $fName = $pax['FirstName'];
                        $sName = $pax['LastName'];
                        $type = $pax['Type'];
                        HotelTravellerDetail::create([
                            'resevation_no' => $BookId,
                            'first_name' => $fName,
                            'last_name' => $sName,
                            'type' => $type
                        ]);
                    }
                }

                return $this->sendEmail($BookId);
            }
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
    // ******************* Booking Hotel End *******************

    public function singleBookHotelRoomTbo(Request $request) //$orderid
    {
        $finalBookingArray = [];

        $rateKey = $request->input('rateKey');

        // $preBookResponse = $this->preBookHotelTbo($rateKey);

        $preBookingArray = [];
        $preBookingArray['BookingCode'] = $rateKey;
        $preBookingArray['PaymentMode'] = 'Limit';

        $urlPrebook = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/PreBook';

        try {
            $responsePreBook = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))
                ->post($urlPrebook, $preBookingArray)->json();

            // return $responsePreBook;

            // *************  *************

            $todayDate = Carbon::now()->format('Y-m-d H:i:s');

            $currentTime = \Carbon\Carbon::now()->toDateTimeString();

            $url_booking = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/Book';

            $HolderFName = $request->input('holderfname');
            $HolderlName = $request->input('holderlname');

            $paxCount = $request->input('paxCount');
            $no_of_adults = $request->input('no_of_adults');
            $no_of_childs = $request->input('no_of_childs');
            $checkin = $request->input('checkin');
            $checkout = $request->input('checkout');
            $remarks = $request->input('remarks');

            $CusTitle = explode(',', $request->input('cusTitle')); //;
            $CusFName = explode(',', $request->input('firstName')); //;
            $CusLName = explode(',', $request->input('lastName')); //;
            $CusType = explode(',', $request->input('type')); //;
            $TotalFair = $request->input('totalFare'); //;
            $bookingfair = $request->input('booking_total');


            // return $bookingfair;
            $finalBookingArray['BookingCode'] = $rateKey;

            $resevationFullName = $HolderFName . ' ' . $HolderlName;

            // return $CusType;
            // return $paxCount;

            if ($paxCount >= 1) {
                for (
                    $c = 0;
                    $c < $paxCount;
                    $c++
                ) {
                    $finalBookingArray['CustomerDetails']['CustomerNames'][] = ['Title' => $CusTitle[$c], 'FirstName' => $CusFName[$c], 'LastName' => $CusLName[$c], 'Type' => $CusType[$c]];
                }
            }



            // *************** Client Reference Key Generating ***************

            $cus = '';

            if (strlen($CusLName[0]) >= 4)
                $cus = strtoupper(substr($CusLName[0], 0, 4));
            else if (strlen($CusLName[0]) == 3)
                $cus = strtoupper(substr($CusLName[0], 0, 3)) . strtoupper(substr($CusLName[0], 0, 1));
            else if (strlen($CusLName[0]) == 2)
                $cus = strtoupper(substr($CusLName[0], 0, 2)) . strtoupper(substr($CusLName[0], 0, 2));
            else if (strlen($CusLName[0]) == 1 && strlen($CusFName[0]) > 1)
                $cus = strtoupper(substr($CusLName[0], 0, 1)) . strtoupper(substr($CusFName[0], 0, 1)) . strtoupper(substr($CusLName[0], 0, 1)) . strtoupper(substr($CusFName[0], 0, 1));


            $ref = substr($todayDate, 8, 2) . substr($todayDate, 5, 2) . substr($todayDate, 2, 2) . substr($todayDate, 11, 2) . substr($todayDate, 14, 2) . substr($todayDate, 17, 2) . '000#' . $cus;

            $finalBookingArray['ClientReferenceId'] = $ref;
            $finalBookingArray['BookingReferenceId'] = $ref;
            $finalBookingArray['TotalFare'] = (float)$bookingfair;
            $finalBookingArray['EmailId'] = 'apisupport@tboholidays.com';
            $finalBookingArray['PhoneNumber'] = '918448780621';
            $finalBookingArray['BookingType'] = 'Voucher';
            $finalBookingArray['PaymentMode'] = 'Limit';

            $subArray = [];
            $subArray['BookingCode'] = $finalBookingArray['BookingCode'];
            $subArray['CustomerDetails'][]['CustomerNames'] = $finalBookingArray['CustomerDetails']['CustomerNames'];
            $subArray['ClientReferenceId'] = $finalBookingArray['ClientReferenceId'];
            $subArray['BookingReferenceId'] = $finalBookingArray['BookingReferenceId'];
            $subArray['TotalFare'] = $finalBookingArray['TotalFare'];
            $subArray['EmailId'] = $finalBookingArray['EmailId'];
            $subArray['PhoneNumber'] = $finalBookingArray['PhoneNumber'];
            $subArray['BookingType'] = $finalBookingArray['BookingType'];
            $subArray['PaymentMode'] = $finalBookingArray['PaymentMode'];

            // return $subArray;

            $responseBook = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))->post($url_booking, $subArray)->json();

            // return $responseBook;

            if ($responseBook['Status']['Code'] === 405) {
                return response([
                    'status' => 405,
                    'message' => 'Booking Fail'
                ]);
            } else {

                $hotelDetailsArray = [];

                $hotelDetailsArray['Hotelcodes'] = $request['hotelCode'];
                $hotelDetailsArray['Language'] = 'en';

                $hotel_Name = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))
                    ->post('http://api.tbotechnology.in/TBOHolidays_HotelAPI/Hoteldetails', $hotelDetailsArray)->json();



                // ******** Aahaas DB saving part ********

                $BookId = $responseBook['ConfirmationNumber'];
                $ClientReference = $responseBook['ClientReferenceId'];
                $Currency = $responsePreBook['HotelResult'][0]['Currency'];
                $HotelName = $request['HotelName'];
                $RoomName = $responsePreBook['HotelResult'][0]['Rooms'][0]['Name'][0];
                $TotalFair = $responsePreBook['HotelResult'][0]['Rooms'][0]['TotalFare'];
                $CancellationDate = $responsePreBook['HotelResult'][0]['Rooms'][0]['CancelPolicies'][0]['FromDate'];
                $CancellationFair = $TotalFair;
                $MealType = $responsePreBook['HotelResult'][0]['Rooms'][0]['MealType'];

                $cancelDate = date('Y-m-d', strtotime($CancellationDate));


                $created_at = $currentTime;
                $updated_at = $currentTime;
                $user_Id = $request['userId'];

                $hotelRes = HotelResevation::create([
                    'rate_key' => $rateKey,
                    'resevation_no' => $BookId,
                    'resevation_name' => $resevationFullName,
                    'resevation_date' => $todayDate,
                    'hotel_name' => $HotelName,
                    'checkin_time' => $checkin,
                    'checkout_time' => $checkout,
                    'baby_crib' => '-',
                    'no_of_adults' => $no_of_adults,
                    'no_of_childs' => $no_of_childs,
                    'bed_type' => '-',
                    'room_type' => $RoomName,
                    'no_of_rooms' => 0,
                    'board_code' => $MealType,
                    'special_notice' => $remarks,
                    'resevation_platform' => 'HOTELTBO',
                    'resevation_status' => 'CONFIRMED',
                    'currency' => $Currency,
                    'cancelation' => '-',
                    'modification' => '-',
                    'cancelation_amount' => $CancellationFair,
                    'cancelation_deadline' => $cancelDate,
                    'booking_remarks' => $remarks,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
                    'user_id' => $user_Id
                ]);
                // return $RoomName;

                MainCheckout::create([
                    'checkout_id' => $request['oid'],
                    'essnoness_id' => null,
                    'lifestyle_id' => null,
                    'education_id' => null,
                    'hotel_id' => $hotelRes->id,
                    'flight_id' => null,
                    'main_category_id' => '4',
                    'quantity' => null,
                    'each_item_price' => null,
                    'total_price' => $TotalFair,
                    'discount_price' => 0.00,
                    'bogof_item_name' => null,
                    'delivery_charge' => null,
                    'discount_type' => null,
                    'child_rate' => '-',
                    'adult_rate' => '-',
                    'discountable_child_rate' => null,
                    'discountable_adult_rate' => null,
                    'flight_trip_type' => null,
                    'flight_total_price' => null,
                    'related_order_id' => $request['id'],
                    'currency' => $Currency,
                    'status' => 'Booked',
                    'delivery_status' => null,
                    'delivery_date' => null,
                    'delivery_address' => null,
                    'cx_id' => $user_Id,
                ]);

                HotelResevationPayment::create([
                    'resevation_no' => $BookId,
                    'total_amount' => $TotalFair,
                    'paid_amount' => 0.00,
                    'balance_payment' => $TotalFair,
                    'payment_method' => '-',
                    'payment_status' => 'Not Paid',
                    'payment_slip_image' => '-'
                ]);

                if (
                    $finalBookingArray['CustomerDetails']['CustomerNames'] >= 1
                ) {
                    foreach ($finalBookingArray['CustomerDetails']['CustomerNames'] as $pax) {

                        $fName = $pax['FirstName'];
                        $sName = $pax['LastName'];
                        HotelRoomDetails::create([
                            'resevation_no' => $BookId,
                            'room_code' => $RoomName,
                            'adult_count' => $no_of_adults,
                            'child_count' => $no_of_childs,
                            'adult_rate' => 0.00,
                            'child_withbed_rate' => 0.00,
                            'child_nobed_rate' => 0.00
                        ]);
                    }
                }

                if (
                    $finalBookingArray['CustomerDetails']['CustomerNames'] >= 1
                ) {
                    foreach ($finalBookingArray['CustomerDetails']['CustomerNames'] as $pax) {

                        $fName = $pax['FirstName'];
                        $sName = $pax['LastName'];
                        $type = $pax['Type'];
                        HotelTravellerDetail::create([
                            'resevation_no' => $BookId,
                            'first_name' => $fName,
                            'last_name' => $sName,
                            'type' => $type
                        ]);
                    }
                }

                return $this->sendEmail($BookId);
            }
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }

    // **************  Send Confirmation Email for HOTEL TBO Customers ****************
    public function sendEmail($bookConfirmId)
    {
        // return $bookConfirmId;
        $dataJoinTbo = DB::table('tbl_hotel_resevation')
            ->join('users', 'tbl_hotel_resevation.user_id', '=', 'users.id')
            ->join('tbl_hotel_resevation_payments', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_resevation_payments.resevation_no')
            ->where('tbl_hotel_resevation.resevation_no', $bookConfirmId)->first();


        // return $bookConfirmId;

        // return $dataJoinTbo;
        $dataJoinTwo = DB::table('tbl_hotel_travellerdetails')->where('tbl_hotel_travellerdetails.resevation_no', $bookConfirmId)->first();
        $dataJoinThree = DB::table('tbl_hotel_roomdetails')
            ->join('tbl_hotel_travellerdetails', 'tbl_hotel_roomdetails.resevation_no', '=', 'tbl_hotel_travellerdetails.resevation_no')
            ->where('tbl_hotel_roomdetails.resevation_no', '=', $bookConfirmId)->select('*')->get();

        // $invoice_no = $dataJoinTbo->InoiceId;
        $resevationNumber = $dataJoinTbo->resevation_no;
        $resevation_name = $dataJoinTbo->resevation_name;
        $resevation_date = $dataJoinTbo->resevation_date;
        $checkin_time = $dataJoinTbo->checkin_time;
        $checkout_time = $dataJoinTbo->checkout_time;
        $no_of_adults = $dataJoinTbo->no_of_adults;
        $no_of_childs = $dataJoinTbo->no_of_childs;
        $bed_type = $dataJoinTbo->bed_type;
        $room_type = $dataJoinTbo->room_type;
        $no_of_rooms = $dataJoinTbo->no_of_rooms;
        $board_code = $dataJoinTbo->board_code;
        $special_notice = $dataJoinTbo->special_notice;
        $currency = $dataJoinTbo->currency;
        $cancelation_deadline = $dataJoinTbo->cancelation_deadline;
        // $room_code = $dataJoinTbo->room_code;
        $net_amount = $dataJoinTbo->total_amount;
        $resevation_status = $dataJoinTbo->resevation_status;
        $hotel_name = $dataJoinTbo->hotel_name;

        $userEmail = $dataJoinTbo->email;
        $CancellationFair = $dataJoinTbo->cancelation_amount;

        // ************************ Calculating Nights ************
        $datetime1 = new \DateTime($checkin_time);
        $datetime2 = new \DateTime($checkout_time);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%a');

        $nightsCount = $days;

        $total_amount = currency($net_amount, $currency, 'USD', true);
        $cancel_amount = currency($CancellationFair, $currency, 'USD', true);

        $dataSet = [
            'resevation_no' => $resevationNumber, 'resevation_name' => $resevation_name, 'resevation_date' => $resevation_date,
            'checkin_date' => $checkin_time, 'checkout_time' => $checkout_time, 'no_of_adults' => $no_of_adults, 'no_of_childs' => $no_of_childs, 'bed_type' => $bed_type, 'room_type' => $room_type,
            'no_of_rooms' => $no_of_rooms, 'board_code' => $board_code, 'special_notice' => $special_notice, 'resevation_status' => $resevation_status,
            'cancel_dealine' => $cancelation_deadline,  'total_amount' => $total_amount, 'nights' => $nightsCount, 'hotelName' => $hotel_name, 'otherdata' => $dataJoinThree, 'cancellationAmount' => $cancel_amount,
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('Mails.HotelTboRecipt', $dataSet);

        // return view('Mails.HotelTboRecipt', $dataSet);
        try {

            Mail::send('Mails.HotelTboRecipt', $dataSet, function ($message) use ($userEmail, $resevationNumber, $pdf) {
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


        // return $dataJoinOne;
    }
    // **************  Send Confirmation Email for HOTEL TBO Customers End ****************

    // ################################################################################### //

    // ******************* Fetch Hotel Details with minimum price *******************

    public function getHotelDetailsWithMinPrice()
    {
        $todayDate = Carbon::now()->format('Y-m-d');
        $_30DaysAfterDate = date('Y-m-d', strtotime('+1 days', strtotime($todayDate)));

        $randomCodes = Arr::random($this->getAllHotelCodesTBO(), 50);
        $finalCodes = implode(',', $randomCodes);

        $hotelDetail = [];

        $hotelDetail['Hotelcodes'] = $finalCodes; //preg_replace('/\r|\n/', '', $finalCodes);

        $hotelDetail['Language'] = 'en';

        /* ------- */

        $DataArray = [];
        $URL1 = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/Hoteldetails';
        $URL2 = 'http://api.tbotechnology.in/TBOHolidays_HotelAPI/search';


        $CheckInDate = $todayDate;
        $CheckOutDate = $_30DaysAfterDate;
        $hotelCode = $finalCodes;
        $guestNationality = 'LK';
        $numOfRooms = '1';
        $numOfAdults = '1';
        $numOfChilds = '0';
        $childAges = 0;

        $DataArray['CheckIn'] = $CheckInDate;
        $DataArray['CheckOut'] = $CheckOutDate;
        $DataArray['HotelCodes'] = $finalCodes;
        $DataArray['GuestNationality'] = $guestNationality;
        $DataArray['Filters']['Refundable'] = 'false';
        $DataArray['Filters']['NoOfRooms'] = $numOfRooms;

        for ($c = 1; $c <= $DataArray['Filters']['NoOfRooms']; $c++) {
            $DataArray['PaxRooms']['Adults'] = $numOfAdults;
            $DataArray['PaxRooms']['Children'] = $numOfChilds;
        }

        $DataArray['PaxRooms']['ChildrenAges'] = [];

        $DataArray['Filters']['Refundable'] = false;
        $DataArray['Filters']['NoOfRooms'] = $numOfRooms;
        $DataArray['Filters']['MealType'] = 'All';

        $sub_array = [];

        $sub_array['CheckIn'] = $DataArray['CheckIn'];
        $sub_array['CheckOut'] = $DataArray['CheckOut'];
        $sub_array['HotelCodes'] = $DataArray['HotelCodes'];
        $sub_array['GuestNationality'] = $DataArray['GuestNationality'];
        $sub_array['PaxRooms'] = [$DataArray['PaxRooms']];
        $sub_array['Filters'] = $DataArray['Filters'];

        // return $hotelDetail;
        try {
            $response1 = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))->post($URL1, $hotelDetail)->json();

            $response2 = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))->post($URL2, $sub_array)->json();

            $newarr = array();

            // return $response2;

            // foreach ($response1['HotelDetails'] as $hotel) {

            //     foreach ($response2['HotelResult'] as $hotelDetails) {
            //         if ($hotelDetails['HotelCode'] == $hotel['HotelCode']) {
            //             $array['hotels']['rates'] = $hotelDetails['Rooms']['TotalFare'];
            //             $array['hotels']['details'] = $hotel;

            //             array_push($newarr, $array);
            //         }
            //     }
            // }


            return response()->json([
                'status_api' => 200,
                'data_fetch_r1' => $response1,
                'data_fetch_r2' => $response2,
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // ******************* Fetch Hotel Details with minimum price END *******************


    // ************* Booking Cancellation Hotel TBO **************
    public function bookingCancellationTbo(Request $request)
    {
        $BookingConfirmaionNo = $request->input('resnumber');
        $CancellationReason = $request['retcanreason'];
        $CancellationRemark = $request['orderremarks'];

        $CancelArray = [];

        $CancelArray['ConfirmationNumber'] = $BookingConfirmaionNo;

        try {

            $todayDate = Carbon::now()->format('Y-m-d');

            $table_data = DB::table('tbl_hotel_resevation')
                ->join('users', 'tbl_hotel_resevation.user_id', '=', 'users.id')
                ->select('tbl_hotel_resevation.resevation_status', 'tbl_hotel_resevation.cancelation_deadline', 'users.email')
                ->where('resevation_no', $BookingConfirmaionNo)->first();

            $cancellation_date = date('Y-m-d', strtotime($table_data->cancelation_deadline));
            $userEmail = $table_data->email;

            $response = Http::withBasicAuth(config('services.hoteltbo.username'), config('services.hoteltbo.password'))
                ->post('http://api.tbotechnology.in/TBOHolidays_HotelAPI/Cancel', $CancelArray)->json();

            if ($response['Status']['Code'] === 405) {
                return response([
                    'status' => 405,
                    'message' => 'Booking already cancelled'
                ]);
            } else {

                $status = 'CANCELLED';
                $canceledDate = $todayDate;

                DB::select(DB::raw("UPDATE tbl_hotel_resevation SET status='$status',cancellation_remarks='$CancellationReason',other_remarks='$CancellationRemark',updated_at='$todayDate' WHERE resevation_no='$BookingConfirmaionNo'"));

                $data = ['booking_id' => $BookingConfirmaionNo, 'status' => $status, 'cancel_date' => $canceledDate];

                Mail::send(
                    'Mails.BookingCancelTBO',
                    $data,
                    function ($message) use ($userEmail) {
                        $message->to($userEmail);
                        $message->subject('Booking Cancellation Confirmation');
                    }
                );

                return response()->json([
                    'status' => 200,
                    'message' => '#' . $BookingConfirmaionNo . ' booking canceled successfully'
                ]);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }



    // ************* Booking Cancellation Hotel TBO End **************

    public function emailBody()
    {
        return view('Mails.HotelTboRecipt');
    }
}
