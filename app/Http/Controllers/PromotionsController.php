<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\ProductListingDiscounts;

use Illuminate\Support\Facades\DB;

class PromotionsController extends Controller
{

    /* Create discount types function starting */
    public function createNewListingPromotion(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            ProductListingDiscounts::create([
                'listing_id' => $request->input('listing_id'),
                'inventory_id' => $request->input('inventory_id'),
                'discount_type_id' => $request->input('discount_type_id'),
                'discount_min_order_qty' => $request->input('discount_min_order_qty'),
                'discount_max_order_qty' => $request->input('discount_max_order_qty'),
                'discount_amount' => $request->input('discount_amount'),
                'discount_percentage' => $request->input('discount_percentage'),
                'offer_product' => $request->input('offer_product'),
                'offer_product_inventory' => $request->input('offer_product_inventory'),
                'offer_product_qty' => $request->input('offer_product_qty'),
                'offer_product_title' => $request->input('offer_product_title')
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Successfull',
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }

    public function getAllBrands()
    {
        $brands = DB::table('tbl_brands')->get();

        return response()->json([
            'status' => 200,
            'brands' => $brands,
        ]);
    }


    /* Create discount types function Ending */
}
