<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery\DeliveryRate;
use Illuminate\Http\Request;

class DeliveryRateController extends Controller
{
    public $delivery_rate;

    public function __construct()
    {
        $this->delivery_rate = new DeliveryRate();
    }

    //get all the delivery rates
    public function getAllDeliveryRates()
    {
        try {

            $response = $this->delivery_rate->getAllDeliveryDetails();

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    //create a new delivery rate
    public function createNewDeliveryRate(Request $request)
    {
        try {

            $delivery_type = $request['delivery_type'];
            $country = $request['country'];
            $state = $request['state'];
            $city = $request['city'];
            $date_from = $request['date_from'];
            $date_to = $request['date_to'];
            $kmrange_start = $request['kmrange_start'];
            $kmrange_end = $request['kmrange_end'];
            $charge = $request['charge'];
            $currency = $request['currency'];

            $response = $this->delivery_rate->createNewDeliveryRate($delivery_type, $country, $state, $city, $date_from, $date_to, $kmrange_start, $kmrange_end, $charge, $currency);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //update delivery rates
    public function updateDeliveryRate(Request $request)
    {
        try {

            $id = $request['id'];
            $newRate = $request['rate'];

            $response = $this->delivery_rate->modifyDeliveryRateData($id, $newRate);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
