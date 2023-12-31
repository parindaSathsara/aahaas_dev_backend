<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use App\Models\Hotel\HotelMeta\HotelMeta;
use App\Models\Hotel\HotelMeta\HotelMetaRates;
use App\Models\Hotels\HotelBeds\HotelBeds;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotelMetaController extends Controller
{
    public $hotel_rates;
    public $hotel_beds;
    public $hotel_meta;
    public $check_in;
    public $check_out;
    public $location;
    public $latitude;
    public $longitude;
    public $no_of_rooms;
    public $child_count;
    public $adult_count;
    public $room_type;
    public $child_ages;

    public function __construct()
    {
        $this->hotel_meta = new HotelMeta();
        $this->hotel_beds = new HotelBeds();
        $this->hotel_rates = new HotelMetaRates();
    }

    function getRadius($lat, $lon)
    {
        $har = "(6371 * acos(cos(radians(" . $lat . ")) 
            * cos(radians(tbl_lifestyle_inventory.latitude)) 
            * cos(radians(tbl_lifestyle_inventory.longitude) - radians(" . $lon . ")) 
            + sin(radians(" . $lat . ")) 
            * sin(radians(tbl_lifestyle_inventory.latitude))))";


        return $har;
    }

    //Fetch All Rates
    public function index($lat, $lon)
    {
        try {

            $har = "(6371 * acos(cos(radians(" . $lat . ")) 
            * cos(radians(aahaas_hotel_meta.latitude)) 
            * cos(radians(aahaas_hotel_meta.longitude) - radians(" . $lon . ")) 
            + sin(radians(" . $lat . ")) 
            * sin(radians(aahaas_hotel_meta.latitude))))";

            $query = DB::table('aahaas_hotel_meta')
                ->whereRaw("{$har} < ?", [20])
                // ->where(['aahaas_rates_meta.autoFetch' => true, 'aahaas_rates_meta.userId' => gethostbyname(gethostname())]) //'aahaas_rates_meta.fetchDate' => Carbon::now()->format('Y-m-d'),
                ->join('aahaas_rates_meta', 'aahaas_hotel_meta.id', '=', 'aahaas_rates_meta.groupId')
                ->selectRaw("{$har} AS distance")
                ->select('*')
                ->orderBy('aahaas_rates_meta.net', 'ASC')
                ->groupBy('aahaas_rates_meta.groupId')
                ->get();

            return response([
                'status' => 200,
                'data_r' => $query
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function rateDataFeed()
    {
        try {

            $this->check_in = date('Y-m-d', strtotime('+2 days', strtotime(Carbon::now()->format('Y-m-d'))));
            $this->check_out = date('Y-m-d', strtotime('+3 days', strtotime(Carbon::now()->format('Y-m-d'))));
            $this->adult_count = 2;
            $this->no_of_rooms = 1;
            $query = DB::table('aahaas_hotel_meta')->select('*')->get(); //->whereNotNull('hotelCode', 'and', 'ahs_HotelId')


            $hotelCodes = array();
            $hb_rates = array();
            $hoteNoCode = array();

            $dataset = array();
            $dataset_second = array();

            foreach ($query as $qr) {
                $hotelCodes['hb'][] = (int)$qr->hotelCode;
                $hotelCodes['ah'][] = (int)$qr->ahs_HotelId;
            }

            $aahaas = $this->getAllHotelsWithRates($hotelCodes);

            $ahsCount = count($aahaas);

            // return $ahsCount;
            $row_data = DB::table('aahaas_rates_meta')->where('userId', gethostbyname(gethostname()))->select('*')->groupBy('aahaas_rates_meta.hotelCode')->get(); //'fetchDate' => Carbon::now()->format('Y-m-d'),
            // return count($row_data);

            if (count($row_data) > 0) {
                foreach ($row_data as $row) {
                    if ($row->provider === 'hotelBeds') {
                        $beds = $this->getAllHotelBedsRates($this->check_in, $this->check_out, [(int)$row->hotelCode], $ahsCount);
                        return $beds;
                        if ($beds['hotels']['total'] == 0) {
                            $hoteNoCode[] = (int)$row->hotelCode;
                        } else {

                            foreach ($beds['hotels']['hotels'] as $bdhotels) {
                                foreach ($bdhotels['rooms'] as $rates) {
                                    foreach ($rates['rates'] as $rate) {
                                        $dataset['roomCode'] = $rates['code'];
                                        $dataset['roomName'] = $rates['name'];
                                        $dataset['roomCategory'] = null;
                                        $dataset['rateKey'] = $rate['rateKey'];
                                        $dataset['rateClass'] = $rate['rateClass'];
                                        $dataset['rateType'] = $rate['rateType'];
                                        $dataset['net'] = $rate['net'];
                                        $dataset['adultRate'] = 0.00;
                                        $dataset['childWithBedRate'] = 0.00;
                                        $dataset['childWithNoBedRate'] = 0.00;
                                        $dataset['allotment'] = $rate['allotment'];
                                        $dataset['paymentType'] = $rate['paymentType'];
                                        $dataset['packaging'] = $rate['packaging'];
                                        $dataset['boardCode'] = $rate['boardCode'];
                                        $dataset['boardName'] = $rate['boardName'];
                                        $dataset['cancellationAmount'] = $rate['cancellationPolicies'][0]['amount'];
                                        $dataset['cancellationFrom'] = $rate['cancellationPolicies'][0]['from'];
                                        $dataset['taxIncluded'] = null;
                                        $dataset['taxAmount'] = null;
                                        $dataset['taxCurrency'] = null;
                                        $dataset['clientAmount'] = null;
                                        $dataset['clientCurrency'] = null;
                                        $dataset['allIncluded'] = null;
                                        $dataset['offerCode'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['code'] : null;
                                        $dataset['offerName'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['name'] : null;
                                        $dataset['offerAmount'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['amount'] : null;
                                        $dataset['discountLimit'] = null;
                                        $dataset['minRate'] = in_array('minRate', $bdhotels['rooms']) ? $rates['minRate'] : $rate['net'];
                                        $dataset['maxRate'] = in_array('maxRate', $bdhotels['rooms']) ? $rates['maxRate'] : $rate['net'];
                                        $dataset['currency'] = in_array('currency', $bdhotels['rooms']) ? $rates['currency'] : 'EUR';
                                        $dataset['checkIn'] = $this->check_in;
                                        $dataset['checkOut'] = $this->check_out;
                                        $dataset['rooms'] = $this->no_of_rooms;
                                        $dataset['adults'] = $this->adult_count;
                                        $dataset['children'] = $this->child_count;
                                        $dataset['childrenAges'] = $this->child_ages;
                                        $dataset['rowId'] = $row->id;
                                        $dataset['hotelCode'] = $row->hotelCode;
                                        $dataset['bookingStart'] = null;
                                        $dataset['bookingEnd'] = null;
                                        $dataset['bookingDeadline'] = null;
                                        $this->hotel_rates->updateHotelData($dataset);
                                    }
                                }
                            }
                        }
                    } else {
                        foreach ($aahaas as $ahsRate) {
                            $dataset_second['roomCode'] = $ahsRate->room_type;
                            $dataset_second['roomName'] = $ahsRate->room_category;
                            $dataset_second['roomCategory'] = $ahsRate->room_category;
                            $dataset_second['rateKey'] = $ahsRate->RateID;
                            $dataset_second['rateClass'] = null;
                            $dataset_second['rateType'] = 'BOOKABLE';
                            $dataset_second['net'] = ((float)$ahsRate->adult_rate * $ahsRate->RT);
                            $dataset_second['adultRate'] = (float)$ahsRate->adult_rate;
                            $dataset_second['childWithBedRate'] = (float)$ahsRate->child_withbed_rate;
                            $dataset_second['childWithNoBedRate'] = (float)$ahsRate->child_withoutbed_rate;
                            $dataset_second['allotment'] = $ahsRate->allotment;
                            $dataset_second['paymentType'] = null;
                            $dataset_second['packaging'] = null;
                            $dataset_second['boardCode'] = $ahsRate->meal_plan;
                            $dataset_second['boardName'] = $ahsRate->meal_plan; //$ahsRate['meal_plan'] == 'BB' ? 'BED & BREAKFAST' : $ahsRate['meal_plan'] == 'FB' ? 'FULL BOARD' : $ahsRate['meal_plan'] == 'RO' ? 'ROOM ONLY' : $ahsRate['meal_plan'] == 'AI' ? 'ALL INCLUSIVE' : null;
                            $dataset_second['cancellationAmount'] = ((float)$ahsRate->adult_rate * $ahsRate->RT);
                            $dataset_second['cancellationFrom'] = $ahsRate->cancellation_days_before;
                            $dataset_second['taxIncluded'] = null;
                            $dataset_second['taxAmount'] = null;
                            $dataset_second['taxCurrency'] = null;
                            $dataset_second['clientAmount'] = null;
                            $dataset_second['clientCurrency'] = null;
                            $dataset_second['allIncluded'] = null;
                            $dataset_second['offerCode'] =  null;
                            $dataset_second['offerName'] = null;
                            $dataset_second['offerAmount'] = null;
                            $dataset_second['discountLimit'] =  $ahsRate->discount_limit;
                            $dataset_second['minRate'] = ((float)$ahsRate->adult_rate * $ahsRate->RT);
                            $dataset_second['maxRate'] = 0.00;
                            $dataset_second['currency'] = $ahsRate->currency;
                            $dataset_second['checkIn'] = $this->check_in;
                            $dataset_second['checkOut'] = $this->check_out;
                            $dataset_second['rooms'] = $this->no_of_rooms;
                            $dataset_second['adults'] = $this->adult_count;
                            $dataset_second['children'] = $this->child_count;
                            $dataset_second['childrenAges'] = $this->child_ages;
                            $dataset_second['rowId'] = $row->id;
                            $dataset_second['hotelCode'] = $row->hotelCode;
                            $dataset_second['bookingStart'] = $ahsRate->booking_startdate;
                            $dataset_second['bookingEnd'] = $ahsRate->booking_enddate;
                            $dataset_second['bookingDeadline'] = $ahsRate->book_by_days;
                            $this->hotel_rates->updateHotelData($dataset_second);
                        }
                    }
                }
            } else {

                foreach ($aahaas as $ahsRate) {
                    $groupId = DB::table('aahaas_hotel_meta')->where('ahs_HotelId', $ahsRate->ahs_HotelId)->first();

                    $dataset_second['code'] = $ahsRate->ahs_HotelId;
                    $dataset_second['name'] = $ahsRate->hotelName;
                    $dataset_second['roomCode'] = $ahsRate->room_type;
                    $dataset_second['roomName'] = $ahsRate->room_category;
                    $dataset_second['roomCategory'] = $ahsRate->room_category;
                    $dataset_second['rateKey'] = $ahsRate->RateID;;
                    $dataset_second['rateClass'] = null;
                    $dataset_second['rateType'] = 'BOOKABLE';
                    $dataset_second['net'] = ((float)$ahsRate->adult_rate * $ahsRate->RT);
                    $dataset_second['adultRate'] = (float)$ahsRate->adult_rate;
                    $dataset_second['childWithBedRate'] = (float)$ahsRate->child_withbed_rate;
                    $dataset_second['childWithNoBedRate'] = (float)$ahsRate->child_withoutbed_rate;
                    $dataset_second['allotment'] = $ahsRate->allotment;
                    $dataset_second['paymentType'] = null;
                    $dataset_second['packaging'] = null;
                    $dataset_second['boardCode'] = $ahsRate->meal_plan;
                    $dataset_second['boardName'] = $ahsRate->meal_plan; //$ahsRate['meal_plan'] == 'BB' ? 'BED & BREAKFAST' : $ahsRate['meal_plan'] == 'FB' ? 'FULL BOARD' : $ahsRate['meal_plan'] == 'RO' ? 'ROOM ONLY' : $ahsRate['meal_plan'] == 'AI' ? 'ALL INCLUSIVE' : null;
                    $dataset_second['cancellationAmount'] = ((float)$ahsRate->adult_rate * $ahsRate->RT);
                    $dataset_second['cancellationFrom'] = $ahsRate->cancellation_days_before;
                    $dataset_second['taxIncluded'] = null;
                    $dataset_second['taxAmount'] = null;
                    $dataset_second['taxCurrency'] = null;
                    $dataset_second['clientAmount'] = null;
                    $dataset_second['clientCurrency'] = null;
                    $dataset_second['allIncluded'] = null;
                    $dataset_second['offerCode'] =  null;
                    $dataset_second['offerName'] = null;
                    $dataset_second['offerAmount'] = null;
                    $dataset_second['discountLimit'] =  $ahsRate->discount_limit;
                    $dataset_second['minRate'] = ((float)$ahsRate->adult_rate * $ahsRate->RT);
                    $dataset_second['maxRate'] = 0.00;
                    $dataset_second['currency'] = $ahsRate->currency;
                    $dataset_second['checkIn'] = $this->check_in;
                    $dataset_second['checkOut'] = $this->check_out;
                    $dataset_second['rooms'] = $this->no_of_rooms;
                    $dataset_second['adults'] = $this->adult_count;
                    $dataset_second['children'] = $this->child_count;
                    $dataset_second['childrenAges'] = $this->child_ages;
                    $dataset_second['sortCriteria'] = null;
                    $dataset_second['source'] = 'hotelAhs';
                    $dataset_second['userId'] = gethostbyname(gethostname());
                    $dataset_second['autoFetch'] = true;
                    $dataset_second['groupId'] = $groupId->id;
                    $dataset_second['provider'] = 'hotelAhs';
                    $dataset_second['bookingStart'] = $ahsRate->booking_startdate;
                    $dataset_second['bookingEnd'] = $ahsRate->booking_enddate;
                    $dataset_second['bookingDeadline'] = $ahsRate->book_by_days;
                    $this->hotel_rates->createHotelRates($dataset_second);
                }

                $beds = $this->getAllHotelBedsRates($this->check_in, $this->check_out, $hotelCodes, $ahsCount);
                foreach ($beds['hotels']['hotels'] as $bdhotels) {
                    $groupId = DB::table('aahaas_hotel_meta')->where('hotelCode', $bdhotels['code'])->first();
                    foreach ($bdhotels['rooms'] as $rates) {
                        foreach ($rates['rates'] as $rate) {
                            // $hb_rates[] = $rate['net'];
                            $dataset['code'] = $bdhotels['code'];
                            $dataset['name'] = $bdhotels['name'];
                            $dataset['roomCode'] = $rates['code'];
                            $dataset['roomName'] = $rates['name'];
                            $dataset['roomCategory'] = null;
                            $dataset['rateKey'] = $rate['rateKey'];
                            $dataset['rateClass'] = $rate['rateClass'];
                            $dataset['rateType'] = $rate['rateType'];
                            $dataset['net'] = $rate['net'];
                            $dataset['adultRate'] = 0.00;
                            $dataset['childWithBedRate'] = 0.00;
                            $dataset['childWithNoBedRate'] = 0.00;
                            $dataset['allotment'] = $rate['allotment'];
                            $dataset['paymentType'] = $rate['paymentType'];
                            $dataset['packaging'] = $rate['packaging'];
                            $dataset['boardCode'] = $rate['boardCode'];
                            $dataset['boardName'] = $rate['boardName'];
                            $dataset['cancellationAmount'] = $rate['cancellationPolicies'][0]['amount'];
                            $dataset['cancellationFrom'] = $rate['cancellationPolicies'][0]['from'];
                            $dataset['taxIncluded'] = null;
                            $dataset['taxAmount'] = null;
                            $dataset['taxCurrency'] = null;
                            $dataset['clientAmount'] = null;
                            $dataset['clientCurrency'] = null;
                            $dataset['allIncluded'] = null;
                            $dataset['offerCode'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['code'] : null;
                            $dataset['offerName'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['name'] : null;
                            $dataset['offerAmount'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['amount'] : null;
                            $dataset['discountLimit'] = null;
                            $dataset['minRate'] = in_array('minRate', $bdhotels['rooms']) ? $rates['minRate'] : $rate['net'];
                            $dataset['maxRate'] = in_array('maxRate', $bdhotels['rooms']) ? $rates['maxRate'] : $rate['net'];
                            $dataset['currency'] = in_array('currency', $bdhotels['rooms']) ? $rates['currency'] : 'EUR';
                            $dataset['checkIn'] = $this->check_in;
                            $dataset['checkOut'] = $this->check_out;
                            $dataset['rooms'] = $this->no_of_rooms;
                            $dataset['adults'] = $this->adult_count;
                            $dataset['children'] = $this->child_count;
                            $dataset['childrenAges'] = $this->child_ages;
                            $dataset['sortCriteria'] = null;
                            $dataset['source'] = 'hotelBeds';
                            $dataset['userId'] = gethostbyname(gethostname());
                            $dataset['autoFetch'] = true;
                            $dataset['groupId'] = $groupId->id;
                            $dataset['provider'] = 'hotelBeds';
                            $dataset['bookingStart'] = null;
                            $dataset['bookingEnd'] = null;
                            $dataset['bookingDeadline'] = null;
                            $this->hotel_rates->createHotelRates($dataset);
                        }
                    }
                }
            }

            return 'Success';
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //GET ALL HOTELS FROM HOTEL META WITH RATES
    public function getAllHotelsWithRates($hotelCodes)
    {
        try {

            $SqlQuery = DB::table('aahaas_hotel_meta')->whereIn('aahaas_hotel_meta.ahs_HotelId', $hotelCodes['ah'])
                ->join('tbl_hotel_room_rate', 'aahaas_hotel_meta.ahs_HotelId', '=', 'tbl_hotel_room_rate.hotel_id')
                ->join('tbl_hotel_terms_conditions', 'aahaas_hotel_meta.ahs_HotelId', 'tbl_hotel_terms_conditions.hotel_id')
                ->join('tbl_hotel_inventory', 'aahaas_hotel_meta.ahs_HotelId', '=', 'tbl_hotel_inventory.hotel_id')
                ->join('tbl_hotel_discount', 'aahaas_hotel_meta.ahs_HotelId', '=', 'tbl_hotel_discount.hotel_id')
                ->select(
                    'aahaas_hotel_meta.*',
                    'aahaas_hotel_meta.ahs_HotelId AS HotelIDHOTEL',
                    'tbl_hotel_room_rate.*',
                    'tbl_hotel_terms_conditions.*',
                    'tbl_hotel_inventory.*',
                    'tbl_hotel_discount.*',
                    'tbl_hotel_room_rate.room_type AS RT',
                    'tbl_hotel_room_rate.id AS RateID'
                )
                ->orderBy('tbl_hotel_room_rate.adult_rate', 'ASC')
                ->groupBy('tbl_hotel_room_rate.hotel_id')
                ->get();

            return $SqlQuery;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //Hotel Beds wise rates
    public function getAllHotelBedsRates($checkin, $checkout, $hotelCodes, $ahsCount)
    {
        try {

            $noOfRooms = 1;

            $noOfAdults = 2;

            $noOfChilds = 0;

            $childAges = '';

            $ReqHotelCode = array_slice($hotelCodes, 0, 10);

            // return $ReqHotelCode;

            return $this->hotel_beds->checkAvailabilityHotelBeds($checkin, $checkout, $noOfRooms, $noOfAdults, $noOfChilds, $childAges, $ReqHotelCode);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //Hotel Beds wise rates
    public function getAllHotelBedsRatesSec($checkin, $checkout, $hotelCodes, $ahsCount)
    {
        try {

            $noOfRooms = 1;

            $noOfAdults = 2;

            $noOfChilds = 0;

            $childAges = '';

            return  $hotelCodes;

            $ReqHotelCode = array_slice($hotelCodes->hb, 0, $ahsCount);

            // return $ReqHotelCode;

            return $this->hotel_beds->checkAvailabilityHotelBeds($checkin, $checkout, $noOfRooms, $noOfAdults, $noOfChilds, $childAges, $ReqHotelCode);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //Fetch Rates for each hotel
    public function fetchRatesForEachHotel($id)
    {
        try {

            $response = $this->hotel_rates->fetchRatesForHotels($id);

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //search hotel based on latitude longitude
    public function getHotelsByLatLon(Request $request)
    {
        try {
            $dataset = array();
            $hotel_id = array();

            // return $request;

            $this->check_in = $request['check_in'];
            $this->check_out = $request['check_out'];
            $this->no_of_rooms = $request['rooms'];
            $this->adult_count = $request['adults'];
            $this->child_count = $request['childs'];
            $this->latitude = $request['latitude'];
            $this->longitude = $request['longitude'];
            // $State = $request['state'];
            $this->child_ages = $request['ages'];

            $response = $this->hotel_rates->fetchHotelByLatLon($this->check_in, $this->check_out, $this->no_of_rooms, $this->adult_count, $this->child_count, $this->latitude, $this->longitude, $this->child_ages);

            // return $response;

            if ($response['hotels']['total'] == 0) {

                return response([
                    'status' => 400,
                    'data_r' => 'No Hotels avaialable'
                ]);
            } else {
                foreach ($response['hotels']['hotels'] as $res) {
                    $hotel_id[] = $res['code'];
                    //checking whether hotel available in local or not
                    $query = DB::table('aahaas_hotel_meta')->where('hotelCode', $res['code'])->select('*')->get();

                    if (count($query) > 0) {
                        //checking the specific hotel has rates available in rates table
                        $q2 = DB::table('aahaas_rates_meta')->where(['groupId' => $query[0]->id, 'userId' => gethostbyname(gethostname()), 'autoFetch' => false])->select('*')->get();

                        if (count($q2) > 0) {
                            DB::table('aahaas_rates_meta')->where('groupId', $query[0]->id)->delete();

                            foreach ($res['rooms'] as $rates) {
                                foreach ($rates['rates'] as $rate) {
                                    // $hb_rates[] = $rate['net'];
                                    $dataset['code'] = $res['code'];
                                    $dataset['name'] = $res['name'];
                                    $dataset['roomCode'] = $rates['code'];
                                    $dataset['roomName'] = $rates['name'];
                                    $dataset['roomCategory'] = null;
                                    $dataset['rateKey'] = $rate['rateKey'];
                                    $dataset['rateClass'] = $rate['rateClass'];
                                    $dataset['rateType'] = $rate['rateType'];
                                    $dataset['net'] = $rate['net'];
                                    $dataset['adultRate'] = 0.00;
                                    $dataset['childWithBedRate'] = 0.00;
                                    $dataset['childWithNoBedRate'] = 0.00;
                                    $dataset['allotment'] = $rate['allotment'];
                                    $dataset['paymentType'] = $rate['paymentType'];
                                    $dataset['packaging'] = $rate['packaging'];
                                    $dataset['boardCode'] = $rate['boardCode'];
                                    $dataset['boardName'] = $rate['boardName'];
                                    $dataset['cancellationAmount'] = $rate['cancellationPolicies'][0]['amount'];
                                    $dataset['cancellationFrom'] = $rate['cancellationPolicies'][0]['amount'];
                                    $dataset['taxIncluded'] = null;
                                    $dataset['taxAmount'] = null;
                                    $dataset['taxCurrency'] = null;
                                    $dataset['clientAmount'] = null;
                                    $dataset['clientCurrency'] = null;
                                    $dataset['allIncluded'] = null;
                                    $dataset['offerCode'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['code'] : null;
                                    $dataset['offerName'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['name'] : null;
                                    $dataset['offerAmount'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['amount'] : null;
                                    $dataset['discountLimit'] = null;
                                    $dataset['minRate'] = in_array('minRate', $res['rooms']) ? $rates['minRate'] : $rate['net'];
                                    $dataset['maxRate'] = in_array('maxRate', $res['rooms']) ? $rates['maxRate'] : $rate['net'];
                                    $dataset['currency'] = in_array('currency', $res['rooms']) ? $rates['currency'] : 'EUR';
                                    $dataset['checkIn'] = $this->check_in;
                                    $dataset['checkOut'] = $this->check_out;
                                    $dataset['rooms'] = $this->no_of_rooms;
                                    $dataset['adults'] = $this->adult_count;
                                    $dataset['children'] = $this->child_count;
                                    $dataset['childrenAges'] = $this->child_ages;
                                    $dataset['sortCriteria'] = null;
                                    $dataset['source'] = 'hotelBeds';
                                    $dataset['userId'] = gethostbyname(gethostname());
                                    $dataset['autoFetch'] = false;
                                    $dataset['groupId'] = $query[0]->id;
                                    $dataset['provider'] = 'hotelBeds';
                                    $this->hotel_rates->createHotelRates($dataset);
                                }
                            }
                        } else {
                            foreach ($res['rooms'] as $rates) {
                                foreach ($rates['rates'] as $rate) {
                                    // $hb_rates[] = $rate['net'];
                                    $dataset['code'] = $res['code'];
                                    $dataset['name'] = $res['name'];
                                    $dataset['roomCode'] = $rates['code'];
                                    $dataset['roomName'] = $rates['name'];
                                    $dataset['roomCategory'] = null;
                                    $dataset['rateKey'] = $rate['rateKey'];
                                    $dataset['rateClass'] = $rate['rateClass'];
                                    $dataset['rateType'] = $rate['rateType'];
                                    $dataset['net'] = $rate['net'];
                                    $dataset['adultRate'] = 0.00;
                                    $dataset['childWithBedRate'] = 0.00;
                                    $dataset['childWithNoBedRate'] = 0.00;
                                    $dataset['allotment'] = $rate['allotment'];
                                    $dataset['paymentType'] = $rate['paymentType'];
                                    $dataset['packaging'] = $rate['packaging'];
                                    $dataset['boardCode'] = $rate['boardCode'];
                                    $dataset['boardName'] = $rate['boardName'];
                                    $dataset['cancellationAmount'] = $rate['cancellationPolicies'][0]['amount'];
                                    $dataset['cancellationFrom'] = $rate['cancellationPolicies'][0]['amount'];
                                    $dataset['taxIncluded'] = null;
                                    $dataset['taxAmount'] = null;
                                    $dataset['taxCurrency'] = null;
                                    $dataset['clientAmount'] = null;
                                    $dataset['clientCurrency'] = null;
                                    $dataset['allIncluded'] = null;
                                    $dataset['offerCode'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['code'] : null;
                                    $dataset['offerName'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['name'] : null;
                                    $dataset['offerAmount'] = in_array('offers', $rates['rates']) ? $rate['offers'][0]['amount'] : null;
                                    $dataset['discountLimit'] = null;
                                    $dataset['minRate'] = in_array('minRate', $res['rooms']) ? $rates['minRate'] : $rate['net'];
                                    $dataset['maxRate'] = in_array('maxRate', $res['rooms']) ? $rates['maxRate'] : $rate['net'];
                                    $dataset['currency'] = in_array('currency', $res['rooms']) ? $rates['currency'] : 'EUR';
                                    $dataset['checkIn'] = $this->check_in;
                                    $dataset['checkOut'] = $this->check_out;
                                    $dataset['rooms'] = $this->no_of_rooms;
                                    $dataset['adults'] = $this->adult_count;
                                    $dataset['children'] = $this->child_count;
                                    $dataset['childrenAges'] = $this->child_ages;
                                    $dataset['sortCriteria'] = null;
                                    $dataset['source'] = 'hotelBeds';
                                    $dataset['userId'] = gethostbyname(gethostname());
                                    $dataset['autoFetch'] = false;
                                    $dataset['groupId'] = $query[0]->id;
                                    $dataset['provider'] = 'hotelBeds';
                                    $this->hotel_rates->createHotelRates($dataset);
                                }
                            }
                        }
                    }
                }

                $queryF = DB::table('aahaas_hotel_meta')->whereIn('aahaas_hotel_meta.hotelCode', $hotel_id)
                    ->where(
                        [
                            'aahaas_rates_meta.fetchDate' => Carbon::now()->format('Y-m-d'),
                            'aahaas_rates_meta.autoFetch' => false,
                            'aahaas_rates_meta.userId' => gethostbyname(gethostname()),
                            'aahaas_rates_meta.checkIn' => $this->check_in,
                            'aahaas_rates_meta.checkOut' => $this->check_out
                        ]
                    )
                    ->join('aahaas_rates_meta', 'aahaas_hotel_meta.id', '=', 'aahaas_rates_meta.groupId')
                    ->select('*')
                    ->orderBy('aahaas_rates_meta.net', 'ASC')
                    ->groupBy('aahaas_rates_meta.groupId')
                    ->get();

                return response([
                    'status' => 200,
                    'data_r' => $queryF
                ]);
            }

            // return $response;

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
