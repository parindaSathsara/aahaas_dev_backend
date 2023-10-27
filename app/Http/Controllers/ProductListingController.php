<?php

namespace App\Http\Controllers;

use App\Models\Customer\MainCheckout;
use Illuminate\Http\Request;
use App\Models\ProductListing;
use App\Models\ListingInventory;
use App\Models\Discount;
use App\Models\ProductListingRates;
use App\Models\ProductsOrders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ProductListingController extends Controller
{
    /* Get All the listings function starting */
    public function index()
    {
        $allListings = DB::table('tbl_product_listing')->get();

        $image_path = array();

        foreach ($allListings as $image) {
            $image_path[] = explode('|', $image->product_images);
        }

        // $pic1 = array_values($image_path)[0];
        // $pic2 = $image_path[0][1];
        // $pic3 = $image_path[0][2];
        // $pic4 = $image_path[0][3];

        return response()->json([
            'status' => 200,
            'allListing' => $allListings,
            'pic1' => $image_path
        ]);
    }
    /* Get All the listings function ending */

    /* Generate Unique Listing Number for each Listing Function Starting */
    public function generateListingId()
    {
        $pdListingId = '';

        $rowCount = DB::table('tbl_product_listing')->count();

        if ($rowCount == 0) {
            $pdListingId = 'PDL01';
        } else {
            $getListingId = DB::table('tbl_product_listing')->limit(1)->orderBy('id', 'DESC')->get('id');

            $listingIdEncode = json_encode($getListingId);
            $listingIdDecode = json_decode($listingIdEncode, true);

            $currentId = $listingIdDecode[0]['id'];
            $pdListingId = 'PDL01' . $currentId + 1;
        }

        return response()->json([
            'status' => 200,
            'sellerId' => $pdListingId
        ]);
    }


    public function fetchSellerProducts($id)
    {
        $prodDataWithDiscounts = DB::table('tbl_product_listing')
            ->join('tbl_seller', 'tbl_product_listing.seller_id', '=', 'tbl_seller.id')

            ->select('tbl_product_listing.id AS listing_id', 'tbl_product_listing.*')
            ->where('seller_id', '=', $id)
            ->get();

        return response()->json([
            'status' => 200,
            'selerData' => $prodDataWithDiscounts
        ]);
    }

    public function createVariations(Request $request)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
        $variationCount = $request->input('variation_count');

        $variationStatus = "";
        $variationStatus = $request->input('variationStatus');

        $mrp = $request->input('price');
        $selling_rate = $request->input('selling_rate');
        $min_order_qty = $request->input('minOrderQty');
        $max_order_qty = $request->input('maxOrderQty');
        $qty = $request->input('qty');


        if ($variationStatus != "noVariations") {

            if ($request->input('listingVariations')) {
                foreach ($request->input('listingVariations') as $variations) {

                    if ($variationCount == 1) {
                        $listingInventory = ListingInventory::create([
                            'variation_type1' => $variations['variationType1'],
                            'variant_type1' => $variations['variant1'],

                            'listing_id' => $request->input('listing_id'),
                            'created_at' => $currentTime,
                            'updated_at' => $currentTime,
                        ]);

                        ProductListingRates::create([
                            'inventory_id' => $listingInventory->id,
                            'active_start_date' => 'Test',
                            'active_end_date' => 'test',
                            'mrp' => $variations['price'],
                            'selling_rate' => $variations['sellingRate'],
                            'wholesale_rate' => 0.00,
                            'purchase_price' => 0.00,
                            'min_order_qty' => $variations['minOrderQty'],
                            'max_order_qty' => $variations['maxOrderQty'],
                            'qty' => $variations['qty'],
                        ]);
                    } else if ($variationCount  == 2) {
                        $listingInventory = ListingInventory::create([
                            'variation_type1' => $variations['variationType1'],
                            'variation_type2' => $variations['variationType2'],

                            'variant_type1' => $variations['variant1'],
                            'variant_type2' => $variations['variant2'],
                            'listing_id' => $request->input('listing_id'),
                            'created_at' => $currentTime,
                            'updated_at' => $currentTime
                        ]);

                        ProductListingRates::create([
                            'inventory_id' => $listingInventory->id,
                            'active_start_date' => 'Test',
                            'active_end_date' => 'test',
                            'mrp' => $variations['price'],
                            'selling_rate' => $variations['sellingRate'],
                            'wholesale_rate' => 0.00,
                            'purchase_price' => 0.00,
                            'min_order_qty' => $variations['minOrderQty'],
                            'max_order_qty' => $variations['maxOrderQty'],
                            'qty' => $variations['qty'],
                        ]);
                    } else if ($variationCount == 3) {
                        $listingInventory = ListingInventory::create([
                            'variation_type1' => $variations['variationType1'],
                            'variation_type2' => $variations['variationType2'],
                            'variation_type3' => $variations['variationType3'],

                            'variant_type1' => $variations['variant1'],
                            'variant_type2' => $variations['variant2'],
                            'variant_type3' => $variations['variant3'],

                            'listing_id' => $request->input('listing_id'),
                            'created_at' => $currentTime,
                            'updated_at' => $currentTime
                        ]);
                        ProductListingRates::create([
                            'inventory_id' => $listingInventory->id,
                            'active_start_date' => 'Test',
                            'active_end_date' => 'test',
                            'mrp' => $variations['price'],
                            'selling_rate' => $variations['sellingRate'],
                            'wholesale_rate' => 0.00,
                            'purchase_price' => 0.00,
                            'min_order_qty' => $variations['minOrderQty'],
                            'max_order_qty' => $variations['maxOrderQty'],
                            'qty' => $variations['qty'],
                        ]);
                    } else if ($variationCount  == 4) {
                        $listingInventory = ListingInventory::create([
                            'variation_type1' => $variations['variationType1'],
                            'variation_type2' => $variations['variationType2'],
                            'variation_type3' => $variations['variationType3'],
                            'variation_type4' => $variations['variationType4'],
                            'variant_type1' => $variations['variant1'],
                            'variant_type2' => $variations['variant2'],
                            'variant_type3' => $variations['variant3'],
                            'variant_type4' => $variations['variant4'],
                            'listing_id' => $request->input('listing_id'),
                            'created_at' => $currentTime,
                            'updated_at' => $currentTime
                        ]);
                        ProductListingRates::create([
                            'inventory_id' => $listingInventory->id,
                            'active_start_date' => 'Test',
                            'active_end_date' => 'test',
                            'mrp' => $variations['price'],
                            'selling_rate' => $variations['sellingRate'],
                            'wholesale_rate' => 0.00,
                            'purchase_price' => 0.00,
                            'min_order_qty' => $variations['minOrderQty'],
                            'max_order_qty' => $variations['maxOrderQty'],
                            'qty' => $variations['qty'],
                        ]);
                    }
                }
            }
        } else {
            $listingInventory = ListingInventory::create([
                'status' => 'NoVariations',
                'listing_id' => $request->input('listing_id'),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
            ProductListingRates::create([
                'inventory_id' => $listingInventory->id,
                'active_start_date' => 'Test',
                'active_end_date' => 'test',
                'mrp' => $mrp,
                'selling_rate' => $selling_rate,
                'wholesale_rate' => 0.00,
                'purchase_price' => 0.00,
                'min_order_qty' => $min_order_qty,
                'max_order_qty' => $max_order_qty,
                'qty' => $qty
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => "Successfully Updated",
        ]);
    }


    /* Generate Unique Listing Number for each Listing Function Ending */

    /* Create new listing function starting */
    public function createNewListing(Request $request)
    {
        try {

            // $variationType1 = $request->input('variation_type1');
            // $variationType2 = $request->input('variation_type2');
            // $variationType3 = $request->input('variation_type3');
            // $variantType1 = $request->input('variant_type1');
            // $variantType2 = $request->input('variant_type2');
            // $variantType3 = $request->input('variant_type3');
            // $qty = $request->input('quantity');
            // $unitprice = $request->input('unit_price');
            // $listingId = $request->input('listing_id');
            $code = random_int(100000, 999999);
            $prod_images = array();
            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            if ($request->hasFile('image0')) {
                $filesLength = $request->input('imageLength');
                $intLength = (int)$filesLength - 1;

                for ($x = 0; $x <= $intLength; $x++) {
                    $file = $request->file('image' . $x);
                    $fileExtension = $file->getClientOriginalExtension();
                    $fileName = $code . $file->getClientOriginalName();
                    $upload_path = 'uploads/listing_images/';
                    $image_url = $upload_path . $fileName;
                    $file->move($upload_path, $fileName);
                    $prod_images[] = $image_url;
                }
            }

            $validator = Validator::make($request->all(), [
                // 'listing_title' => 'required|unique:tbl_product_listing',
                'listing_description' => 'required',
                // 'images'=>'image|mimes:png,jpeg,jpg|max:2048',
                'seller_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            }


            $newListing = ProductListing::create([
                'listing_title' => $request->input('listing_title'),
                'listing_description' => $request->input('listing_description'),
                'sub_description' => $request->input('sub_description'),
                'discount_status' => $request->input('discount_status'),
                'product_images' => implode('|', $prod_images),
                'lisiting_status' => 'Published',
                'seo_tags' => $request->input('seo_tags'),
                'seller_id' => $request->input('seller_id'),

                'created_at' => $currentTime,
                'updated_at' => $currentTime,
                'updated_by' => 'viraj@gmail.com',
                'sku' => $request->input('sku'),
                'unit' => $request->input('unit'),
                'brand_id' => $request->input('brand_id'),
            ]);

            // $newListingInventory = DB::select(DB::raw("INSERT INTO tbl_listing_inventory(variation_type1,variation_type2,variation_type3,variant_type1,variant_type2,variant_type3,quantity,unit_price,listing_id,created_at,updated_at)
            //                         VALUES($variationType1,$variationType2,$variationType3,$variantType1,$variantType2,$variantType3,$qty,$unitprice,$listingId,$currentTime,$currentTime)"));





            // Discount::create([
            //     'discount_title' => $request->input('discount_title'),
            //     'discount_rate' => $request->input('discount_rate'),
            //     'from_date' => $request->input('from_date'),
            //     'to_date' => $request->input('to_date'),
            //     'discount_type_id' => $request->input('discount_type_id'),
            //     'listing_id' => $newListing->id,
            //     'discount_rate_type' => $request->input('discount_rate_type'),
            //     'created_at' => $currentTime,
            //     'updated_at' => $currentTime,
            //     'updated_by' => 'viraj@gmail.com'
            // ]);

            return response()->json([
                'status' => 200,
                'data' => implode('|', $prod_images),
                'message' => 'Data Successfully updated',
                'listingID' => $newListing->id,
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }

    /* Create new listing function ending */

    /* Get Data from Product Listing Function Starting  */
    public function getProductListingData()
    {
        $listingData = DB::select(DB::raw('SELECT listing_title,listing_description,sub_description,cash_onDelivery,discount_status,product_images,lisiting_status,manufacture_title,submaincat_type,submaincatsub_type,
                        variation_type1,variation_type2,variation_type3,variant_type1,variant_type2,variant_type3,quantity,unit_price,discount_title_type,discount_title,discount_rate,from_date,to_date
                        FROM aahaas_v1.tbl_product_listing
                        JOIN aahaas_v1.tbl_listing_inventory ON aahaas_v1.tbl_product_listing.id = aahaas_v1.tbl_listing_inventory.listing_id
                        JOIN aahaas_v1.tbl_discount ON aahaas_v1.tbl_product_listing.id=aahaas_v1.tbl_discount.listing_id
                        JOIN aahaas_v1.tbl_discount_types ON aahaas_v1.tbl_discount.discount_type_id=aahaas_v1.tbl_discount_types.id
                        JOIN aahaas_v1.tbl_submaincategorysub ON aahaas_v1.tbl_product_listing.subcategory_id = aahaas_v1.tbl_submaincategorysub.submaincatsub_id
                        JOIN aahaas_v1.tbl_manufacture ON aahaas_v1.tbl_submaincategorysub.submaincatsub_id = aahaas_v1.tbl_manufacture.subcategory_id
                        JOIN aahaas_v1.tbl_submaincategory ON aahaas_v1.tbl_submaincategorysub.submaincat_id = aahaas_v1.tbl_submaincategory.submaincat_id
                        ORDER BY discount_status DESC'));

        $image_path = array();
        // $imagesAll = $listingData->product_images;/

        foreach ($listingData as $image) {
            $image_path[] = explode('|', $image->product_images);
        }

        $pic1 = $image_path[0][0];
        $pic2 = $image_path[0][1];
        $pic3 = $image_path[0][2];
        $pic4 = $image_path[0][3];
        // $pic4 = $image_path[0][4];

        return response()->json([
            'status' => 200,
            'listData' => $listingData,
            'image1' => $pic1
        ]);
    }
    /* Get Data from Product Listing Function Ending */


    public function getProdListingWithDicounts($category1, $category2, $category3, $category4, $limit)
    {

        $whereArray = array();

        // if ($mainId != 0 && $subId != 0) {
        //     $whereArray = [['tbl_product_details.category1', '=', $id], ['tbl_product_details.category2', '=', $mainId], ['tbl_product_details.category3', '=', $subId]];
        // } else if ($mainId != 0) {
        //     $whereArray = [['tbl_product_details.category1', '=', $id], ['tbl_product_details.category2', '=', $mainId]];
        // } else {
        //     $whereArray = [['tbl_product_details.category1', '=', $id]];
        // }


        if ($category1 != 0 && $category2 != 0 && $category3 != 0 && $category4 != 0) {
            $whereArray = [['tbl_product_details.category1', '=', $category1], ['tbl_product_details.category2', '=', $category2], ['tbl_product_details.category3', '=', $category3], ['tbl_product_details.category4', '=', $category4]];
        } else if ($category1 != 0 && $category2 != 0 && $category3 != 0) {
            $whereArray = [['tbl_product_details.category1', '=', $category1], ['tbl_product_details.category2', '=', $category2], ['tbl_product_details.category3', '=', $category3]];
        } else if ($category1 != 0 && $category2 != 0) {
            $whereArray = [['tbl_product_details.category1', '=', $category1], ['tbl_product_details.category2', '=', $category2]];
        } else {
            $whereArray = [['tbl_product_details.category1', '=', $category1]];
        }
        // test



        try {

            $prodDataWithDiscounts = DB::table('tbl_product_listing')
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                ->join('tbl_seller', 'tbl_product_listing.seller_id', '=', 'tbl_seller.id')
                // ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                // ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                // ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
                ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
                // ->join('tbl_product_listing_rates', 'tbl_listing_inventory.id', '=', 'tbl_product_listing_rates.inventory_id','inner','tbl_product_listing_rates.qty==0')

                ->join("tbl_product_listing_rates", function ($join) {
                    $join->on("tbl_listing_inventory.id", "=", "tbl_product_listing_rates.inventory_id")->where('tbl_product_listing_rates.qty', '>', 0);;
                })

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
                    'tbl_seller.latlon',
                    'tbl_product_listing.cancellationDay',
                    'tbl_listing_inventory.variant_type1',
                    'tbl_listing_inventory.variant_type2',
                    'tbl_listing_inventory.variant_type3',
                    'tbl_listing_inventory.variant_type4',
                    DB::raw("min(tbl_product_listing_rates.mrp) AS mrp_price")
                )
                ->orderBy('tbl_product_listing.id', 'desc')
                ->groupBy('tbl_product_listing.id')
                ->where($whereArray)
                ->limit($limit)
                ->get();

            $brands = [];



            foreach ($prodDataWithDiscounts as $product) {
                $brands[] = $product->brand_id;
            }

            sort($brands);
            $brands = array_values(array_unique($brands));

            // return $brands;

            // $brands = DB::table('tbl_product_listing')
            //     ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
            //     ->join('tbl_seller', 'tbl_product_listing.seller_id', '=', 'tbl_seller.id')
            //     // ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
            //     // ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
            //     // ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
            //     ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
            //     ->join('tbl_product_listing_rates', 'tbl_listing_inventory.id', '=', 'tbl_product_listing_rates.inventory_id')
            //     ->leftJoin('tbl_listing_discount', 'tbl_product_listing.id', '=', 'tbl_listing_discount.listing_id')

            //     ->select(
            //         'tbl_product_listing.brand_id',
            //     )

            //     ->where('tbl_product_details.category1', $category1)
            //     // ->where($whereArray)
            //     ->limit($limit)
            //     ->get();


            // $brands = DB::table('tbl_product_listing')
            //     ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
            //     ->groupBy('tbl_product_listing.brand_id')
            //     ->where('tbl_product_details.category1', $id)
            //     ->limit($limit)
            //     ->get();

            //----

            // $rate = new \stdClass();
            // foreach($prodDataWithDiscounts as $key => $value)
            // {
            //     $rate->$key = $value;
            // }

            return response()->json([
                'status' => 200,
                'discount_data' => $prodDataWithDiscounts,
                'brands' => $brands
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }


    /* Get Data from Product listing with Dicounts Function Starting */
    public function getProductRelated($id, $mainId, $subId, $brand, $limit)
    {


        $whereArray = array();


        if ($mainId != 0 && $subId != 0) {
            $whereArray = [['tbl_product_details.category1', '=', $id], ['tbl_product_details.category2', '=', $mainId], ['tbl_product_details.category3', '=', $subId]];
        } else if ($mainId != 0) {
            $whereArray = [['tbl_product_details.category1', '=', $id], ['tbl_product_details.category2', '=', $mainId]];
        } else {
            $whereArray = [['tbl_product_details.category1', '=', $id]];
        }


        try {

            $prodDataWithDiscounts = DB::table('tbl_product_listing')
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                ->join('tbl_seller', 'tbl_product_listing.seller_id', '=', 'tbl_seller.id')
                // ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                // ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                // ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
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
                    'tbl_seller.latlon',
                    DB::raw("min(tbl_product_listing_rates.mrp) AS mrp_price")
                )

                ->groupBy('tbl_product_listing.id')
                ->where($whereArray)
                ->orWhere('tbl_product_listing.brand_id', $brand)



                ->limit($limit)
                ->get();


            $brands = DB::table('tbl_product_listing')
                ->join('tbl_listing_inventory', 'tbl_product_listing.id', '=', 'tbl_listing_inventory.listing_id')
                // ->join('tbl_discount', 'tbl_product_listing.id', '=', 'tbl_discount.listing_id')
                // ->join('tbl_discount_types', 'tbl_discount.discount_type_id', '=', 'tbl_discount_types.id')
                ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
                ->join('tbl_product_listing_rates', 'tbl_listing_inventory.id', '=', 'tbl_product_listing_rates.inventory_id')
                ->leftJoin('tbl_listing_discount', 'tbl_product_listing.id', '=', 'tbl_listing_discount.listing_id')

                ->select(
                    'tbl_product_listing.brand_id',
                )
                ->orderBy('tbl_product_listing.brand_id')

                ->groupBy('tbl_product_listing.id')
                ->where('tbl_product_details.category1', $id)
                ->limit($limit)
                ->get();


            // $brands = DB::table('tbl_product_listing')
            //     ->join('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
            //     ->groupBy('tbl_product_listing.brand_id')
            //     ->where('tbl_product_details.category1', $id)
            //     ->limit($limit)
            //     ->get();

            //----

            // $rate = new \stdClass();
            // foreach($prodDataWithDiscounts as $key => $value)
            // {
            //     $rate->$key = $value;
            // }

            return response()->json([
                'status' => 200,
                'discount_data' => $prodDataWithDiscounts,
                'brands' => $brands
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }
    /* Get Data from Product listing with Dicounts Function Ending */



    public function getProductInventory($id)
    {
        $listingsData = DB::table('tbl_listing_inventory')
            ->where('tbl_listing_inventory.listing_id', '=', $id)
            ->join('tbl_product_listing', 'tbl_listing_inventory.listing_id', '=', 'tbl_product_listing.id')
            ->select(
                'tbl_listing_inventory.id AS inventory_id',
                'tbl_listing_inventory.*',
                'tbl_product_listing.*'
            )
            ->get();

        return response()->json([
            'status' => 200,
            'listingsData' => $listingsData,
        ]);
    }


    public function getAvailableQty($rateID)
    {
        $getCarts = ProductListingRates::where('rate_id', $rateID)
            ->select("tbl_product_listing_rates.qty")
            ->get();


        return $getCarts[0]['qty'];
    }






    public function confirmProductOrder(Request $request)
    {
        $currentDate = \Carbon\Carbon::now()->toDateString();
        try {

            $availableQty = $this->getAvailableQty($request->input('rate_id'));
            $reqQty = $request->input('order_quantity');

            return $request;

            if ($availableQty >= $reqQty) {
                $productOrder = ProductsOrders::create([
                    'listing_id' => $request->input('listing_id'),
                    'rate_id' => $request->input('rate_id'),
                    'inventory_id' => $request->input('inventory_id'),
                    'discount_id' => $request->input('discount_id'),
                    'payment_option_id' => $request->input('payment_option_id'),
                    'customer_id' => $request->input('customer_id'),
                    'order_number' => $request['orderid'],
                    'order_quantity' => $request->input('quantity'),
                    'unit_price' => $request->input('unit_price'),
                    'total_price' => $request->input('total_price'),
                    'discount_amount' => $request->input('discount_amount'),
                    'ship_to' => $request->input('ship_to'),
                    'address' => $request->input('address'),
                    'preffered_delivery_date' => $request->input('preffered_date'),
                    'message_to_seller' => $request->input('message_to_seller'),
                    'order_date' => $currentDate,
                    'order_status' => 'Pending',
                    'addressType' => $request->input('addressType')
                ]);

                DB::table('tbl_product_listing_rates')
                    ->where('rate_id', $request->input('rate_id'))
                    ->where('inventory_id', $request->input('inventory_id'))
                    ->update(['qty' => $availableQty - $reqQty]);

                if ($request['main_category_id'] === '2') {
                    MainCheckout::create([
                        'checkout_id' => $request['orderid'],
                        'essnoness_id' => $request->input('listing_id'),
                        'lifestyle_id' => null,
                        'education_id' => null,
                        'hotel_id' => null,
                        'flight_id' => null,
                        'main_category_id' => '2',
                        'quantity' => $request->input('quantity'),
                        'each_item_price' => $request->input('unit_price'),
                        'total_price' => $request->input('total_price'),
                        'discount_price' => $request->input('discount_amount'),
                        'bogof_item_name' => null,
                        'delivery_charge' => null,
                        'discount_type' => null,
                        'child_rate' => '-',
                        'adult_rate' => '-',
                        'discountable_child_rate' => null,
                        'discountable_adult_rate' => null,
                        'flight_trip_type' => null,
                        'flight_total_price' => null,
                        'related_order_id' => $request->input('preoid'),
                        'status' => 'Booked',
                        'delivery_status' => null,
                        'cx_id' => $request['user_id'],
                    ]);
                } else {
                    MainCheckout::create([
                        'checkout_id' => $request['orderid'],
                        'essnoness_id' => $request->input('listing_id'),
                        'lifestyle_id' => null,
                        'education_id' => null,
                        'hotel_id' => null,
                        'flight_id' => null,
                        'main_category_id' => '1',
                        'quantity' => $request->input('quantity'),
                        'each_item_price' => $request->input('unit_price'),
                        'total_price' => $request->input('total_price'),
                        'discount_price' => $request->input('discount_amount'),
                        'bogof_item_name' => null,
                        'delivery_charge' => null,
                        'discount_type' => null,
                        'child_rate' => '-',
                        'adult_rate' => '-',
                        'discountable_child_rate' => null,
                        'discountable_adult_rate' => null,
                        'flight_trip_type' => null,
                        'flight_total_price' => null,
                        'related_order_id' => $request->input('preoid'),
                        'status' => 'Booked',
                        'delivery_status' => null,
                        'cx_id' => $request['user_id'],
                    ]);
                }

                return response()->json([
                    'status' => 200,
                    'qty' => $productOrder,
                    'message' => "Order Success",
                    'inventory' => 'Available'
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => "Order Unsuccessful",
                    'inventory' => 'Unavailable',
                    'qty' => 0,

                ]);
            }
        } catch (\Exception $exception) {

            throw $exception;
        }
    }
}
