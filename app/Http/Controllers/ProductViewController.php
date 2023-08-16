<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class ProductViewController extends Controller
{
    /* Get Product Data By Id funcrion starting */
    public function viewProductDataById($id)
    {
        try {

            $image_path = array(); //Pushing array of product images

            $prodDataWithDiscounts = DB::table('tbl_product_listing')
                ->where('tbl_product_listing.id', $id)
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')

                // ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                // ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->join('tbl_seller', 'tbl_product_listing.seller_id', '=', 'tbl_seller.id')
                ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
                ->join('tbl_product_listing_rates', 'tbl_listing_inventory.id', '=', 'tbl_product_listing_rates.inventory_id')
                ->leftJoin('tbl_listing_discount', 'tbl_product_listing.id', '=', 'tbl_listing_discount.listing_id')

                ->select(
                    'tbl_product_listing.id',
                    'tbl_product_listing.cancellationDay',
                    'tbl_product_listing.listing_title',
                    'tbl_product_listing.listing_description',
                    'tbl_product_listing.sub_description',
                    'tbl_product_listing.cash_onDelivery',
                    'tbl_product_listing.discount_status',
                    'tbl_product_listing.product_images',
                    'tbl_product_listing.lisiting_status',
                    'tbl_product_listing.brand_id',
                    'tbl_product_listing_rates.*',
                    'tbl_product_details.*',
                    'tbl_listing_discount.*',
                    'tbl_seller.latlon',
                    'tbl_product_listing.*',
                    DB::raw("min(tbl_product_listing_rates.mrp) AS mrp_price")
                )
                ->groupBy('tbl_product_listing.id')

                ->get();



            foreach ($prodDataWithDiscounts as $image) {
                $image_path[] = explode('|', $image->product_images);
            }

            return response()->json([
                'status' => 200,
                'discount_data' => $prodDataWithDiscounts,
                'pic_1' => $image_path
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    
    /* Get Product Data By Id funcrion Ending */

    /* Sql query to group by variation type 1 funcrion starting */
    public function sqlGroupByVariationOne($id)
    {
        try {

            $variationOne = DB::table('tbl_product_listing')
                ->where('tbl_product_listing.id', $id)
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->select(
                    'aahaas_v1.tbl_product_listing.id AS Listing_Id',
                    'listing_title',
                    'listing_description',
                    'cash_onDelivery',
                    'discount_status',
                    'lisiting_status',
                    'variation_type1',
                    'variation_type2',
                    'variation_type3',
                    'variant_type1',
                    'variant_type2',
                    'variant_type3',
                    'quantity',
                    'unit_price',
                    'discount_title_type',
                    'discount_title',
                    'discount_rate'
                )
                ->groupBy('tbl_listing_inventory.variation_type1')
                ->get();

            return response()->json([
                'status' => 200,
                'variation_one_data' => $variationOne
            ]);
            // $variationOne = DB::table('SELECT aahaas_v1.tbl_product_listing.id AS Listing_Id,listing_title,listing_description,cash_onDelivery,discount_status,lisiting_status,
            //         variation_type1,variation_type2,variation_type3,variant_type1,variant_type2,variant_type3,quantity,unit_price,discount_title_type,discount_title,discount_rate
            //         FROM aahaas_v1.tbl_product_listing
            //         JOIN aahaas_v1.tbl_listing_inventory ON aahaas_v1.tbl_product_listing.id = aahaas_v1.tbl_listing_inventory.listing_id
            //         JOIN aahaas_v1.tbl_discount ON aahaas_v1.tbl_product_listing.id = aahaas_v1.tbl_discount.listing_id
            //         JOIN aahaas_v1.tbl_discount_types ON aahaas_v1.tbl_discount.discount_type_id = aahaas_v1.tbl_discount_types.id
            //         WHERE aahaas_v1.tbl_product_listing.id
            //         GROUP BY variation_type1');

        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }

    public function getDeliveryDistance(Request $request)
    {
        try {
            $cityName = $request->input('cityName');
            $date = $request->input('date');

            $deliveryURL = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $request->input('latLonMain') . "&destinations=" . $request->input('latLonSecondary') . "&key=AIzaSyDMlecBhMzqL5tXiuWOS4hqFCWcZkP7OMo";
            $response_1 = Http::get($deliveryURL)->json();

            $distance = $response_1['rows'][0]['elements'][0]['distance']['value'];
            $totDistance = round($distance / 1000);

            $getDistanceData = DB::table('tbl_product_delivery_rates')
                ->where('DateFrom', '<', $date)
                ->where('DateTo', '>', $date)
                ->where('KMRange', '<', $totDistance)
                ->where('KMRangeEnd', '>', $totDistance)
                ->whereRaw('FIND_IN_SET(?, CitiesInclude)', [$cityName])
                ->get();

            if (count($getDistanceData) > 0) {
                $delivery = $getDistanceData[0]->delivery_charge;
                $totalDeliveryPrice = $delivery * $totDistance;
                return response([
                    'status' => 200,
                    // 'message' => $totalDeliveryPrice,
                    // 'deliveryCharge' => $totalDeliveryPrice,
                    'message' => $getDistanceData[0]->delivery_charge,
                    'deliveryCharge' => $getDistanceData[0]->delivery_charge,
                    'currency' => $getDistanceData[0]->currency,
                    'deliveryRateID' => $getDistanceData[0]->delivery_rate_id,
                ]);
            } else {
                return response([
                    'status' => 400,
                    'message' => "Sorry! This product cannot be delivered to your selected address",
                    'deliveryCharge' => 0,
                    // 'currency' => $getDistanceData[0]->currency,
                ]);
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }


    /* Sql query to get all the variations */
    public function getVariationsByID($id)
    {
        try {

            $getVariant1ByGroup = [];
            $variations = DB::table('tbl_listing_inventory')
                ->join('tbl_product_listing_rates', 'tbl_product_listing_rates.inventory_id', '=', 'tbl_listing_inventory.id')

                ->where('tbl_listing_inventory.listing_id', $id)
                ->get();


            for ($x = 1; $x <= 5; $x++) {
                $getVariant1ByGroup[] = DB::table('tbl_listing_inventory')

                    ->where('tbl_listing_inventory.listing_id', $id)
                    ->select('tbl_listing_inventory.variant_type' . $x)
                    ->groupBy('tbl_listing_inventory.variant_type' . $x)
                    ->get();
            }



            return response()->json([
                'status' => 200,
                'variations' => $variations,
                'variant' => $getVariant1ByGroup
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Sql query to group by variation type 1 funcrion ending */



    public function getInventoryDataByID($id)
    {
        $inventoryDataSet = DB::table('tbl_listing_inventory')
            ->where('id', $id)->get();


        return response()->json([
            'status' => 200,
            'inventoryData' => $inventoryDataSet
        ]);
    }

    /* Sql query to group by variation type 2 funcrion starting */
    public function sqlGroupByVariationTwo($id)
    {
        try {

            $variationTwo = DB::table('tbl_product_listing')
                ->where('tbl_product_listing.id', $id)
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->select(
                    'aahaas_v1.tbl_product_listing.id AS Listing_Id',
                    'listing_title',
                    'listing_description',
                    'cash_onDelivery',
                    'discount_status',
                    'lisiting_status',
                    'variation_type1',
                    'variation_type2',
                    'variation_type3',
                    'variant_type1',
                    'variant_type2',
                    'variant_type3',
                    'quantity',
                    'unit_price',
                    'discount_title_type',
                    'discount_title',
                    'discount_rate'
                )
                ->groupBy('tbl_listing_inventory.variation_type2')
                ->get();

            return response()->json([
                'status' => 200,
                'variation_two_data' => $variationTwo
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Sql query to group by variation type 2 funcrion ending */

    /* Sql query to group by variation type 3 funcrion starting */
    public function sqlGroupByVariationThree($id)
    {
        try {

            $variationThree = DB::table('tbl_product_listing')
                ->where('tbl_product_listing.id', $id)
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->select(
                    'aahaas_v1.tbl_product_listing.id AS Listing_Id',
                    'listing_title',
                    'listing_description',
                    'cash_onDelivery',
                    'discount_status',
                    'lisiting_status',
                    'variation_type1',
                    'variation_type2',
                    'variation_type3',
                    'variant_type1',
                    'variant_type2',
                    'variant_type3',
                    'quantity',
                    'unit_price',
                    'discount_title_type',
                    'discount_title',
                    'discount_rate'
                )
                ->groupBy('tbl_listing_inventory.variation_type3')
                ->get();

            return response()->json([
                'status' => 200,
                'variation_three_data' => $variationThree
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Sql query to group by variation type 3 funcrion ending */

    /* ------------------xxxxxxxxxxxxx---------------------- */
    /* ------------------xxxxxxxxxxxxx---------------------- */
    /* ------------------xxxxxxxxxxxxx---------------------- */

    /* Sql query to group by variant type 1 funcrion starting */
    public function sqlGroupByVariantOne($id)
    {
        try {

            $variantOne = DB::table('tbl_product_listing')
                ->where('tbl_product_listing.id', $id)
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->select(
                    'aahaas_v1.tbl_product_listing.id AS Listing_Id',
                    'listing_title',
                    'listing_description',
                    'cash_onDelivery',
                    'discount_status',
                    'lisiting_status',
                    'variation_type1',
                    'variation_type2',
                    'variation_type3',
                    'variant_type1',
                    'variant_type2',
                    'variant_type3',
                    'quantity',
                    'unit_price',
                    'discount_title_type',
                    'discount_title',
                    'discount_rate'
                )
                ->groupBy('tbl_listing_inventory.variant_type1')
                ->get();

            return response()->json([
                'status' => 200,
                'variant_one_data' => $variantOne
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Sql query to group by variant type 1 funcrion ending */

    /* Sql query to group by variant type 2 funcrion starting */
    public function sqlGroupByVariantTwo($id)
    {
        try {

            $variantTwo = DB::table('tbl_product_listing')
                ->where('tbl_product_listing.id', $id)
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                // ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                // ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->select('*')
                ->groupBy('tbl_listing_inventory.variant_type2')
                ->get();

            return response()->json([
                'status' => 200,
                'variant_two_data' => $variantTwo
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Sql query to group by variant type 2 funcrion ending */

    /* Sql query to group by variant type 3 funcrion starting */
    public function sqlGroupByVariantThree($id)
    {
        try {

            $variantThree = DB::table('tbl_product_listing')
                ->where('tbl_product_listing.id', $id)
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->select(
                    'aahaas_v1.tbl_product_listing.id AS Listing_Id',
                    'listing_title',
                    'listing_description',
                    'cash_onDelivery',
                    'discount_status',
                    'lisiting_status',
                    'variation_type1',
                    'variation_type2',
                    'variation_type3',
                    'variant_type1',
                    'variant_type2',
                    'variant_type3',
                    'quantity',
                    'unit_price',
                    'discount_title_type',
                    'discount_title',
                    'discount_rate'
                )
                ->groupBy('tbl_listing_inventory.variant_type3')
                ->get();

            return response()->json([
                'status' => 200,
                'variant_three_data' => $variantThree
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Sql query to group by variant type 3 funcrion ending */

    /* ------------------xxxxxxxxxxxxx---------------------- */
    /* ------------------xxxxxxxxxxxxx---------------------- */
    /* ------------------xxxxxxxxxxxxx---------------------- */

    /* Sql query to fetch prod variation by variant function starting */
    public function getVariantByVariation(Request $request, $id)
    {
        try {

            $variant1 = $request->input('variant1');
            $variant2 = $request->input('variant2');
            $variant3 = $request->input('variant3');

            $variantByVariationData = DB::table('tbl_product_listing')
                ->where([['tbl_product_listing.id', $id], ['tbl_listing_inventory.variant_type1', $variant1], ['tbl_listing_inventory.variant_type2', $variant2], ['tbl_listing_inventory.variant_type3', $variant3]])
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->select(
                    'aahaas_v1.tbl_product_listing.id AS Listing_Id',
                    'listing_title',
                    'listing_description',
                    'cash_onDelivery',
                    'discount_status',
                    'lisiting_status',
                    'variation_type1',
                    'variation_type2',
                    'variation_type3',
                    'variant_type1',
                    'variant_type2',
                    'variant_type3',
                    'quantity',
                    'unit_price',
                    'discount_title_type',
                    'discount_title',
                    'discount_rate'
                )
                ->get();

            return response()->json([
                'status' => 200,
                'variantByVariationData' => $variantByVariationData
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Sql query to fetch prod variation by variant function ending */
}
