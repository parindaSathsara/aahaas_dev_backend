<?php

namespace App\Models\Hotel\HotelMeta;

use App\Models\Hotels\HotelBeds\HotelBeds;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;

class HotelMetaRates extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'aahaas_rates_meta';

    public $timestamps = false;
    public $hotel_beds;

    protected $fillable = [
        'hotelCode',
        'hotelName',
        'roomCode',
        'roomName',
        'roomCategory',
        'rateKey',
        'rateClass',
        'rateType',
        'net',
        'adultRate',
        'childWithBedRate',
        'childWithNoBedRate',
        'allotment',
        'paymentType',
        'packaging',
        'boardCode',
        'boardName',
        'cancellationAmount',
        'cancellationFrom',
        'taxIncluded',
        'taxAmount',
        'taxCurrency',
        'clientAmount',
        'clientCurrency',
        'allIncluded',
        'offerCode',
        'offerName',
        'offerAmount',
        'discountLimit',
        'minRate',
        'maxRate',
        'currency',
        'checkIn',
        'checkOut',
        'rooms',
        'adults',
        'children',
        'childrenAges',
        'sortCriteria',
        'source',
        'userId',
        'autoFetch',
        'fetchDate',
        'groupId',
        'provider',
        'bookingStart',
        'bookingEnd',
        'bookingDeadline',
        'createdDate'
    ];

    public function __construct()
    {
        $this->hotel_beds = new HotelBeds();
    }


    //Feeding rates according to hotels
    public function createHotelRates($dataset)
    {
        try {

            HotelMetaRates::create([
                'hotelCode' => $dataset['code'],
                'hotelName' => $dataset['name'],
                'roomCode' => $dataset['roomCode'],
                'roomName' => $dataset['roomName'],
                'roomCategory' => $dataset['roomCategory'],
                'rateKey' => $dataset['rateKey'],
                'rateClass' => $dataset['rateClass'],
                'rateType' => $dataset['rateType'],
                'net' => $dataset['net'],
                'adultRate' => $dataset['adultRate'],
                'childWithBedRate' => $dataset['childWithBedRate'],
                'childWithNoBedRate' => $dataset['childWithNoBedRate'],
                'allotment' => $dataset['allotment'],
                'paymentType' => $dataset['paymentType'],
                'packaging' => $dataset['packaging'],
                'boardCode' => $dataset['boardCode'],
                'boardName' => $dataset['boardName'],
                'cancellationAmount' => $dataset['cancellationAmount'],
                'cancellationFrom' => $dataset['cancellationFrom'],
                'taxIncluded' => $dataset['taxIncluded'],
                'taxAmount' => $dataset['taxAmount'],
                'taxCurrency' => $dataset['taxCurrency'],
                'clientAmount' => $dataset['clientAmount'],
                'clientCurrency' => $dataset['clientCurrency'],
                'allIncluded' => $dataset['allIncluded'],
                'offerCode' => $dataset['offerCode'],
                'offerName' => $dataset['offerName'],
                'offerAmount' => $dataset['offerAmount'],
                'discountLimit' => $dataset['discountLimit'],
                'minRate' => $dataset['minRate'],
                'maxRate' => $dataset['maxRate'],
                'currency' => $dataset['currency'],
                'checkIn' => $dataset['checkIn'],
                'checkOut' => $dataset['checkOut'],
                'rooms' => $dataset['rooms'],
                'adults' => $dataset['adults'],
                'children' => $dataset['children'],
                'childrenAges' => $dataset['childrenAges'],
                'sortCriteria' => $dataset['sortCriteria'],
                'source' => $dataset['source'],
                'userId' => $dataset['userId'],
                'autoFetch' => $dataset['autoFetch'],
                'fetchDate' => Carbon::now()->format('Y-m-d'),
                'groupId' => $dataset['groupId'],
                'provider' => $dataset['provider'],
                'bookingStart',
                'bookingEnd',
                'bookingDeadline',
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //fetch hotel rates for each hotel
    public function fetchRatesForHotels($id)
    {
        try {

            // return gethostbyname(gethostname());

            $query = DB::table('aahaas_rates_meta')->where('groupId', $id)
                // ->where(['aahaas_rates_meta.userId' => gethostbyname(gethostname()), 'groupId' => $id]) // 'aahaas_rates_meta.fetchDate' => Carbon::now()->format('Y-m-d'),
                ->select('*')
                ->limit(3)
                ->orderBy('aahaas_rates_meta.net', 'ASC')
                ->get();

            $booking_deadline = array();

            // foreach ($query as $qry) {
            //     if ($qry->provider == 'hotelAhs') {
            //         $data_qry = DB::table('tbl_hotel_room_rate')->where('tbl_hotel_room_rate.hotel_id')
            //     }
            // }

            return response([
                'status' => 200,
                'data_res' => $query
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //Update hotels meta dataset
    public function updateHotelData($dataset)
    {
        try {

            $query = DB::table('aahaas_rates_meta')->where('id', $dataset['rowId'])->update([
                'roomCode' => $dataset['roomCode'],
                'roomName' => $dataset['roomName'],
                'roomCategory' => $dataset['roomCategory'],
                'rateKey' => $dataset['rateKey'],
                'rateClass' => $dataset['rateClass'],
                'rateType' => $dataset['rateType'],
                'net' => $dataset['net'],
                'adultRate' => $dataset['adultRate'],
                'childWithBedRate' => $dataset['childWithBedRate'],
                'childWithNoBedRate' => $dataset['childWithNoBedRate'],
                'allotment' => $dataset['allotment'],
                'paymentType' => $dataset['paymentType'],
                'packaging' => $dataset['packaging'],
                'boardCode' => $dataset['boardCode'],
                'boardName' => $dataset['boardName'],
                'cancellationAmount' => $dataset['cancellationAmount'],
                'cancellationFrom' => $dataset['cancellationFrom'],
                'taxIncluded' => $dataset['taxIncluded'],
                'taxAmount' => $dataset['taxAmount'],
                'taxCurrency' => $dataset['taxCurrency'],
                'clientAmount' => $dataset['clientAmount'],
                'clientCurrency' => $dataset['clientCurrency'],
                'allIncluded' => $dataset['allIncluded'],
                'offerCode' => $dataset['offerCode'],
                'offerName' => $dataset['offerName'],
                'offerAmount' => $dataset['offerAmount'],
                'discountLimit' => $dataset['discountLimit'],
                'minRate' => $dataset['minRate'],
                'maxRate' => $dataset['maxRate'],
                'currency' => $dataset['currency'],
                'checkIn' => $dataset['checkIn'],
                'checkOut' => $dataset['checkOut'],
                'rooms' => $dataset['rooms'],
                'adults' => $dataset['adults'],
                'children' => $dataset['children'],
                'childrenAges' => $dataset['childrenAges'],
                'sortCriteria' => null, //in_array('currency', $bdhotels['rooms']) ? $rates['currency'] : 'EUR'; //in_array('sortCriteria', $dataset) ? $dataset['sortCriteria'] :
                'autoFetch' => true,
                'fetchDate' => Carbon::now()->format('Y-m-d'),
                'bookingStart' => $dataset['bookingStart'],
                'bookingEnd' => $dataset['bookingEnd'],
                'bookingDeadline' => $dataset['bookingDeadline'],
            ]);

            return $query;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //search hotel data by lat lon
    public function fetchHotelByLatLon($CheckIn, $CheckOut, $Rooms, $Adults, $Childs, $Latitude, $Longitude, $Age)
    {
        try {

            $api = $this->hotel_beds->fetchDestinationWiseHotels($CheckIn, $CheckOut, $Rooms, $Adults, $Childs, $Latitude, $Longitude, $Age);



            return $api;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
