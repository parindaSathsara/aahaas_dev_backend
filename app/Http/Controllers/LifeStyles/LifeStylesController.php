<?php

namespace App\Http\Controllers\LifeStyles;

use App\Http\Controllers\Controller;
use App\Models\Lifestyle\LifeStyle;
use App\Models\Lifestyle\LifeStyleDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class LifeStylesController extends Controller
{

    public function __construct()
    {
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

    public function createNewLifeStyle(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $validator = Validator::make($request->all(), [
                'vendor_id' => 'required',
                'lifestyle_category' => 'required',
                'lifestyle_place' => 'required',
                'lifestyle_name' => 'required',
                'lifestyle_description' => 'required',
                'lifestyle_pickup_point' => 'required',
                'lifestyle_tips' => 'required',
                'lifestyles_duration' => 'required',
                'prefered' => 'required',
                'prefered_type' => 'required',
                'opening_time' => 'required',
                'closing_time' => 'required',
                'lifestyle_type' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {
                $newLifeStyle = LifeStyle::create([
                    'brandName' => $request->input('brandName'),
                    'vendor_id' => $request->input('vendor_id'),
                    'lifestyle_category' => $request->input('lifestyle_category'),
                    'lifestyle_place' => $request->input('lifestyle_place'),
                    'lifestyle_name' => $request->input('lifestyle_name'),
                    'lifestyle_description' => $request->input('lifestyle_description'),
                    'lifestyle_pickup_point' => $request->input('lifestyle_pickup_point'),
                    'lifestyle_tips' => $request->input('lifestyle_tips'),
                    'lifestyle_duration' => $request->input('lifestyles_duration'),
                    'prefered' => $request->input('prefered'),
                    'prefered_type' => $request->input('prefered_type'),
                    'opening_time' => $request->input('opening_time'),
                    'closing_time' => $request->input('closing_time'),
                    'distance' => $request->input('distance'),
                    'lifestyle_type' => $request->input('lifestyle_type'),
                    'active_user' => 'Viraj',
                    'disabled' => '1',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'updated_by' => 'Viraj'
                ]);

                LifeStyleDetails::create([
                    'lifestyle_id' => $newLifeStyle->lifestyle_id,
                    'entrance' => $request->input('entrance'),
                    'guide' => $request->input('guide'),
                    'meal' => $request->input('meal'),
                    'meal_transfer' => $request->input('meal_transfer'),
                    'water_bottle' => $request->input('water_bottle'),
                    'highway_charges' => $request->input('highway_charges'),
                    'covid_safe' => $request->input('covid_safe'),
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'updated_by' => 'Viraj'
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Successfull',
                'lifeStyleData' => $newLifeStyle
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }

    public function get_lifestyles($category1, $category2, $category3, $category4, $latlon, $radius, $limit)
    {

        // ini_set('max_execution_time', 300);

        $today_date = Carbon::now()->format('Y-m-d');
        $end_day_in_two_month = Carbon::now()->addMonth()->lastOfMonth()->format('Y-m-d');


        // return $today_date;

        $whereArray = array();

        if ($category1 != 0 && $category2 != 0 && $category3 != 0 && $category4 != 0) {
            $whereArray = [['tbl_lifestyle.category1', '=', $category1], ['tbl_lifestyle.category2', '=', $category2], ['tbl_lifestyle.category3', '=', $category3], ['tbl_lifestyle.category4', '=', $category4], ['tbl_lifestyle_inventory.inventory_date', '>=', $today_date], ['tbl_lifestyle_inventory.inventory_date', '<=', $end_day_in_two_month]];
        } else if ($category3 != 0 && $category2 != 0 && $category3 != 0) {
            $whereArray = [['tbl_lifestyle.category1', '=', $category1], ['tbl_lifestyle.category2', '=', $category2], ['tbl_lifestyle.category3', '=', $category3], ['tbl_lifestyle_inventory.inventory_date', '>=', $today_date], ['tbl_lifestyle_inventory.inventory_date', '<=', $end_day_in_two_month]];
        } else if ($category1 != 0 && $category2 != 0) {
            $whereArray = [['tbl_lifestyle.category1', '=', $category1], ['tbl_lifestyle.category2', '=', $category2], ['tbl_lifestyle_inventory.inventory_date', '>=', $today_date], ['tbl_lifestyle_inventory.inventory_date', '<=', $end_day_in_two_month]];
        } else {
            $whereArray = [['tbl_lifestyle.category1', '=', $category1], ['tbl_lifestyle_inventory.inventory_date', '>=', $today_date], ['tbl_lifestyle_inventory.inventory_date', '<=', $end_day_in_two_month]];
        }
        //'tbl_lifestyle_inventory.inventory_date', '>=', $first_date, 'tbl_lifestyle_inventory.inventory_date', '<=', $last_date

        //['tbl_lifestyle_inventory.inventory_date', '>=', $today_date], ['tbl_lifestyle_inventory.inventory_date', '<=', $end_day_in_two_month]

        // return "Test";


        $latitudeLongitudes = explode(',', $latlon);

        $lat = $latitudeLongitudes[0];
        $lon = $latitudeLongitudes[1];
        $rad = $radius;


        $har = "(6371 * acos(cos(radians(" . $lat . ")) 
        * cos(radians(tbl_lifestyle.latitude)) 
        * cos(radians(tbl_lifestyle.longitude) - radians(" . $lon . ")) 
        + sin(radians(" . $lat . ")) 
        * sin(radians(tbl_lifestyle.latitude))))";


        if ($latlon != "\"\"") {

         
            $lifeStyles = DB::table('tbl_lifestyle')
                ->leftJoin('tbl_lifestyle_detail', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_detail.lifestyle_id')
                ->join('tbl_lifestyle_inventory', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_inventory.lifestyle_id')
                ->join('tbl_lifestyle_rates', 'tbl_lifestyle_inventory.lifestyle_inventory_id', '=', 'tbl_lifestyle_rates.lifestyle_inventory_id')

                ->leftJoin('tbl_lifestyle_discount', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_discount.lifestyle_id')
                ->select(
                    DB::raw("min(tbl_lifestyle_rates.adult_rate) AS adult_rate"),
                    DB::raw("min(tbl_lifestyle_rates.child_rate) AS child_rate"),
                    DB::raw("max(tbl_lifestyle_inventory.inventory_date) AS inventory_date"),
                    'tbl_lifestyle.lifestyle_city',
                    'tbl_lifestyle.lifestyle_attraction_type',
                    'tbl_lifestyle.lifestyle_name',
                    'tbl_lifestyle.lifestyle_description',
                    'tbl_lifestyle.image',
                    'tbl_lifestyle.lifestyle_id',
                    'tbl_lifestyle_inventory.pickup_location',
                    // 'tbl_lifestyle_inventory.inventory_date',
                    'tbl_lifestyle_inventory.pickup_time',
                    'tbl_lifestyle_rates.currency',
                    'tbl_lifestyle_discount.lifestyle_inventory_id',
                    'tbl_lifestyle_discount.discount_limit',
                    'tbl_lifestyle_discount.discount_type',
                    'tbl_lifestyle_discount.offered_product',
                    'tbl_lifestyle_discount.direct',
                    'tbl_lifestyle_discount.value',
                    'tbl_lifestyle_discount.inventory_limit',
                    'tbl_lifestyle_discount.sale_start_date',
                    'tbl_lifestyle_discount.sale_end_date',
                    'tbl_lifestyle_rates.cancellation_days',
                    'tbl_lifestyle_rates.book_by_days',
                    'tbl_lifestyle_rates.booking_start_date',
                    'tbl_lifestyle_rates.payment_policy',
                    'tbl_lifestyle_rates.cancel_policy',
                    'tbl_lifestyle_rates.currency',
                    // "{$har} as Distance"
                )

                ->groupBy('tbl_lifestyle.lifestyle_id')
                ->orderBy('tbl_lifestyle_inventory.inventory_date')


                ->where($whereArray)

                ->selectRaw("{$har} AS distance")
                ->whereRaw("{$har} < ?", [$rad])
                ->limit($limit)

                // ->selectRaw("{$distance} AS distance")
                // ->whereRaw("{$distance} < ?", [$rad])
                ->get();
        } else {
            $lifeStyles = DB::table('tbl_lifestyle')
                ->leftJoin('tbl_lifestyle_detail', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_detail.lifestyle_id')
                ->join('tbl_lifestyle_inventory', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_inventory.lifestyle_id')
                ->join('tbl_lifestyle_rates', 'tbl_lifestyle_inventory.lifestyle_inventory_id', '=', 'tbl_lifestyle_rates.lifestyle_inventory_id')

                ->leftJoin('tbl_lifestyle_discount', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_discount.lifestyle_id')
                ->select(
                    DB::raw("min(tbl_lifestyle_rates.adult_rate) AS adult_rate"),
                    DB::raw("min(tbl_lifestyle_rates.child_rate) AS child_rate"),
                    DB::raw("max(tbl_lifestyle_inventory.inventory_date) AS inventory_date"),
                    'tbl_lifestyle.lifestyle_city',
                    'tbl_lifestyle.lifestyle_attraction_type',
                    'tbl_lifestyle.lifestyle_name',
                    'tbl_lifestyle.lifestyle_description',
                    'tbl_lifestyle.image',
                    'tbl_lifestyle.lifestyle_id',
                    'tbl_lifestyle_inventory.pickup_location',
                    // 'tbl_lifestyle_inventory.inventory_date',
                    'tbl_lifestyle_inventory.pickup_time',
                    'tbl_lifestyle_rates.currency',
                    'tbl_lifestyle_discount.lifestyle_inventory_id',
                    'tbl_lifestyle_discount.discount_limit',
                    'tbl_lifestyle_discount.discount_type',
                    'tbl_lifestyle_discount.offered_product',
                    'tbl_lifestyle_discount.direct',
                    'tbl_lifestyle_discount.value',
                    'tbl_lifestyle_discount.inventory_limit',
                    'tbl_lifestyle_discount.sale_start_date',
                    'tbl_lifestyle_discount.sale_end_date',
                    'tbl_lifestyle_rates.cancellation_days',
                    'tbl_lifestyle_rates.book_by_days',
                    'tbl_lifestyle_rates.booking_start_date',
                    'tbl_lifestyle_rates.payment_policy',
                    'tbl_lifestyle_rates.cancel_policy',
                    'tbl_lifestyle_rates.currency',
                    // "{$har} as Distance"
                )

                ->groupBy('tbl_lifestyle.lifestyle_id')
                ->orderBy('tbl_lifestyle_inventory.inventory_date')


                ->where($whereArray)

                ->selectRaw("{$har} AS distance")
                ->whereRaw("{$har} < ?", [$rad])
                ->limit($limit)

                // ->selectRaw("{$distance} AS distance")
                // ->whereRaw("{$distance} < ?", [$rad])
                ->get();
        }

        return response()->json([
            'status' => 200,
            // 'radius' => $latitudeLongitudes,
            'lifeStylesData' => $lifeStyles,
        ]);
    }

    public function getLifeStylesByID($id)
    {
        $lifeStyles = DB::table('tbl_lifestyle')
            ->leftJoin('tbl_lifestyle_detail', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_detail.lifestyle_id')
            ->join('tbl_lifestyle_inventory', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_inventory.lifestyle_id')
            ->join('tbl_lifestyle_rates', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_rates.lifestyle_id')
            // ->join('tbl_lifestyle_rates', 'tbl_lifestyle_inventory.lifestyle_inventory_id', '=', 'tbl_lifestyle_rates.lifestyle_inventory_id')

            ->select(
                'tbl_lifestyle.lifestyle_city',
                'tbl_lifestyle.lifestyle_attraction_type',
                'tbl_lifestyle.lifestyle_name',
                'tbl_lifestyle.lifestyle_description',
                'tbl_lifestyle.image',
                'tbl_lifestyle.lifestyle_id',
                'tbl_lifestyle_rates.child_rate',
                'tbl_lifestyle_rates.adult_rate',
                'tbl_lifestyle_rates.payment_policy',
                'tbl_lifestyle_rates.cancel_policy',
                'tbl_lifestyle_detail.closed_days',
                'tbl_lifestyle_detail.closed_dates',
                'tbl_lifestyle_inventory.max_adult_occupancy',
                'tbl_lifestyle_inventory.max_children_occupancy',
                'tbl_lifestyle_rates.currency',
                'tbl_lifestyle.category1',
                'tbl_lifestyle.category2',
                'tbl_lifestyle.category3',
                'tbl_lifestyle.category4',
                'tbl_lifestyle_rates.currency',
                DB::raw("min(tbl_lifestyle_rates.adult_rate) AS default_adult_rate"),
                DB::raw("min(tbl_lifestyle_rates.child_rate) AS default_child_rate"),
            )
            ->where('tbl_lifestyle.lifestyle_id', $id)
            ->get();

        // return $lifeStyles;

        $lifeStyleServiceLocations = DB::table('tbl_lifestyle_inventory')
            ->select('tbl_lifestyle_inventory.pickup_location')
            ->where('tbl_lifestyle_inventory.lifestyle_id', $id)
            ->groupBy('tbl_lifestyle_inventory.pickup_location')
            ->get();

        $lifeStyleDefaultRate = DB::table('tbl_lifestyle')
            ->join('tbl_lifestyle_rates', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_rates.lifestyle_id')
            ->select(
                DB::raw("min(tbl_lifestyle_rates.adult_rate) AS adult_rate"),
                DB::raw("min(tbl_lifestyle_rates.child_rate) AS child_rate"),
            )
            ->where('tbl_lifestyle.lifestyle_id', $id)

            ->get();

        $lifeStyleInventory = DB::table('tbl_lifestyle_inventory')
            ->where('tbl_lifestyle_inventory.lifestyle_id', $id)
            ->get();

        $inventoryDates = DB::table('tbl_lifestyle_inventory')
            ->where('tbl_lifestyle_inventory.lifestyle_id', $id)
            ->select('inventory_date')
            ->groupBy('inventory_date')
            ->get();

        foreach ($inventoryDates as $key) {
            $invDates[] = $key->inventory_date;
        }

        $lifeStyleRates = DB::table('tbl_lifestyle_rates')
            ->where('tbl_lifestyle_rates.lifestyle_id', $id)
            ->get();

        $lifeStyleDiscounts = DB::table('tbl_lifestyle_discount')
            ->where('tbl_lifestyle_discount.lifestyle_id', $id)
            ->get();


        $lifeStyleTnC = DB::table('tbl_lifestyle_terms_and_conditions')
            ->where('tbl_lifestyle_terms_and_conditions.lifestyle_id', $id)
            ->get();


        return response()->json([
            'status' => 200,
            'lifeStylesData' => $lifeStyles,
            'lifeStyleRate' => $lifeStyleDefaultRate,
            'servicePoints' => $lifeStyleServiceLocations,
            'lifeStyleInventory' => $lifeStyleInventory,
            'lifeStyleInventoryRates' => $lifeStyleRates,
            'lifeStyleDiscount' => $lifeStyleDiscounts,
            'lifeStyleTnC' => $lifeStyleTnC,
            'inventoryDates' => $invDates
        ]);
    }
}
