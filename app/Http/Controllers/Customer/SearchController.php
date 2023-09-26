<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{

    public $api_key;

    public function __construct()
    {
        // set_time_limit(0);
        $this->api_key = "AIzaSyAVZV3D2aAC4a9w8BqLvBx0DxSMwLZkKjI";
    }

    function getHeader()
    {
        $Header = [];

        $Header['Accept'] = 'application/json';
        $Header['Content-Type'] = 'application/json';

        return $Header;
    }


    public function productSearchByImage2(Request $request)
    {
        $customerImage = "";


        if ($request->hasFile('userImage')) {
            $file = $request->file('userImage');
            $fileExtension = $file->getClientOriginalExtension();
            $fileName =  $file->getClientOriginalName();
            $file->move('uploads/image_searches/', $fileName);
            $customerImage = 'https://api.aahaas.com/uploads/image_searches/' . $fileName;
        }

        // return $customerImage;

        $url = "https://api.aahaas.com/uploads/image_searches/20211007_tm_chilli_pepper_heatwave_improved_mixed.jpg";

        $passData = ' {
            "parent": "",
            "requests": [
                {
                "image": {
                    "source": {
                    "imageUri": "' . $customerImage . '"
                    }
                },
                "features": [
                    {
                    "type": "LABEL_DETECTION"
                    
                    },
                    {
                    "type": "OBJECT_LOCALIZATION"
                    },

                    {
                        "type": "PRODUCT_SEARCH"
                    },
                    {
                        "type": "DOCUMENT_TEXT_DETECTION"
                    },
                    {
                        "type":"LANDMARK_DETECTION"
                    }
                ]
                }
            ]
        }';

        $jsonData = json_decode($passData, true);


        $response = Http::withHeaders($this->getHeader())->post('https://vision.googleapis.com/v1/images:annotate?key=AIzaSyAVZV3D2aAC4a9w8BqLvBx0DxSMwLZkKjI', $jsonData)->json();

        $responseKeys = [];
        foreach ($response['responses'] as $key) {
            // return   array_key_exists('labelAnnotations', $key);


            if (array_key_exists('labelAnnotations', $key)) {
                foreach ($key['labelAnnotations'] as $lableAnnotions) {


                    $responseKeys[] = $lableAnnotions['description'];
                }
            }
            if (array_key_exists('localizedObjectAnnotations', $key)) {
                // return $key["localizedObjectAnnotations"];
                foreach ($key['localizedObjectAnnotations'] as $localizedObjectAnnotations) {
                    $responseKeys[] = $localizedObjectAnnotations['name'];
                }
            }

            if (array_key_exists('textAnnotations', $key)) {

                foreach ($key['textAnnotations'] as $textAnnotations) {
                    $responseKeys[] = $textAnnotations['description'];
                }
            }

            if (array_key_exists('localizedObjectAnnotations', $key)) {
                foreach ($key['localizedObjectAnnotations'] as $localizedObjectAnnotations) {
                    $responseKeys[] = $localizedObjectAnnotations['name'];
                }
            }
        }

        // return $responseKeys;
        // return $this->searchProductsByImage($responseKeys);
    }


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




    public function productSearchByImage(Request $request)
    {

        $customerImage = "";

        if ($request->hasFile('userImage')) {
            $file = $request->file('userImage');
            $fileExtension = $file->getClientOriginalExtension();
            $fileName =  $file->getClientOriginalName();
            $file->move('uploads/image_searches/', $fileName);
            $customerImage = 'https://api.aahaas.com/uploads/image_searches/' . $fileName;
        }

        // return $customerImage;

        $url = "https://api.aahaas.com/uploads/image_searches/20211007_tm_chilli_pepper_heatwave_improved_mixed.jpg";

        $passData = ' {
            "parent": "",
            "requests": [
                {
                "image": {
                    "source": {
                    "imageUri": "' . $customerImage . '"
                    }
                },
                "features": [
                    {
                    "type": "LABEL_DETECTION"
                    
                    },
                    {
                    "type": "OBJECT_LOCALIZATION"
                    },

                    {
                        "type": "PRODUCT_SEARCH"
                    },
                    {
                        "type": "DOCUMENT_TEXT_DETECTION"
                    },
                    {
                        "type":"LANDMARK_DETECTION"
                    }
                ]
                }
            ]
        }';

        $jsonData = json_decode($passData, true);


        $response = Http::withHeaders($this->getHeader())->post('https://vision.googleapis.com/v1/images:annotate?key=AIzaSyAVZV3D2aAC4a9w8BqLvBx0DxSMwLZkKjI', $jsonData)->json();

        $responseKeys = [];
        foreach ($response['responses'] as $key) {
            // return   array_key_exists('labelAnnotations', $key);


            if (array_key_exists('labelAnnotations', $key)) {
                foreach ($key['labelAnnotations'] as $lableAnnotions) {


                    $responseKeys[] = $lableAnnotions['description'];
                }
            }
            if (array_key_exists('localizedObjectAnnotations', $key)) {
                // return $key["localizedObjectAnnotations"];
                foreach ($key['localizedObjectAnnotations'] as $localizedObjectAnnotations) {
                    $responseKeys[] = $localizedObjectAnnotations['name'];
                }
            }

            if (array_key_exists('textAnnotations', $key)) {

                foreach ($key['textAnnotations'] as $textAnnotations) {
                    $responseKeys[] = $textAnnotations['description'];
                }
            }

            if (array_key_exists('localizedObjectAnnotations', $key)) {
                foreach ($key['localizedObjectAnnotations'] as $localizedObjectAnnotations) {
                    $responseKeys[] = $localizedObjectAnnotations['name'];
                }
            }
        }

        // return $responseKeys;

        try {
            $word = "";
            $SearchArray = $responseKeys;
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
                ->where(function ($query) use ($SearchArray) {
                    foreach ($SearchArray as $wordVal) {
                        $query->orWhere('tbl_product_listing.listing_title', 'LIKE', '%' . $wordVal . '%');
                    }
                })

                // ->where('tbl_product_details.search_tags', 'LIKE', '%' . $SearchArray . '%')
                // ->orWhere('tbl_product_listing.listing_title', 'LIKE', '%' . $SearchArray . '%')
                ->get();


            // //LifeStyles
            // $lifeStyles = DB::table('tbl_lifestyle')
            //     ->join('tbl_lifestyle_detail', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_detail.lifestyle_id')
            //     ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle.lifestyle_id', '=', 'tbl_lifestyle_rates.lifestyle_id')
            //     ->select(
            //         DB::raw("min(tbl_lifestyle_rates.adult_rate) AS adult_rate"),
            //         DB::raw("min(tbl_lifestyle_rates.child_rate) AS child_rate"),
            //         'tbl_lifestyle.lifestyle_city',
            //         'tbl_lifestyle.lifestyle_attraction_type',
            //         'tbl_lifestyle.lifestyle_name',
            //         'tbl_lifestyle.lifestyle_description',
            //         'tbl_lifestyle.image',
            //         'tbl_lifestyle.lifestyle_id',
            //         'tbl_lifestyle_rates.currency'
            //     )
            //     ->groupBy('tbl_lifestyle.lifestyle_id')
            //     ->where('tbl_lifestyle.lifestyle_attraction_type', 'LIKE', '%' . $SearchArray . '%')
            //     ->orWhere('tbl_lifestyle.lifestyle_name', 'LIKE', '%' . $SearchArray . '%')
            //     ->get();

            // //Education
            // $educationListings = DB::table('edu_tbl_education')
            //     ->leftJoin('edu_tbl_vendor', 'edu_tbl_education.vendor_id', '=', 'edu_tbl_vendor.id')
            //     ->leftJoin('edu_tbl_details', 'edu_tbl_education.education_id', '=', 'edu_tbl_details.edu_id')
            //     ->leftJoin('edu_tbl_inventory', 'edu_tbl_education.education_id', '=', 'edu_tbl_inventory.edu_id')
            //     ->leftJoin('edu_tbl_rate', 'edu_tbl_education.education_id', '=', 'edu_tbl_rate.edu_id')
            //     ->select(
            //         'edu_tbl_education.*',
            //         'edu_tbl_vendor.*',
            //         'edu_tbl_details.*',
            //         'edu_tbl_inventory.*',
            //         'edu_tbl_rate.adult_course_fee',
            //         'edu_tbl_rate.child_course_fee',
            //         'edu_tbl_rate.currency',
            //     )
            //     ->where('edu_tbl_education.course_name', 'LIKE', '%' . $SearchArray . '%')
            //     ->orWhere('edu_tbl_education.course_description', 'LIKE', '%' . $SearchArray . '%')
            //     ->groupBy('edu_tbl_education.education_id')
            //     ->get();

            // //Hotel
            // $hotelListings = DB::table('tbl_hotel')
            //     ->leftJoin('tbl_hotel_details', 'tbl_hotel.id', '=', 'tbl_hotel_details.hotel_id')
            //     ->select('*', 'tbl_hotel.id AS HotelIDHOTEL')
            //     ->where('tbl_hotel.hotel_name', 'LIKE', '%' . $SearchArray . '%')
            //     ->orWhere('tbl_hotel.hotel_description', 'LIKE', '%' . $SearchArray . '%')->get();

            return response()->json([
                'status' => 200,
                'essential_data' => $prodDataWithDiscounts,
                'search' => $responseKeys
                // 'lifestyle_data' => $lifeStyles,
                // 'education_data' => $educationListings,
                // 'hotel_data' => $hotelListings
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 400,
                'message' => throw $ex
            ]);
        }
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

            //Hotel
            $hotelListings = DB::table('tbl_hotel')
                ->leftJoin('tbl_hotel_details', 'tbl_hotel.id', '=', 'tbl_hotel_details.hotel_id')
                ->select('*', 'tbl_hotel.id AS HotelIDHOTEL')
                ->where('tbl_hotel.hotel_name', 'LIKE', '%' . $SearchText . '%')
                ->orWhere('tbl_hotel.hotel_description', 'LIKE', '%' . $SearchText . '%')->get();

            return response()->json([
                'status' => 200,
                'essential_data' => $prodDataWithDiscounts,
                'lifestyle_data' => $lifeStyles,
                'education_data' => $educationListings,
                'hotel_data' => $hotelListings
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 400,
                'message' => throw $ex
            ]);
        }
    }
}
