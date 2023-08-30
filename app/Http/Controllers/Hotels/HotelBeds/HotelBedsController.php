<?php

namespace App\Http\Controllers\Hotels\HotelBeds;

use App\Http\Controllers\Controller;
use App\Models\Customer\MainCheckout;
use App\Models\Hotels\HotelBeds\HotelBeds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Hotels\HotelResevation;
use App\Models\Hotels\HotelRoomDetails;
use App\Models\Hotels\HotelResevationPayment;
use App\Models\Hotels\HotelsPreBookings;
use App\Models\Hotels\HotelTravellerDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;

class HotelBedsController extends Controller
{

    public $hotel_beds;

    public function __construct()
    {
        set_time_limit(0);
        $this->hotel_beds = new HotelBeds();
    }


    /***** Generating X-Signature AND API Headers code for API call *****/
    function getSignature()
    {
        $api_key = config('services.hotelbed.key');
        $secret_key = config('services.hotelbed.secret');
        $current_timestamp = Carbon::now()->timestamp;
        $signature = $api_key . $secret_key . $current_timestamp;

        $x_sig = hash('sha256', $signature, true);

        $test_key = bin2hex($x_sig);

        return $test_key;
    }

    function getHeader()
    {
        $Header = [];

        $Header['Accept'] = 'application/json';
        $Header['Api-key'] = config('services.hotelbed.key');
        $Header['X-Signature'] = $this->getSignature();
        $Header['Content-Type'] = 'application/json';

        return $Header;
    }
    /***** Generating X-Signature AND API Headers code for API call END *****/

    /***** Checking Hotel beds API hotel Availability *****/
    public function checkAvailability(Request $request)
    {
        // return $request;

        try {
            $MainArray = [];

            $checkInDate = date('Y-m-d', strtotime($request['checkInDate']));

            $checkOutDate = date('Y-m-d', strtotime($request['checkOutDate']));

            $MainArray['stay']['checkIn'] = $checkInDate;

            $MainArray['stay']['checkOut'] = $checkOutDate;

            $noOfRooms = $request['num_of_rooms'];

            $noOfAdults = $request['no_of_adults'];

            $noOfChilds = $request['no_of_childs'];

            $childAges = $request['childAges'];

            $ReqHotelCode = (int)$request['hotelCode'];

            $response = $this->hotel_beds->checkAvailabilityHotelBeds($checkInDate, $checkOutDate, $noOfRooms, $noOfAdults, $noOfChilds, $childAges, $ReqHotelCode);

            return $response;
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
    /***** Checking Hotel beds API hotel Availability END *****/

    /***** Getting Hotels with Minimum Price *****/
    public function getHotelListMinPriceHotelBeds()
    {
        $response = $this->hotel_beds->getHotelBedsMinPriceHotels();

        return $response;
    }
    /***** Getting Hotels with Minimum Price END *****/

    /***** Getting Hotels with Minimum Price *****/
    public function getHotelListMinPriceHotelBedsVersion2()
    {
        $response = $this->hotel_beds->getHotelBedsMinPriceHotelsV1();

        return $response;
    }
    /***** Getting Hotels with Minimum Price END *****/

    /***** Fetching hotel details from hotel beds API *****/
    public function getHotelDetails(Request $request)
    {

        $DestinationCode = $request['destcode'];

        $response = $this->hotel_beds->getHotelBedsDetails($DestinationCode);

        return $response;
    }

    /***** Fetching country list from hotel beds API *****/
    public function getCountryList(Request $request)
    {

        $FromDate = $request['fromDate'];
        $ToDate = $request['toDate'];

        $response = $this->hotel_beds->getHotelBedsCountries($FromDate, $ToDate);


        return $response;
    }
    /***** Fetching country list from hotel beds API END *****/

    /***** Fetching destinations list from hotel beds API *****/
    public function getDestinationList(Request $request)
    {
        try {
            $FromDate = $request['fromDate'];
            $ToDate = $request['toDate'];
            $CountryCodes = $request['countryCodes'];

            $response = $this->hotel_beds->getHotelBedsDestinations($FromDate, $ToDate, $CountryCodes);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /***** Fetching destinations list from hotel beds API END *****/

    /***** Get Hotel By ID *****/
    public function getHotelByIdHotelBeds($id)
    {

        try {
            $response = $this->hotel_beds->getHotelBedsHotelById($id);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /***** Get Hotel By ID End *****/

    /***** Filter Hotel by Geo Location *****/
    public function filterHotelsByGeoLocation(Request $request)
    {
        try {

            $CheckInDate = $request['checkInDate'];
            $CheckOutDate = $request['checkOutDate'];
            $RoomCount = $request['roomCount'];
            $AdultCount = $request['adultCount'];
            $ChildCount = $request['childCount'];
            $Latitude = $request['latitude'];
            $Longitude = $request['longitude'];

            $response = $this->hotel_beds->filterHotelBedsHotelByGeoLoc($CheckInDate, $CheckOutDate, $RoomCount, $AdultCount, $ChildCount, $Latitude, $Longitude);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /***** Filter Hotel by Geo Location End *****/

    /***** Filter Hotel by Board Type *****/
    public function getHotelByBoardCode(Request $request)
    {
        try {

            $CheckInDate = $request['checkInDate'];
            $CheckOutDate = $request['checkOutDate'];
            $RoomCount = $request['roomCount'];
            $AdultCount = $request['adultCount'];
            $ChildCount = $request['childCount'];
            $BoardCode = $request['boardCodes'];

            $response = $this->hotel_beds->getHotelBedsHotelByBoardCode($CheckInDate, $CheckOutDate, $RoomCount, $AdultCount, $ChildCount, $BoardCode);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /***** Filter Hotel by Board Type End *****/

    /***** Filter Hotel by Hotel Code *****/
    public function getRoomAvailabilityByHotelCode(Request $request)
    {

        try {
            $CheckInDate = $request['checkInDate'];
            $CheckOutDate = $request['checkOutDate'];
            $RoomCount = $request['num_of_rooms'];
            $AdultCount = $request['no_of_adults'];
            $ChildCount = $request['no_of_childs'];
            $HotelCode = $request['hotelCode'];
            $ChildAges = $request['childAges'];

            $response = $this->hotel_beds->getHotelBedsRoomAvailabilityByHotelCode($CheckInDate, $CheckOutDate, $RoomCount, $AdultCount, $ChildCount, $HotelCode, $ChildAges);

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /***** Filter Hotel by Hotel Code End *****/

    /***** Booking confirmation for Hotel beds API *****/

    public function updateHotelsStatus(Request $request)
    {

        $HotelPreId = $request['hotels_pre_id'];
        $Status = $request['status'];
        $oid = $request['oid'];

        $id = $request->input('hotels_pre_id');
        $status = $request->input('status');

        $response = $this->hotel_beds->updateHotelBedsHotelStatus($HotelPreId, $Status, $oid, $request);

        return $response;
    }

    // public function confirmBooking($request, $oid)
    // {
    //     $BookingArray = [];

    //     $dateNow = Carbon::now()->toDateTimeString();

    //     $UserId = $request['user'];
    //     $rateKey = $request['rateKey'];
    //     $roomId = $request['roomId'];

    //     $holderFirstName = $request['holderFirstName'];
    //     $holderLastName = $request['holderLastName'];

    //     $Remarks = $request['remarks'];

    //     $response = $this->hotel_beds->confirmHotelBedsHotelBooking($HotelPreId, $Status, $oid, $request);

    //     return $response;

    //     $noOfPax = [];

    //     $type = $request['type'];
    //     $name = $request['name'];
    //     $surname = $request['surname'];

    //     $noOfPax['pax']['roomId'] = $roomId;
    //     $noOfPax['pax']['type'] = $type;
    //     $noOfPax['pax']['name'] = $name;
    //     $noOfPax['pax']['surname'] = $surname;

    //     $BookingArray['holder']['name'] = $holderFirstName;
    //     $BookingArray['holder']['surname'] = $holderLastName;

    //     $BookingArray['rooms']['rateKey'] = $rateKey;

    //     if (count($noOfPax['pax']['type']) > 0) {
    //         for ($c = 0; $c < count($noOfPax['pax']['type']); $c++) {

    //             // $BookingArray['rooms']['paxes'][] = ['roomId' => $roomId, 'type' => $type[$c], 'name' => $name[$c], 'surname' => $surname[$c]];
    //             $BookingArray['rooms']['paxes'][] = ['roomId' => $roomId[$c], 'type' => $type[$c], 'name' => $name[$c], 'surname' => $surname[$c]];
    //         }
    //     }

    //     $BookingArray['clientReference'] = 'IntegrationAgency';
    //     $BookingArray['remark'] = $request->input('remarks');

    //     $subBookingArray['holder'] = $BookingArray['holder'];
    //     $subBookingArray['rooms'] = [$BookingArray['rooms']];
    //     $subBookingArray['clientReference'] = $BookingArray['clientReference'];
    //     $subBookingArray['remark'] = $BookingArray['remark'];

    //     $response = Http::withHeaders($this->getHeader())
    //         ->post('https://api.test.hotelbeds.com/hotel-api/1.0/bookings', $subBookingArray)->json();


    //     // return $response;
    //     //Logging executing data executing logger
    //     Log::build([
    //         'driver' => 'single',
    //         'path' => storage_path('logs/HotelBedsAPI_Logs.log')
    //     ])->info('executing confirm booking for:' . Auth::user() . $dateNow, $response);

    //     $bookingRef = $response['booking']['reference'];

    //     // $bookingDataResponse = Http::withHeaders($this->getHeader())->get('https://api.test.hotelbeds.com/hotel-api/1.0/bookings/' . $bookingRef)->json();

    //     return $this->getBookingDetails($bookingRef, $UserId, $oid);
    // }
    /***** Booking confirmation for Hotel beds API End *****/

    /***** Get Booking Details Hotel beds API *****/
    // public function getBookingDetails($refId, $UId, $oid)
    // {
    //     $bookingID = $refId;

    //     $bookingDataResponse = Http::withHeaders($this->getHeader())->get('https://api.test.hotelbeds.com/hotel-api/1.0/bookings/' . $refId)->json();

    //     $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

    //     // ******************** Booking Reference Data *********************
    //     $bookingRef = $bookingDataResponse['booking']['reference'];
    //     $HolderFname = $bookingDataResponse['booking']['holder']['name'];
    //     $HolderLname = $bookingDataResponse['booking']['holder']['surname'];
    //     $HolderFullName = $HolderFname . ' ' . $HolderLname;
    //     $resevationDate = $bookingDataResponse['auditData']['timestamp'];
    //     $checkinTime = $bookingDataResponse['booking']['hotel']['checkIn'];
    //     $checkoutTime = $bookingDataResponse['booking']['hotel']['checkOut'];
    //     $hotelName = $bookingDataResponse['booking']['hotel']['name'];

    //     $noOfAD = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['adults'];
    //     $noOfCH = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['children'];
    //     $bedType = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['rateClass'];
    //     $room_code = $bookingDataResponse['booking']['hotel']['rooms'][0]['code'];
    //     $roomType = $bookingDataResponse['booking']['hotel']['rooms'][0]['name'];
    //     $noOfRooms = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['rooms'];
    //     $boardCode = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['boardCode'];

    //     $remarks = $bookingDataResponse['booking']['remark'];
    //     $resevationPlatform = 'HOTELBEDS';
    //     $resevationStatus = $bookingDataResponse['booking']['status'];
    //     $currency = $bookingDataResponse['booking']['hotel']['currency'];
    //     $cancelation = $bookingDataResponse['booking']['modificationPolicies']['cancellation'];
    //     $modification = $bookingDataResponse['booking']['modificationPolicies']['modification'];
    //     $cancelation_amount = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['cancellationPolicies'][0]['amount'];
    //     $cancelation_deadline = $bookingDataResponse['booking']['hotel']['rooms'][0]['rates'][0]['cancellationPolicies'][0]['from'];

    //     $totalAmount = $bookingDataResponse['booking']['totalNet'];
    //     $pendingAmount = $bookingDataResponse['booking']['pendingAmount'];

    //     // $balancePayment = $totalAmount
    //     $created_at = $currentTime;
    //     $updated_at = $currentTime;
    //     $user_Id = $UId;

    //     $hotelRes = HotelResevation::create([
    //         'resevation_no' => $bookingRef,
    //         'resevation_name' => $HolderFullName,
    //         'resevation_date' => $resevationDate,
    //         'hotel_name' => $hotelName,
    //         'checkin_time' => $checkinTime,
    //         'checkout_time' => $checkoutTime,
    //         'baby_crib' => '-',
    //         'no_of_adults' => $noOfAD,
    //         'no_of_childs' => $noOfCH,
    //         'bed_type' => $bedType,
    //         'room_type' => $roomType,
    //         'no_of_rooms' => $noOfRooms,
    //         'board_code' => $boardCode,
    //         'special_notice' => $remarks,
    //         'resevation_platform' => $resevationPlatform,
    //         'resevation_status' => $resevationStatus,
    //         'currency' => $currency,
    //         'cancelation' => $cancelation,
    //         'modification' => $modification,
    //         'cancelation_amount' => $cancelation_amount,
    //         'cancelation_deadline' => $cancelation_deadline,
    //         'booking_remarks' => $remarks,
    //         'status' => 'New',
    //         'created_at' => $created_at,
    //         'updated_at' => $updated_at,
    //         'user_id' => $user_Id
    //     ]);

    //     /*
    //     'discountable_child_rate',
    //     'discountable_adult_rate',
    //     'flight_trip_type',
    //     'flight_total_price',
    //     'related_order_id',
    //     'currency',
    //     'status',
    //     'delivery_status',
    //     'delivery_date',
    //     'delivery_address',
    //     'cx_id',
    //     */

    //     MainCheckout::create([
    //         'checkout_id' => $oid,
    //         'essnoness_id' => null,
    //         'lifestyle_id' => null,
    //         'education_id' => null,
    //         'hotel_id' => $hotelRes->id,
    //         'flight_id' => null,
    //         'main_category_id' => '4',
    //         'quantity' => null,
    //         'each_item_price' => null,
    //         'total_price' => $totalAmount,
    //         'discount_price' => 0.00,
    //         'bogof_item_name' => null,
    //         'delivery_charge' => null,
    //         'discount_type' => null,
    //         'child_rate' => '-',
    //         'adult_rate' => '-',
    //         'discountable_child_rate' => null,
    //         'discountable_adult_rate' => null,
    //         'flight_trip_type' => null,
    //         'flight_total_price' => null,
    //         'related_order_id' => $hotelRes->id,
    //         'currency' => $currency,
    //         'status' => 'Booked',
    //         'delivery_status' => null,
    //         'delivery_date' => null,
    //         'delivery_address' => null,
    //         'cx_id' => $user_Id,
    //     ]);

    //     HotelRoomDetails::create([
    //         'resevation_no' => $bookingRef,
    //         'room_code' => $room_code,
    //         'adult_count' => $noOfAD,
    //         'child_count' => $noOfCH
    //     ]);

    //     HotelResevationPayment::create([
    //         'resevation_no' => $bookingRef,
    //         'total_amount' => $totalAmount,
    //         'paid_amount' => 0.00,
    //         'balance_payment' => $totalAmount,
    //         'payment_method' => 'Card',
    //         'payment_status' => 'Paid',
    //         'payment_slip_image' => '-'
    //     ]);

    //     if ($bookingDataResponse['booking']['hotel']['rooms'][0]['paxes'] >= 1) {
    //         foreach ($bookingDataResponse['booking']['hotel']['rooms'][0]['paxes'] as $pax) {

    //             $fName = $pax['name'];
    //             $sName = $pax['surname'];
    //             $type = $pax['type'];
    //             HotelTravellerDetail::create([
    //                 'resevation_no' => $bookingRef,
    //                 'first_name' => $fName,
    //                 'last_name' => $sName,
    //                 'type' => $type
    //             ]);
    //         }
    //     }
    //     // $this->emailRecipt($bookingRef);
    //     // return $bookingRef;
    //     return $this->emailRecipt($bookingRef);
    // }
    /***** Get Booking Details Hotel beds API End *****/

    /***** Send Confirmation Email Hotel beds API *****/
    // public function emailRecipt($bookingId) //$bookingId
    // {
    //     // $currency = currency()->getUserCurrency();
    //     // return view('Mails.AahaasRecipt')->render();

    //     // $hotel_Resevation = DB::table('tbl_hotel_resevation')->where('resevation_no', $bookingId)->first();

    //     // $pdf = Pdf::loadView('pdf_view', $data);
    //     // $pdf = PDF::loadView('Mails.AahaasRecipt', $dataSet);



    //     $dataJoinOne = DB::table('tbl_hotel_resevation')
    //         ->where('tbl_hotel_resevation.resevation_no', $bookingId)
    //         ->where('tbl_hotel_resevation.status', 'New')
    //         ->leftJoin('users', 'tbl_hotel_resevation.user_id', '=', 'users.id')
    //         ->leftJoin('tbl_hotel_roomdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_roomdetails.resevation_no')
    //         ->leftJoin('tbl_hotel_resevation_payments', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_resevation_payments.resevation_no')
    //         ->leftJoin('tbl_hotel', 'tbl_hotel_resevation.hotel_name', '=', 'tbl_hotel.id')
    //         ->leftJoin('tbl_hotel_vendor', 'tbl_hotel_resevation.hotel_name', '=', 'tbl_hotel_vendor.hotel_id')
    //         ->select(
    //             'users.email',
    //             'tbl_hotel_resevation.*',
    //             'tbl_hotel_resevation.hotel_name AS ResHotelName',
    //             'tbl_hotel_resevation.id AS InoiceId',
    //             'tbl_hotel_roomdetails.*',
    //             'tbl_hotel_resevation_payments.*',
    //             'tbl_hotel.hotel_name',
    //             'tbl_hotel.hotel_address',
    //             'tbl_hotel_vendor.hotel_email'
    //         )->first();

    //     // return $dataJoinOne;

    //     $dataJoinTwo = DB::table('tbl_hotel_resevation')->where('tbl_hotel_resevation.resevation_no', '=', $bookingId)->select('*')->get();

    //     // return $dataJoinTwo;

    //     $detailJoin = DB::table('tbl_hotel_resevation')
    //         ->where('tbl_hotel_resevation.resevation_no', $bookingId)
    //         ->where('tbl_hotel_resevation.status', 'New')
    //         ->leftJoin('tbl_hotel_roomdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_roomdetails.resevation_no')
    //         ->leftJoin('tbl_hotel_mealdetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_mealdetail.resevation_no')
    //         ->leftJoin('tbl_hotel_servicedetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_servicedetail.resevation_no')
    //         ->leftJoin('tbl_hotel_travellerdetails', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_travellerdetails.resevation_no')
    //         ->select(
    //             'tbl_hotel_roomdetails.adult_count AS AdultCount',
    //             'tbl_hotel_roomdetails.child_count AS ChildCount',
    //             'tbl_hotel_mealdetail.meal_plan AS MealPlan',
    //             'tbl_hotel_mealdetail.adult_count AS MealAdult',
    //             'tbl_hotel_mealdetail.child_count AS MealChild',
    //             'tbl_hotel_mealdetail.date AS MealDate',
    //             'tbl_hotel_mealdetail.special_request AS MealSpeReq',
    //             'tbl_hotel_mealdetail.unit_price AS MealPrice',
    //             'tbl_hotel_servicedetail.service_type AS SerType',
    //             'tbl_hotel_servicedetail.unit_price AS ServicePrice',
    //             'tbl_hotel_servicedetail.child_count AS SerChildCount',
    //             'tbl_hotel_servicedetail.date AS SerDate',
    //             'tbl_hotel_servicedetail.unit_price AS SerPerPrice',
    //             'tbl_hotel_travellerdetails.type AS PaxType'
    //         )->get();

    //     // return $detailJoin;
    //     // $dataJoinThree = DB::table('tbl_hotel_roomdetails')
    //     //     ->join('tbl_hotel_travellerdetails', 'tbl_hotel_roomdetails.resevation_no', '=', 'tbl_hotel_travellerdetails.resevation_no')
    //     //     ->where('tbl_hotel_roomdetails.resevation_no', '=', $bookingId)->select('*')->get();

    //     $dataJoinThree = DB::table('tbl_hotel_resevation')
    //         ->where('tbl_hotel_resevation.resevation_no', $bookingId)
    //         ->where('tbl_hotel_resevation.status', 'New')
    //         ->leftJoin('tbl_hotel_mealdetail', 'tbl_hotel_resevation.resevation_no', '=', 'tbl_hotel_mealdetail.resevation_no')
    //         ->select('*')->get();

    //     // return $dataJoinOne->email;

    //     $userEmail = $dataJoinOne->email;

    //     $invoice_no = $dataJoinOne->InoiceId;
    //     $resevationNumber = $dataJoinOne->resevation_no;
    //     $resevation_name = $dataJoinOne->resevation_name;
    //     $resevation_date = $dataJoinOne->resevation_date;
    //     $checkin_time = $dataJoinOne->checkin_time;
    //     $checkout_time = $dataJoinOne->checkout_time;
    //     $no_of_adults = $dataJoinOne->no_of_adults;
    //     $no_of_childs = $dataJoinOne->no_of_childs;
    //     $bed_type = $dataJoinOne->bed_type;
    //     $room_type = $dataJoinOne->room_type;
    //     $no_of_rooms = $dataJoinOne->no_of_rooms;
    //     $board_code = $dataJoinOne->board_code;
    //     $special_notice = $dataJoinOne->special_notice;
    //     $currency = $dataJoinOne->currency;
    //     $cancelation_deadline = $dataJoinOne->cancelation_deadline;
    //     $room_code = $dataJoinOne->room_code;
    //     $net_amount = $dataJoinOne->total_amount;
    //     $resevation_status = $dataJoinOne->resevation_status;
    //     $hotel_name = $dataJoinOne->hotel_name;
    //     $hotelAddress = $dataJoinOne->hotel_address;
    //     $hotel_email = $dataJoinOne->hotel_email;
    //     $ResHotelName = $dataJoinOne->ResHotelName;

    //     // ************************ Calculating Nights ************
    //     $datetime1 = new \DateTime($checkin_time);
    //     $datetime2 = new \DateTime($checkout_time);
    //     $interval = $datetime1->diff($datetime2);
    //     $days = $interval->format('%a');

    //     $nightsCount = $days;

    //     $total_amount = currency($net_amount, $currency, 'USD', true);

    //     $dataSet = [
    //         'invoice_id' => $invoice_no, 'resevation_no' => $resevationNumber, 'resevation_name' => $resevation_name, 'resevation_date' => $resevation_date,
    //         'checkin_date' => $checkin_time, 'checkout_time' => $checkout_time, 'no_of_adults' => $no_of_adults, 'no_of_childs' => $no_of_childs, 'bed_type' => $bed_type, 'room_type' => $room_type,
    //         'no_of_rooms' => $no_of_rooms, 'board_code' => $board_code, 'special_notice' => $special_notice, 'resevation_status' => $resevation_status,
    //         'hotelAddress' => $hotelAddress, 'hotelEmail' => $hotel_email, 'otherData' => $detailJoin, 'meal_data' => $dataJoinThree, 'ResHotelName' => $ResHotelName,
    //         'cancel_dealine' => $cancelation_deadline, 'room_code' => $room_code, 'total_amount' => $total_amount, 'nights' => $nightsCount, 'hotelName' => $hotel_name, 'otherdata' => $dataJoinTwo,
    //         'main_data' => $dataJoinOne
    //     ];



    //     $pdf = app('dompdf.wrapper');
    //     $pdf->loadView('Mails.AahaasRecipt', $dataSet);

    //     $pdf2 = app('dompdf.wrapper');
    //     $pdf2->loadView('Mails.HotelBeds', $dataSet);
    //     // return $pdf->download('pdf_file.pdf');

    //     // return view('Mails.HotelBeds', $dataSet);
    //     try {
    //         // return "Try";
    //         Mail::send('Mails.ReciptBody', $dataSet, function ($message) use ($userEmail, $resevationNumber, $pdf, $pdf2) {
    //             $message->to($userEmail);
    //             $message->subject('Confirmation Email on your Booking Reference: #' . $resevationNumber . '.');
    //             $message->attachData($pdf->output(), $resevationNumber . '_' . 'Recipt.pdf', ['mime' => 'application/pdf',]);
    //             $message->attachData($pdf2->output(), $resevationNumber . '_' . 'Beds_Recipt.pdf', ['mime' => 'application/pdf',]);
    //         });

    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Booking Confirmed and Confirmation Mail sent your email'
    //         ]);
    //     } catch (\Exception $ex) {
    //         throw $ex;
    //     }
    // }
    /***** Send Confirmation Email Hotel beds API End *****/

    //***************** Get Booking Details By User Id ********************
    public function getBookingsById()
    {
    }

    /***** Booking Cancellation and Email Sending Hotel beds API *****/
    public function bookingCancellation(Request $request, $referenceId)
    {
        try {

            $flag = 'CANCELLATION';

            $CancellationReason = $request['retcanreason'];
            $CancellationRemark = $request['orderremarks'];
            $status = 'CANCELLED';

            $response = $this->hotel_beds->cancelHotelBedsHotelBooking($CancellationReason, $CancellationRemark, $referenceId);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //function to get destination wise hotels
    public function getHotelByDestination(Request $request)
    {
        try {
            $Latitude = $request['latitude'];
            $Longitude = $request['longitude'];
            $State = $request['state'];
            $CheckIn = $request['check_in'];
            $CheckOut = $request['check_out'];
            $Adults = $request['adults'];
            $Childs = $request['childs'];
            $Rooms = $request['rooms'];
            $Age = $request['age'];

            $response = $this->hotel_beds->fetchDestinationWiseHotels($CheckIn, $CheckOut, $Rooms, $Adults, $Childs, $Latitude, $Longitude, $Age);

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /***** Booking Cancellation and Email Sending Hotel beds API End *****/

    public function email()
    {
        return view('Mails.HotelBeds');
    }



    // *************** ############################################### *********************

    //Getting data based on currenct location
    public function availabilityBasedOnCurrentLocation($latitude, $longitude)
    {
        try {

            $response = $this->hotel_beds->fetchHotelsBedsBasedOnCurrentLocation($latitude, $longitude);

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getHotelFacilities()
    {
        $response = $this->hotel_beds->getHotelFacilities();

        return $response;
    }
}
