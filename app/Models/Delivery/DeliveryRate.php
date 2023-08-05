<?php

namespace App\Models\Delivery;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class DeliveryRate extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_product_delivery_rates';

    protected $fillable = [
        'DeliveryType',
        'country',
        'state',
        'CitiesInclude',
        'DateFrom',
        'DateTo',
        'KMRange',
        'KMRangeEnd',
        'delivery_charge',
        'currency',
    ];

    public $timestamps = false;

    //fetch all the delivery details
    public function getAllDeliveryDetails()
    {
        try {

            $Delivery_Data = DB::table('tbl_product_delivery_rates')->select('*')->get();

            return response([
                'status' => 200,
                'data_response' => $Delivery_Data
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //create new delivery rate
    public function createNewDeliveryRate($delivery_type, $country, $state, $city, $date_from, $date_to, $kmrange_start, $kmrange_end, $charge, $currency)
    {
        try {
            DeliveryRate::create([
                'DeliveryType' => $delivery_type,
                'country' => $country,
                'state' => $state,
                'CitiesInclude' => $city,
                'DateFrom' => $date_from,
                'DateTo' => $date_to,
                'KMRange' => $kmrange_start,
                'KMRangeEnd' => $kmrange_end,
                'delivery_charge' => $charge,
                'currency' => $currency,
            ]);

            return response([
                'status' => 200,
                'data_response' => 'New delivery rate created'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //update delivery rate data row
    public function modifyDeliveryRateData($id, $value)
    {
        try {

            DB::table('tbl_product_delivery_rates')->where('delivery_rate_id', $id)->update(['delivery_charge' => $value]);

            return response([
                'status' => 200,
                'data_response' => 'Row updated'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
