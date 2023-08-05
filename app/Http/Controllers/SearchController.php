<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductListing;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /* Essentials Search on each landing page function starting */
    public function mainSearchLanding(Request $request)
    {
        try {

            $userSearch_req = $request->input('ess_search_req');

            if ($userSearch_req === null) {
                return response()->json([
                    'status' => 401,
                    'essential_data' => 'Please enter a keyword to search products'
                ]);
            }

            $searchItem = ProductListing::query()
                ->where('listing_title', 'LIKE', "%{$userSearch_req}%")
                ->orWhere('seo_tags', 'LIKE', "%{$userSearch_req}%")->orderBy('subcategory_id', 'DESC')->get();

            return response()->json([
                'status' => 200,
                'essential_data' => $searchItem
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Essentials Search on each landing page function ending */

    /* FIlter Search Essential Data by Filters Function Starting */
    public function essentialSearchFilterByManufacture(Request $request)
    {
        try {

            $manufacture_requests = array();

            if ($manRequests = $request->input('manufacture_filter')) {
                foreach ($manRequests as $manReq) {
                    // $man_id = $manReq;
                    $manufacture_requests[] = '%' . $manReq . '%';
                }
            }

            $req_data = implode(" OR ", $manufacture_requests);

            $get_data = DB::table('tbl_product_listing')->where('tbl_manufacture.id', 'LIKE', "{$req_data}")
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->join('tbl_submaincategorysub', 'tbl_product_listing.subcategory_id', '=', 'tbl_submaincategorysub.submaincatsub_id')
                ->join('tbl_manufacture', 'tbl_submaincategorysub.submaincatsub_id', '=', 'tbl_manufacture.subcategory_id')
                ->join('tbl_submaincategory', 'tbl_submaincategorysub.submaincat_id', '=', 'tbl_submaincategory.submaincat_id')
                ->orderBy('discount_status', 'DESC')
                ->select('*')->get();


            return response()->json([
                'status' => 200,
                'manufac_requests' => $get_data
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* FIlter Search Essential Data by Filters Function Ending */
}
