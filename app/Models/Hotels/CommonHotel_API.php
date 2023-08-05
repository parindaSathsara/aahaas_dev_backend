<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Hotels\AahaasHotelMeta;

class CommonHotel_API extends Model
{
    use HasFactory;

    public $hotel_beds_apikey;
    public $hotel_beds_secretkey;
    public $hotel_tbo_username;
    public $hotel_tbo_password;
    public $aahaas_meta;

    public function __construct()
    {
        $this->hotel_beds_apikey = config('services.hotelbed.key');
        $this->hotel_beds_secretkey = config('services.hotelbed.secret');
        $this->aahaas_meta = new AahaasHotelMeta();
    }

    public function aahaasHotelPush()
    {
        try {

            $SqlQuery = DB::table('tbl_hotel')
                ->join('tbl_hotel_room_rate', 'tbl_hotel.id', '=', 'tbl_hotel_room_rate.hotel_id')
                ->join('tbl_submaincategory', 'tbl_hotel.category1', '=', 'tbl_submaincategory.id')
                ->leftJoin('tbl_hotel_terms_conditions', 'tbl_hotel.id', 'tbl_hotel_terms_conditions.hotel_id')
                ->leftJoin('tbl_hotel_inventory', 'tbl_hotel.id', '=', 'tbl_hotel_inventory.hotel_id')
                ->leftJoin('tbl_hotel_discount', 'tbl_hotel.id', '=', 'tbl_hotel_discount.hotel_id')
                ->select('tbl_hotel.*', 'tbl_hotel.id AS HotelIDHOTEL', 'tbl_hotel_room_rate.*', 'tbl_hotel_terms_conditions.*', 'tbl_hotel_inventory.*', 'tbl_hotel_discount.*', 'tbl_submaincategory.submaincat_type AS CategoryType')
                ->orderBy('tbl_hotel_room_rate.adult_rate', 'ASC')
                ->groupBy('tbl_hotel_room_rate.hotel_id')
                ->get();


            $dataArray = array();

            // return $SqlQuery;

            foreach ($SqlQuery as $dataset) {
                $RowCount = DB::table('aahaas_hotel_meta')->where('hotel_code', $dataset->HotelIDHOTEL)->count();

                $dataArray[] = $dataset;

                if ($RowCount == 0) {

                    $response =  $this->aahaas_meta->pushAahaasHotelMeta($dataset);
                } else {
                    $response =  $this->aahaas_meta->updateAahaasHotels($dataset);
                }
            }

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
