<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /* Essential Search */

    public function getPromotionOffers($type, $mainCategory)
    {
        $prodDataWithDiscounts = DB::table('tbl_promotion_offers')
            // ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
            ->where('promotion_main_category', $mainCategory)
            // ->where('promotion_sub_category', $subCategory)
            ->where('promotion_priority', $type)
            ->get();

        return response()->json([
            'status' => 200,
            'promotionData' => $prodDataWithDiscounts,

        ]);
    }



    public function getAllPromotionOffers($type, $mainCategory, $subCategory)
    {
        $prodDataWithDiscounts = DB::table('tbl_promotion_offers')
            // ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
            // ->where('promotion_main_category', $mainCategory)
            ->leftJoin('tbl_product_details', 'tbl_promotion_offers.promotion_related_id', '=', 'tbl_product_details.listing_id')
            ->where('promotion_main_category', $mainCategory)
            ->where('promotion_sub_category', $subCategory)
            ->where('promotion_priority', $type)
            ->get();


        return response()->json([
            'status' => 200,
            'promotionData' => $prodDataWithDiscounts,

        ]);
    }




    public function searchEssentialProducts($search)
    {

        try {
            $SearchText = $search;
            //Essentials
            $prodDataWithDiscounts = DB::table('tbl_product_listing')
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                // ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                // ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
                ->join('tbl_product_listing_rates', 'tbl_listing_inventory.id', '=', 'tbl_product_listing_rates.inventory_id')
                ->leftJoin('tbl_listing_discount', 'tbl_product_listing.id', '=', 'tbl_listing_discount.listing_id')

                ->select(
                    'tbl_product_listing.id',
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
                    'tbl_product_listing.sku',
                    'tbl_product_listing.unit',
                    'tbl_product_details.search_tags',
                    DB::raw("min(tbl_product_listing_rates.mrp) AS mrp_price")
                )

                ->groupBy('tbl_product_listing.id')
                ->where('tbl_product_details.search_tags', 'LIKE', '%' . $SearchText . '%')
                ->orWhere('tbl_product_listing.listing_title', 'LIKE', '%' . $SearchText . '%')
                ->get();


            //LifeStyles
            $lifeStyles = DB::table('tbl_lifestyle')
                ->join('tbl_lifestyle_detail', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_detail.lifestyle_id')
                ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_rates.lifestyle_id')
                ->select(
                    DB::raw("min(tbl_lifestyle_rates.adult_rate) AS adult_rate"),
                    DB::raw("min(tbl_lifestyle_rates.child_rate) AS child_rate"),
                    'tbl_lifestyle.lifestyle_city',
                    'tbl_lifestyle.lifestyle_attraction_type',
                    'tbl_lifestyle.lifestyle_name',
                    'tbl_lifestyle.lifestyle_description',
                    'tbl_lifestyle.image',
                    'tbl_lifestyle.lifestyle_id',
                    'tbl_lifestyle_rates.currency'
                )
                ->groupBy('tbl_lifestyle.lifestyle_id')
                ->where('tbl_lifestyle.lifestyle_attraction_type', 'LIKE', '%' . $SearchText . '%')
                ->orWhere('tbl_lifestyle.lifestyle_name', 'LIKE', '%' . $SearchText . '%')
                ->get();

            //Education
            $educationListings = DB::table('edu_tbl_education')
                ->leftJoin('edu_tbl_vendor', 'edu_tbl_education.vendor_id', '=', 'edu_tbl_vendor.id')
                ->leftJoin('edu_tbl_details', 'edu_tbl_education.education_id', '=', 'edu_tbl_details.edu_id')
                ->leftJoin('edu_tbl_inventory', 'edu_tbl_education.education_id', '=', 'edu_tbl_inventory.edu_id')
                ->leftJoin('edu_tbl_rate', 'edu_tbl_education.education_id', '=', 'edu_tbl_rate.edu_id')
                ->select(
                    'edu_tbl_education.*',
                    'edu_tbl_vendor.*',
                    'edu_tbl_details.*',
                    'edu_tbl_inventory.*',
                    'edu_tbl_rate.adult_course_fee',
                    'edu_tbl_rate.child_course_fee',
                    'edu_tbl_rate.currency',
                )
                ->where('edu_tbl_education.course_name', 'LIKE', '%' . $SearchText . '%')
                ->orWhere('edu_tbl_education.course_description', 'LIKE', '%' . $SearchText . '%')
                ->groupBy('edu_tbl_education.education_id')
                ->get();

            return response()->json([
                'status' => 200,
                'essential_data' => $prodDataWithDiscounts,
                'lifestyle_data' => $lifeStyles,
                'education_data' => $educationListings
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 400,
                'message' => throw $ex
            ]);
        }
    }
}
