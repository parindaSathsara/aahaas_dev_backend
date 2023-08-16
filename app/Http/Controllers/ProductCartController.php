<?php

namespace App\Http\Controllers;

use App\Models\CustomerCarts;
use App\Models\CustomerCustomCarts;
use App\Models\Education\EducationBookings;
use App\Models\Hotels\HotelsPreBookings;
use App\Models\Lifestyle\LifeStyleBook;
use Illuminate\Http\Request;
use App\Models\ProductCart;
use App\Models\ProductListingRates;
use App\Models\ProductPreOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class ProductCartController extends Controller
{
    /* Products Adding to cart function starting */
    // public function productsAddToCart(Request $request)
    // {
    // try{

    //     // $request_data = array();
    //     $client_ip = \Request::ip();
    //     $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

    //     $listing_id = $request->input('listing_id');
    //     $cat_type = $request->input('cat_id');
    //     $user_id = $request->input('user_id');
    //     $product_title = $request->input('product_title');
    //     $delivery_date = $request->input('delivery_date');
    //     $quantity = $request->input('quantity');
    //     $unit_price = $request->input('unit_price');
    //     $child_count = $request->input('child_count');
    //     $adult_count = $request->input('adult_count');


    //     $cartData = ProductCart::create([
    //         'prod_title'=>$product_title,
    //         'cat_type_id'=>$cat_type,
    //         'delivery_date'=>$delivery_date,
    //         'quantity'=>$quantity,
    //         'unit_price'=>$unit_price,
    //         'child_count'=>$child_count,
    //         'adult_count'=>$adult_count,
    //         'user_id'=>$user_id,
    //         'listing_id'=>$listing_id,
    //     ]);

    //     Storage::disk('local')->append('_cart_daily_log.txt', $cartData);

    //     return response()->json([
    //         'status'=>200,
    //         'client_ip'=>$client_ip,
    //         'cardData'=>$cartData
    //     ]);

    // }catch(\Exception $exception){

    //     return response()->json([
    //         'status' => 400,
    //         'message' => $exception->getMessage()
    //     ]);

    // }
    // }
    /* Products Adding to cart function Ending */


    public function getCartDataLength($id)
    {
        $cartData = DB::table('tbl_customer_carts')
            ->where('tbl_customer_carts.customer_id', $id)
            ->where('tbl_customer_carts.cart_status', 'InCart')
            ->get();

        return $cartData->count();
    }


    public function createNewCart(Request $request)
    {
        try {

            $cartData = CustomerCarts::create([
                'customer_id' => $request->input('customer_id'),
                'cart_title' => $request->input('cart_title')
            ]);

            return response()->json([
                'status' => 200,
                'cartData' => $cartData,
                'message' => "Success"
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }

    public function getCartQty($rateID, $inventoryID, $listingID, $customerID, $cartID)
    {
        $getCarts = ProductPreOrder::where('rate_id', $rateID)
            ->where('essential_listing_id', $listingID)
            ->where('essential_inventory_id', $inventoryID)
            ->where('status', 'Pending')
            ->where('customer_id', $customerID)
            ->where('cart_id', $cartID)
            ->select("quantity")
            ->get();
        return $getCarts;
    }

    // public function getAvailableQty($rateID)
    // {
    //     $getCarts = ProductListingRates::where('rate_id', $rateID)
    //         ->select("tbl_product_listing_rates.qty")
    //         ->get();
    //     return $getCarts[0]['qty'];
    // }

    public function newOrder(Request $request)
    {
        try {

            $productQuantity = $this->getCartQty(
                $request->input('rate_id'),
                $request->input('essential_inventory_id'),
                $request->input('listing_id'),
                $request->input('customer_id'),
                $request->input('cart_id')
            );

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();


            if ($request->input('viewStatus') == 'update') {
                // if (count($productQuantity) > 0) {

                //     ProductPreOrder::where('rate_id', $request->input('rate_id'))
                //         ->where('essential_listing_id', $request->input('listing_id'))
                //         ->where('essential_inventory_id', $request->input('essential_inventory_id'))
                //         ->where('customer_id', $request->input('customer_id'))
                //         ->where('cart_id', $request->input('cart_id'))
                //         ->update(['quantity' => (int)$request->input('quantity'), 'updated_status' => 1]);


                //     return response()->json([
                //         'status' => 250,
                //         'message' => "Cart Updated Successfully"
                //     ]);
                // } else {
                $preOrderData = ProductPreOrder::where('essential_pre_order_id', $request->input('preId'))->update([
                    'essential_inventory_id' => $request->input('essential_inventory_id'),
                    'essential_listing_id' => $request->input('listing_id'),
                    'address' => $request->input('address'),
                    'city' => $request->input('city'),
                    'customer_id' => $request->input('customer_id'),
                    'cart_id' => $request->input('cart_id'),
                    'rate_id' => $request->input('rate_id'),
                    'preffered_date' => $request->input('preffered_date'),
                    'quantity' => $request->input('quantity'),
                    'status' => $request->input('status'),
                    'addressType' => $request->input('addressType'),
                    'deliveryRateID' => $request->input('deliveryRateID'),
                    'deliveryRate' => $request->input('deliverycharge')
                ]);

                CustomerCustomCarts::where('listing_pre_id', $request->input('preId'))->update([
                    'customer_id' => $request->input('customer_id'),
                    'main_category_id' => $request->input('main_category_id'),
                    'cart_id' => $request->input('cart_id'),
                    // 'listing_pre_id' => $preOrderData->id,
                    'lifestyle_pre_id' => '',
                    'hotels_pre_id' => '',
                    'cart_status' => 'InCart',
                    'cart_added_date' => $currentTime,
                    'order_preffered_date' => $request->input('preffered_date'),
                ]);
                // }
            } else {
                if (count($productQuantity) > 0) {

                    ProductPreOrder::where('rate_id', $request->input('rate_id'))
                        ->where('essential_listing_id', $request->input('listing_id'))
                        ->where('essential_inventory_id', $request->input('essential_inventory_id'))
                        ->where('customer_id', $request->input('customer_id'))
                        ->where('cart_id', $request->input('cart_id'))
                        ->update(['quantity' => (int)$request->input('quantity'), 'updated_status' => 1]);


                    return response()->json([
                        'status' => 250,
                        'message' => "Cart Updated Successfully"
                    ]);
                } else {
                    $preOrderData = ProductPreOrder::create([
                        'essential_inventory_id' => $request->input('essential_inventory_id'),
                        'essential_listing_id' => $request->input('listing_id'),
                        'address' => $request->input('address'),
                        'city' => $request->input('city'),
                        'customer_id' => $request->input('customer_id'),
                        'cart_id' => $request->input('cart_id'),
                        'rate_id' => $request->input('rate_id'),
                        'preffered_date' => $request->input('preffered_date'),
                        'quantity' => $request->input('quantity'),
                        'status' => $request->input('status'),
                        'addressType' => $request->input('addressType'),
                        'deliveryRateID' => $request->input('deliveryRateID'),
                        'deliveryRate' => $request->input('deliverycharge')
                    ]);

                    CustomerCustomCarts::create([
                        'customer_id' => $request->input('customer_id'),
                        'main_category_id' => $request->input('main_category_id'),
                        'cart_id' => $request->input('cart_id'),
                        'listing_pre_id' => $preOrderData->id,
                        'lifestyle_pre_id' => '',
                        'hotels_pre_id' => '',
                        'cart_status' => 'InCart',
                        'cart_added_date' => $currentTime,
                        'order_preffered_date' => $request->input('preffered_date'),

                    ]);
                }
            }





            return response()->json([
                'status' => 200,
                'data' => $request->input('deliveryRateID'),
                'preOrderData' => $request->input('preId'),
                'message' => "Success"
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => $exception->getMessage()
            ]);
        }
    }


    public function getCustomerCarts(Request $request)
    {
        try {
            $customerCarts = ProductPreOrder::where('customer_id', $request->input('customer_id'))
                ->where('status', 'Pending')
                ->where('essential_listing_id', $request->input('essential_listing_id'))
                ->get();

            return response()->json([
                'status' => 200,
                'cartData' => $customerCarts,
                'message' => "Success"
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }


    public function getCarts($id)
    {
        try {
            $getCarts = CustomerCarts::where('customer_id', $id)->get();

            return response()->json([
                'status' => 200,
                'cartData' => $getCarts,
                'message' => "Success"
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }


    public function addToCustomerCarts($request)
    {
        try {

            $cartData = CustomerCustomCarts::create([
                'customer_id' => $request->input('customer_id'),
                'cart_title' => $request->input('cart_title')
            ]);

            return response()->json([
                'status' => 200,
                'cartData' => $cartData,
                'message' => "Success"
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }

    public function addCart(Request $request)
    {
        $currentDate = \Carbon\Carbon::now('Asia/Kolkata')->toDateString();
        try {


            $cartData = CustomerCustomCarts::create([
                'main_category_id' => $request->input('main_category_id'),
                'cart_id' => $request->input('cart_id'),
                'listing_pre_id' => $request->input('listing_pre_id'),
                'lifestyle_pre_id' => $request->input('lifestyle_pre_id'),
                'hotels_pre_id' => $request->input('hotels_pre_id'),
                'cart_status' => $request->input('cart_status'),
                'cart_added_date' => $currentDate,
            ]);

            return response()->json([
                'status' => 200,
                'cartData' => $cartData,
                'message' => "Success"
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }


    public function deleteCart(Request $request)
    {
        CustomerCarts::where('cart_id', $request->input('cart_id'))->delete();

        return response()->json([
            'status' => 200,
            'message' => "Successfully Deleted"
        ]);
    }


    public function deleteCustomerCart(Request $request)
    {
        try {

            $id = $request->input('customer_cart_id');
            $mainCategoryID = $request->input('main_category_id');
            $related_id = $request->input('related_id');


            $validator = Validator::make($request->all(), [
                'related_id' => 'required',

            ]);


            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            } else {

                if ($mainCategoryID == 1) {
                    ProductPreOrder::where('essential_pre_order_id', $related_id)->delete();
                    CustomerCustomCarts::where('listing_pre_id', $related_id)->delete();
                } else if ($mainCategoryID == 2) {
                    ProductPreOrder::where('essential_pre_order_id', $related_id)->delete();
                    CustomerCustomCarts::where('listing_pre_id', $related_id)->delete();
                } else if ($mainCategoryID == 3) {
                    DB::table('tbl_lifestyle_bookings')->where('lifestyle_booking_id', $related_id)->delete();
                    CustomerCustomCarts::where('lifestyle_pre_id', $related_id)->delete();
                } else if ($mainCategoryID == 4) {
                    HotelsPreBookings::where('booking_id', $related_id)->delete();
                    CustomerCustomCarts::where('hotels_pre_id', $related_id)->delete();
                } else if ($mainCategoryID == 5) {
                    // EducationBookings::where('booking_id', $related_id)->delete();
                    CustomerCustomCarts::where('education_pre_id', $related_id)->delete();
                    EducationBookings::where('booking_id', $related_id)->delete();
                }

                return response()->json([
                    'status' => 200,
                    'message' => "Success"
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 4001,
                'message' => throw $exception
            ]);
        }
    }


    public function deleteAllCartData(Request $request)
    {
        try {

            $id = $request->input('customer_id');

            CustomerCustomCarts::where('customer_id', $id)->delete();
            ProductPreOrder::where('customer_id', $id)->delete();

            DB::table('tbl_customer_carts')
                ->join('tbl_carts', 'tbl_customer_carts.cart_id', '=', 'tbl_carts.cart_id')
                ->join('tbl_maincategory', 'tbl_customer_carts.main_category_id', '=', 'tbl_maincategory.id')

                ->where('tbl_carts.customer_id', $id)
                ->delete();

            return response()->json([
                'status' => 200,
                'message' => "Success"
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }





    public function getCartData($id)
    {
        try {
            $cartData = DB::table('tbl_customer_carts')

                ->join('tbl_carts', 'tbl_customer_carts.cart_id', '=', 'tbl_carts.cart_id')
                ->join('tbl_maincategory', 'tbl_customer_carts.main_category_id', '=', 'tbl_maincategory.id')
                ->leftJoin('tbl_lifestyle_bookings', 'tbl_customer_carts.lifestyle_pre_id', '=', 'tbl_lifestyle_bookings.lifestyle_booking_id')
                ->leftJoin('tbl_lifestyle', 'tbl_lifestyle_bookings.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')
                ->leftJoin('tbl_lifestyle_inventory', 'tbl_lifestyle_bookings.lifestyle_inventory_id', '=', 'tbl_lifestyle_inventory.lifestyle_inventory_id')
                ->leftJoin('tbl_lifestyle_rates', 'tbl_lifestyle_bookings.lifestyle_rate_id', '=', 'tbl_lifestyle_rates.lifestyle_rate_id')
                ->leftJoin('tbl_lifestyle_discount', 'tbl_lifestyle_bookings.lifestyle_discount_id', '=', 'tbl_lifestyle_discount.discount_id')

                ->leftJoin('tbl_essentials_preorder', 'tbl_customer_carts.listing_pre_id', '=', 'tbl_essentials_preorder.essential_pre_order_id')
                ->leftJoin('tbl_product_delivery_rates', 'tbl_essentials_preorder.deliveryRateID', '=', 'tbl_product_delivery_rates.delivery_rate_id')
                ->leftJoin('tbl_product_listing', 'tbl_essentials_preorder.essential_listing_id', '=', 'tbl_product_listing.id')
                ->leftJoin('tbl_product_details', 'tbl_product_listing.id', '=', 'tbl_product_details.listing_id')
                ->leftJoin('tbl_listing_inventory', 'tbl_essentials_preorder.essential_inventory_id', '=', 'tbl_listing_inventory.id')
                ->leftJoin('tbl_product_listing_rates', 'tbl_listing_inventory.id', '=', 'tbl_product_listing_rates.inventory_id')
                ->leftJoin('tbl_listing_discount', 'tbl_product_listing.id', '=', 'tbl_listing_discount.listing_id')

                ->leftJoin('tbl_hotels_pre_booking', 'tbl_customer_carts.hotels_pre_id', '=', 'tbl_hotels_pre_booking.booking_id')

                ->leftJoin('edu_tbl_booking', 'tbl_customer_carts.education_pre_id', '=', 'edu_tbl_booking.booking_id')

                ->leftJoin('edu_tbl_education', 'edu_tbl_booking.education_id', '=', 'edu_tbl_education.education_id')
                ->leftJoin('edu_tbl_details', 'edu_tbl_education.education_id', '=', 'edu_tbl_details.edu_id')
                ->leftJoin('edu_tbl_inventory', 'edu_tbl_education.education_id', '=', 'edu_tbl_inventory.edu_id')
                //->leftJoin('edu_tbl_rate', 'edu_tbl_education.education_id', '=', 'edu_tbl_rate.edu_id')
                ->leftJoin('edu_tbl_rate', 'edu_tbl_booking.rate_id', '=', 'edu_tbl_rate.id') //Added newly
                ->leftJoin('edu_tbl_discount', 'edu_tbl_rate.id', '=', 'edu_tbl_discount.edu_rate_id')
                //->leftJoin('edu_tbl_rate', 'edu_tbl_booking.rate_id', '=', 'edu_tbl_rate.id')


                ->select(
                    'tbl_essentials_preorder.essential_listing_id',
                    'tbl_essentials_preorder.address',
                    'tbl_essentials_preorder.city',
                    'tbl_essentials_preorder.addressType',
                    'tbl_essentials_preorder.preffered_date',
                    'tbl_product_listing.discount_status',
                    'tbl_essentials_preorder.quantity',
                    'tbl_customer_carts.*',
                    'tbl_maincategory.*',
                    'tbl_lifestyle.lifestyle_id',
                    'tbl_lifestyle.lifestyle_city',
                    'tbl_lifestyle.lifestyle_attraction_type',
                    'tbl_lifestyle.lifestyle_name',
                    'tbl_lifestyle.lifestyle_description',
                    'tbl_lifestyle.active_status',
                    'tbl_lifestyle.image',
                    'tbl_lifestyle_inventory.pickup_time',
                    'tbl_lifestyle_inventory.pickup_location',

                    'tbl_lifestyle_bookings.*',
                    'tbl_lifestyle_inventory.balance',
                    'tbl_lifestyle_rates.adult_rate',
                    'tbl_lifestyle_rates.child_rate',
                    'tbl_lifestyle_rates.student_rate',

                    'tbl_lifestyle_discount.discount_type',
                    'tbl_lifestyle_discount.value',
                    'tbl_lifestyle_rates.payment_policy',
                    'tbl_lifestyle_rates.cancellation_days',
                    'tbl_lifestyle_rates.cancel_policy',
                    // 'tbl_lifestyle_rates.payment_policy',


                    'tbl_lifestyle_inventory.inclusions as lifeStyleInclusions',
                    'tbl_lifestyle_inventory.exclusions as lifeStyleExclusions',
                    'tbl_lifestyle_inventory.inventory_date',
                    'tbl_lifestyle_inventory.lifestyle_inventory_id',

                    'tbl_lifestyle_rates.currency as lsCurrency',
                    'tbl_product_listing_rates.currency as esCurrency',
                    'tbl_product_listing_rates.currency as currency',
                    'edu_tbl_rate.currency as eCurrency',

                    'tbl_product_listing.listing_title',
                    'tbl_product_listing.product_images as productImage',
                    'tbl_product_listing_rates.rate_id as essentialsRateId',
                    'tbl_listing_inventory.variant_type1',
                    'tbl_listing_inventory.variant_type2',
                    'tbl_listing_inventory.variant_type3',
                    'tbl_listing_inventory.variant_type4',
                    'tbl_listing_inventory.variant_type5',

                    'tbl_product_listing_rates.mrp',
                    'tbl_product_listing_rates.selling_rate',
                    'tbl_product_listing_rates.wholesale_rate',
                    'tbl_product_listing_rates.purchase_price',
                    'tbl_product_listing.cancellationDay',


                    'tbl_product_details.payment_options',

                    'tbl_listing_discount.*',
                    'tbl_essentials_preorder.essential_inventory_id',
                    'tbl_essentials_preorder.quantity AS essentialQTY',
                    'tbl_essentials_preorder.essential_pre_order_id',
                    'tbl_essentials_preorder.deliveryRateID',
                    'tbl_essentials_preorder.deliveryRate',
                    'tbl_product_delivery_rates.currency as DeliveryCurrency',
                    // 'tbl_product_delivery_rates.deliveryRateID',
                    // 'tbl_essentials_preorder.ship_to AS ShipTo',
                    // 'tbl_essentials_preorder.address AS ShipAddress',
                    // 'tbl_essentials_preorder.preffered_delivery_date AS PrefDelDate',
                    // 'tbl_essentials_preorder.message_to_seller AS MessageSeller',

                    'tbl_hotels_pre_booking.*',
                    'tbl_hotels_pre_booking.booking_id AS hotel_id',

                    'edu_tbl_booking.student_type',
                    'edu_tbl_booking.session_id',
                    'edu_tbl_booking.discount_id as edu_discount_id',
                    'edu_tbl_booking.rate_id as edu_rate_id',
                    'edu_tbl_booking.booking_date as edu_booking_date',
                    'edu_tbl_booking.student_name',
                    'edu_tbl_booking.preffered_booking_date',
                    'edu_tbl_booking.student_age',

                    'edu_tbl_discount.id as discountID',
                    'edu_tbl_discount.edu_id',
                    'edu_tbl_discount.edu_inventory_id',
                    'edu_tbl_discount.edu_rate_id',
                    'edu_tbl_discount.value',
                    'edu_tbl_discount.discount_type',


                    'edu_tbl_education.*',
                    'edu_tbl_details.*',
                    'edu_tbl_inventory.*',
                    'edu_tbl_rate.adult_course_fee',
                    'edu_tbl_rate.child_course_fee',
                    'edu_tbl_rate.id AS rateID',

                )

                ->where('tbl_customer_carts.customer_id', $id)
                ->where('tbl_customer_carts.cart_status', 'InCart')
                ->get();


            $cartDataCategories = DB::table('tbl_customer_carts')
                ->join('tbl_maincategory', 'tbl_customer_carts.main_category_id', '=', 'tbl_maincategory.id')
                ->join('tbl_carts', 'tbl_customer_carts.cart_id', '=', 'tbl_carts.cart_id')
                ->groupBy('tbl_customer_carts.main_category_id', 'tbl_customer_carts.cart_id')
                ->where('tbl_customer_carts.customer_id', $id)
                ->where('tbl_customer_carts.cart_status', 'InCart')
                ->get();


            $cartDataDates = DB::table('tbl_customer_carts')
                ->join('tbl_maincategory', 'tbl_customer_carts.main_category_id', '=', 'tbl_maincategory.id')
                ->join('tbl_carts', 'tbl_customer_carts.cart_id', '=', 'tbl_carts.cart_id')
                ->groupBy('tbl_customer_carts.order_preffered_date')
                ->select('order_preffered_date')
                ->where('tbl_customer_carts.customer_id', $id)
                ->where('tbl_customer_carts.cart_status', 'InCart')
                ->get();

            $customerMultiCarts = DB::table('tbl_carts')
                ->join('tbl_customer_carts', 'tbl_carts.cart_id', '=', 'tbl_customer_carts.cart_id')
                ->groupBy('tbl_customer_carts.cart_id')
                ->where('tbl_customer_carts.customer_id', $id)
                ->where('tbl_customer_carts.cart_status', 'InCart')
                ->get();


            return response()->json([
                'status' => 200,
                'cartData' => $cartData,
                'cartCategories' => $cartDataCategories,
                'cartDates' => $cartDataDates,
                'customerMultiCarts' => $customerMultiCarts,
                'message' => "Success"
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }


    public function getCustomerPreDefineCartOrders($id)
    {
        $preDefnedOrders = ProductPreOrder::where('customer_id', $id)
            ->where('status', 'Pending')
            ->select("*")
            ->get();

        return response()->json([
            'status' => 200,
            'preOrders' => $preDefnedOrders,
        ]);
    }
}
