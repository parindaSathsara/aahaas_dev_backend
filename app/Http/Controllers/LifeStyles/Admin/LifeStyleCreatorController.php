<?php

namespace App\Http\Controllers\LifeStyles\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lifestyle\LifeStyle;
use App\Models\Lifestyle\LifeStyleDetails;
use App\Models\Lifestyle\LifeStyleInventory;
use App\Models\Lifestyle\LifeStyleRates;
use Illuminate\Http\Request;

class LifeStyleCreatorController extends Controller
{
    public $lifestyle;
    public $lifestyledetails;
    public $lifestyleinventories;

    public function __construct()
    {
        $this->lifestyle = new LifeStyle();
        $this->lifestyledetails = new LifeStyleDetails();
        $this->lifestyleinventories = new LifeStyleInventory();
    }






    public function createNewLifeStyleRate(Request $request)
    {
        try {

            LifeStyleRates::create([
                'lifestyle_id' => $request['lifestyle_id'],
                'lifestyle_inventory_id' => $request['lifestyle_inventory_id'],
                'booking_start_date' => $request['booking_start_date'],
                'booking_end_date' => $request['booking_end_date'],
                'travel_start_date' => $request['travel_start_date'],
                'travel_end_date' => $request['travel_end_date'],
                'attraction_category' => $request['attraction_category'],
                'meal_plan' => $request['meal_plan'],
                'market' => $request['market'],
                'currency' => $request['currency'],
                'adult_rate' => $request['adult_rate'],
                'child_rate' => $request['child_rate'],
                'student_rate' => $request['student_rate'],
                'senior_rate' => $request['senior_rate'],
                'military_rate' => $request['military_rate'],
                'other_rate' => $request['other_rate'],
                'child_foc_age' => $request['child_foc_age'],
                'child_age' => $request['child_age'],
                'adult_age' => $request['adult_age'],
                'cwb_age' => $request['cwb_age'],
                'cnb_age' => $request['cnb_age'],
                'adult_age' => $request['adult_age'],
                'payment_policy' => $request['payment_policy'],
                'book_by_days' => $request['book_by_days'],
                'cancellation_days' => $request['cancellation_days'],
                'cancel_policy' => $request['cancel_policy'],
                'stop_sales_Dates' => $request['stop_sales_Dates'],
                'blackout_days' => $request['blackout_days'],
                'blackout_dates' => $request['blackout_dates'],
            ]);



            return response([
                'status' => 200,
                'data_response' => 'Data created'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }



    //create a new life style
    public function createLifeStyle(Request $request)
    {
        try {

            $City = $request['city'];
            $Name = $request['name'];
            $AttractionType = $request['attraction_type'];
            $Description = $request['description'];
            $Latitude = $request['latitude'];
            $Longitude = $request['longitude'];
            $Address = $request['address'];
            $MicroLocation = $request['micro_location'];
            $TripAd = $request['trip_advisor'];
            $StartDate = $request['start_date'];
            $EndDate = $request['end_date'];
            $SubCategory1 = $request['subcategory1'];
            $SubCategory2 = $request['subcategory2'];
            $SubCategory3 = $request['subcategory3'];

            $images = array();

            if ($request->has('gallery_images')) {
                $gallery_images = $request->file('gallery_images');

                foreach ($gallery_images as $galImage) {
                    $image_name = $galImage->getClientOriginalName();
                    $extension = $galImage->getClientOriginalExtension();
                    $full_name = $image_name . '.' . $extension;
                    $path = 'upload/lifestyleproductimages/';
                    $url = $path . $image_name;
                    $galImage->move($path, $image_name);
                    $images[] = $url;
                }
            }

            $response = $this->lifestyle->createNewLifeStyleData($City, $AttractionType, $Name, $Description, $Latitude, $Longitude, $Address, $MicroLocation, $TripAd, $StartDate, $EndDate, $images, $SubCategory1, $SubCategory2, $SubCategory3);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //get main categories
    public function getSubCategories()
    {
        $response = $this->lifestyle->getMainCategories();

        return $response;
    }

    //getting life style data details
    public function getLifeStyleDetailsData()
    {
        $response = $this->lifestyledetails->fetchLifeStyleDetails();

        return $response;
    }

    //create life style detail data row
    public function createLifeStyleDetails(Request $request)
    {
        try {

            $LifeStyle = $request['lifestyle_id'];
            $Entrance = $request['entrance'];
            $Guide = $request['guide'];
            $Meal = $request['meal'];
            $MealTrans = $request['meal_trans'];
            $CovidTrans = $request['covid_trans'];
            $OperatingDates = $request['operating_dates'];
            $OperatingDays = $request['operating_days'];
            $OpeningTime = $request['opening_time'];
            $ClosingTime = $request['closing_time'];
            $ClosedDays = $request['closed_days'];
            $ClosedDates = $request['closed_dates'];
            $Vendor = $request['vendor'];

            $response = $this->lifestyledetails->createLifeStyleDetailRow($LifeStyle, $Entrance, $Guide, $Meal, $MealTrans, $CovidTrans, $OperatingDates, $OperatingDays, $OpeningTime, $ClosingTime, $ClosedDays, $ClosedDates, $Vendor);

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //creating life style inventory part
    public function createNewLifeStyleInventory(Request $request)
    {
        try {
            $LifeStyle = $request['lifestyle_id'];
            $City = $request['city'];
            $InventoryDate = $request['inventory_date'];
            $StartTime = $request['start_time'];
            $EndTime = $request['end_time'];
            $MaxAdult = $request['max_adult'];
            $MaxChild = $request['max_child'];
            $MaxTotal = $request['max_total'];
            $Allotments = $request['allotments'];
            $Used = $request['used'];
            $Balance = $request['balance'];
            $VehicleType = $request['vehicle_type'];
            $Inc = $request['inclusion'];
            $Exc = $request['exclusions'];
            $Lat = $request['lat'];
            $Long = $request['long'];
            $Vendor = $request['vendor'];

            $response = $this->lifestyleinventories->createLifeStyleNewInventory($LifeStyle, $City, $InventoryDate, $StartTime, $EndTime, $MaxAdult, $MaxChild, $MaxTotal, $Allotments, $Used, $Balance, $VehicleType, $Inc, $Exc, $Lat, $Long, $Vendor);

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
