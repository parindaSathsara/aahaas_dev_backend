<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ForgotController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\HotelBulkController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\Currency\CurrencyController;
use App\Http\Controllers\Customer\CancelOrderController;
use App\Http\Controllers\Customer\CustomerNotificationController;
use App\Http\Controllers\Customer\CustomerOrdersController;
use App\Http\Controllers\Customer\MainCheckoutController;
use App\Http\Controllers\Customer\OnlineTransferController;
use App\Http\Controllers\Customer\OrderFeedbackController;
use App\Http\Controllers\OrderDashboard\OrderDashboardController;
use App\Http\Controllers\Customer\SearchController as CustomerSearchController;
use App\Http\Controllers\Customer\ShippingAddressController;
use App\Http\Controllers\Customer\RecentSearchController;
use App\Http\Controllers\CustomerCartCheckout;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\MainCategoryController;
use App\Http\Controllers\SubMainCategoryController;
use App\Http\Controllers\SubMainCategorySubController;
use App\Http\Controllers\SubMiniCategoryController;
use App\Http\Controllers\ProductListingController;
use App\Http\Controllers\DiscountTypeController;
use App\Http\Controllers\Education\ClassRequestController;
use App\Http\Controllers\Education\EducationInventoryController;
use App\Http\Controllers\Education\EducationListingsController;
use App\Http\Controllers\Education\EducationSessionsController;
use App\Http\Controllers\Education\EducationVendorController;
use App\Http\Controllers\Education\VideoController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\Flights\AirportCodesController;
use App\Http\Controllers\FullCartCheckoutController;
use App\Http\Controllers\Hotels\AppleHolidays\BookingController;
use App\Http\Controllers\PaymentOptionsController;
use App\Http\Controllers\ProductViewController;
use App\Http\Controllers\ProductCartController;

use App\Http\Controllers\ProductDetailsController;

use App\Http\Controllers\TestController;
use App\Http\Controllers\Hotels\HotelController;
use App\Http\Controllers\Hotels\HotelDetailController;
use App\Http\Controllers\Hotels\HotelInventoryController;

use App\Http\Controllers\Hotels\RoomRateController;
use App\Http\Controllers\Hotels\ServiceRateController;
use App\Http\Controllers\Hotels\HotelDiscountController;
use App\Http\Controllers\Hotels\HotelTBO\HotelTBOController;
use App\Http\Controllers\Hotels\HotelVendorController;
use App\Http\Controllers\Hotels\HotelTermsConditionsController;
use App\Http\Controllers\Hotels\HotelBeds\HotelBedsController;
use App\Http\Controllers\Hotels\HotelsPreBookings;
use App\Http\Controllers\LifeStyles\LifeStyleBookingController;
use App\Http\Controllers\LifeStyles\LifeStylesController;
use App\Http\Controllers\LifeStyles\LifeStyleVenController;
use App\Http\Controllers\LifeStyles\LifeStyleVendorController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Payments\NewPaymentIPGController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\Sabre\SabreFlightController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ZoomMeeting\ZoomMeetingController;
use App\Models\Education\EducationListings;
use App\Models\Education\EducationSessions;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Delivery\DeliveryRateController;
use App\Http\Controllers\Driver\DriverRegController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Map\MapController;
use App\Http\Controllers\Customer\ProductReview\ProductReviewController;
use App\Http\Controllers\LifeStyles\Admin\LifeStyleCreatorController;
use App\Models\Currencies;
use App\Http\Controllers\Hotels\CommonHotel_APIController;
use App\Http\Controllers\Hotels\HotelMetaController;
use App\Http\Controllers\TBO_Hotel\TBOController;
use App\Models\Hotel\HotelMeta\HotelMeta;
use App\Models\Hotels\HotelInventory;
use App\Models\Lifestyle\LifeStyle;
use App\Http\Controllers\HotelsMeta\HotelsBooking;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/livewire-datatables', function () {
    return view('Livewire');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/get-all-user', [UserController::class, 'index']);
}); //test


Route::group(['middleware' => 'api'], function () {
    // Route::post('/login-user', ['as' => 'login', 'uses' => 'AuthController@userLoginWeb']);
    Route::post('/login-user', [AuthController::class, 'userLoginWeb'])->name('login');

    Route::post('/new-user-registration', [AuthController::class, 'registerUser'])->name('userregistration');

    Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');

    /* User Controller Routes ----------  */
    // Route::get('/get-all-user', [UserController::class, 'index']);
    Route::get('/get-user-by-id/{id}', [UserController::class, 'getUserById']);
    Route::post('/update-user/{id}', [UserController::class, 'updateUserData']);
    Route::delete('/remove-user/{id}', [UserController::class, 'userDeletion']);

    /* Auth Controller Routes ----------  */
    Route::post('/google-login-user', [AuthController::class, 'getGoogleUserData']);
    Route::post('/facebook-login-user', [AuthController::class, 'getFacebookUserData']);

    Route::post('/apple-login-user', [AuthController::class, 'getAppleUserData']);

    Route::get('/get-existing-user/{id}', [AuthController::class, 'googleUserDataCheck']);
    Route::get('/get-existing-user-facebook/{id}', [AuthController::class, 'googleUserDataCheck']);
    Route::post('/mobile-user', [AuthController::class, 'mobileUserCreation']);
    Route::get('/authenicate-user-byid/{id}', [AuthController::class, 'getCurrentUserById']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/onetimepassword', [AuthController::class, 'oneTimePasswordUpdate']);
    // Route::get('/user-verify-view', [AuthController::class, 'verifyMail']);


    /* Customer Controller Routes ----------  */
    Route::get('/get-all-customers', [CustomerController::class, 'index']);
    Route::get('/generate-cx-auto-id', [CustomerController::class, 'generateCustomerId']);
    Route::post('/create-new-customer', [CustomerController::class, 'registerNewCustomer']);
    Route::get('/get-customers-count', [CustomerController::class, 'getCustomerCount']);
    Route::get('/get-customer-data-byid/{id}', [CustomerController::class, 'getCustomerDataById']);

    Route::post('/update-customer-profile', [CustomerController::class, 'updateCustomerProfile']);
    Route::post('/deactivate-customer-profile/{id}', [CustomerController::class, 'deactivateCustomerAccount']);



    Route::get('/get-customer-data-by-originID/{id}', [CustomerController::class, 'getCustomerDataByOriginId']);

    /* Forgot Controller Routes ----------  */
    Route::post('/forgot-password', [ForgotController::class, 'forgotPassword']);

    Route::post('/forgot-password-mobile', [ForgotController::class, 'forgotPasswordMobile']);

    Route::post('/userreset', [ForgotController::class, 'resetPassword']);

    // Route::post('/forgot-view', [ForgotController::class, 'fogotview']);

    /* ---- ---- ---- ---- ---- ---- */

    /* Address Controller Routes ----------  */
    Route::post('/create-address', [AddressController::class, 'createAddress']);
    Route::post('/update-address/{id}', [AddressController::class, 'updateAddress']);
    Route::get('/get-addresses-by-id/{id}', [AddressController::class, 'getAddressByID']);
    Route::get('/get-addresses-by-addressid/{id}', [AddressController::class, 'getAddressByAddressID']);

    /* Seller Controller Routes ----------  */
    Route::get('/fetch-all-sellers', [SellerController::class, 'index']);
    Route::post('/create-new-seller', [SellerController::class, 'createSeller']);
    Route::get('/generate-seller-auto-id', [SellerController::class, 'generateSellerAutoId']);
    Route::post('/seller-activation/{id}', [SellerController::class, 'sellerActivation']);
    Route::get('/seller-by-id/{id}', [SellerController::class, 'getSellerDetailsById']);
    Route::post('/create-new-seller-login', [SellerController::class, 'newSellerLoginCreate']);
    Route::post('/verifi_email_otp_seller', [SellerController::class, 'verifyCode']);
    Route::get('/get_seller_user_count/{id}', [SellerController::class, 'getUserCount']);

    /* Main Category Controller Routes ----------  */
    Route::get('/fetch-all-main-cat', [MainCategoryController::class, 'index']);
    Route::post('/create-new-main-cat', [MainCategoryController::class, 'createMainCategory']);
    Route::get('/generate-maincat-auto-id', [MainCategoryController::class, 'generateMainCatId']);

    /* Sub Main Category Controller Routes ----------  */
    Route::get('/fetch-all-sub-main-cat', [SubMainCategoryController::class, 'index']);
    Route::post('/create-new-sub-main-cat', [SubMainCategoryController::class, 'createSubMainCategory']);
    Route::get('/generate-submaincat-auto-id', [SubMainCategoryController::class, 'generateSubMainCatId']);

    /* Sub Main Sub Category Controller Routes ----------  */
    Route::get('/fetch-all-sub-mainsub-cat', [SubMainCategorySubController::class, 'index']);
    Route::post('/create-new-sub-mainsub-cat', [SubMainCategorySubController::class, 'createSubMainSubCategory']);
    Route::get('/generate-submaincsubat-auto-id', [SubMainCategorySubController::class, 'generateSubMainSubCatId']);

    /* Sub Mini Category Controller Routes ----------  */
    Route::get('/fetch-all-sub-mini-cat', [SubMiniCategoryController::class, 'index']);
    Route::post('/create-new-sub-min-cat', [SubMiniCategoryController::class, 'createSubMiniCategory']);
    Route::get('/generate-submini-auto-id', [SubMiniCategoryController::class, 'generateSubMiniCatId']);

    /* Product Listing Controller Routes ----------  */
    Route::get('/fetch-all-listings', [ProductListingController::class, 'index']);
    Route::get('/fetch-listings-seller/{id}', [ProductListingController::class, 'fetchSellerProducts']);
    Route::post('/create-new-listing', [ProductListingController::class, 'createNewListing']);
    Route::post('/create-variations', [ProductListingController::class, 'createVariations']);
    Route::post('/confirm-order', [ProductListingController::class, 'confirmProductOrder']);



    /*Customer Cart Checkout Routes-------- */
    Route::post('/update_essentials_status', [CustomerCartCheckout::class, 'updateEssentialsStatus']);
    Route::post('/update_cart_status', [CustomerCartCheckout::class, 'updateCartStatus']);
    Route::post('/update_education_status', [CustomerCartCheckout::class, 'updateEducationStatus']);
    Route::post('/update_lifestyle_status', [CustomerCartCheckout::class, 'updateLifeStyleStatus']);


    /*Customer Full Cart Checkout Routes-------- */
    Route::post('/update_essentials_status_fullcart', [FullCartCheckoutController::class, 'updateEssentialsStatus']);
    Route::post('/update_cart_status_fullcart', [FullCartCheckoutController::class, 'updateCartStatus']);
    Route::post('/update_education_status_fullcart', [FullCartCheckoutController::class, 'updateEducationStatus']);
    Route::post('/update_lifestyle_status_fullcart', [FullCartCheckoutController::class, 'updateLifeStyleStatus']);

    Route::post('/check-inventory-availability', [ProductListingController::class, 'confirmProductOrder']);

    Route::post('/check-inventory-availability', [ProductListingController::class, 'confirmProductOrder']);


    Route::get('/get-list-data', [ProductListingController::class, 'getProductListingData']);


    Route::get('/get-list-data-test', [ProductListingController::class, 'getProductData']);

    Route::get('/get-product-inventory/{id}', [ProductListingController::class, 'getProductInventory']);
    Route::get('/get-list-data-with-discount/{category1}/{category2}/{category3}/{category4}/{limit}', [ProductListingController::class, 'getProdListingWithDicounts']);

    Route::get('/get-list-data-with-discount-related/{id}/{mainId}/{subId}/{brand}/{limit}', [ProductListingController::class, 'getProductRelated']);
    // Route::get('/generate-listing-category-id', [ProductListingController::class, 'generateListingId']);
    // Route::get('/generate-listing-category-id', [ProductListingController::class, 'generateListingId']);

    /* Discount Listing Controller Routes ----------  */
    Route::get('/fetch-all-discounttypes', [DiscountTypeController::class, 'index']);
    Route::post('/create-new-discounttype', [DiscountTypeController::class, 'createNewDiscountType']);
    Route::post('/create-new-promotion', [PromotionsController::class, 'createNewListingPromotion']);

    /* Filter Categories Controller Routes ----------  */
    Route::get('/fetch-all-mainsub', [FilterController::class, 'getMainSubCategories']);
    Route::get('/fetch-all-submainsub', [FilterController::class, 'getSubMainSubCategories']);
    Route::get('/fetch-all-manufacture', [FilterController::class, 'getManufactures']);

    Route::get('/fetch-all-categories', [FilterController::class, 'getCategories']);

    /* ProductView Controller Routes ---------- */

    Route::post('/getDeliveryDetails', [ProductViewController::class, 'getDeliveryDistance']);
    Route::get('/get-prodviewdata-byid/{id}', [ProductViewController::class, 'viewProductDataById']);
    Route::get('/get-variations-byid/{id}', [ProductViewController::class, 'getVariationsByID']);
    Route::get('/get-variation1-byid/{id}', [ProductViewController::class, 'sqlGroupByVariationOne']);
    Route::get('/get-variation2-byid/{id}', [ProductViewController::class, 'sqlGroupByVariationTwo']);
    Route::get('/get-variation3-byid/{id}', [ProductViewController::class, 'sqlGroupByVariationThree']);
    Route::get('/get-variant1-byid/{id}', [ProductViewController::class, 'sqlGroupByVariantOne']);
    Route::get('/get-variant2-byid/{id}', [ProductViewController::class, 'sqlGroupByVariantTwo']);
    Route::get('/get-variant3-byid/{id}', [ProductViewController::class, 'sqlGroupByVariantThree']);
    Route::get('/get-variant-byvariation/{id}', [ProductViewController::class, 'getVariantByVariation']);

    Route::get('/get-inventory-data-by-ID/{id}', [ProductViewController::class, 'getInventoryDataByID']);


    /* ProductCart Controller Routes ----------  */
    Route::get('/add-prod-to-cart', [ProductCartController::class, 'productsAddToCart']);
    Route::post('/add-to-cart', [ProductCartController::class, 'addCart']);
    Route::post('/create-new-cart', [ProductCartController::class, 'createNewCart']);
    Route::get('/get-carts/{id}', [ProductCartController::class, 'getCarts']);
    Route::get('/get-customer-cart-data/{id}', [ProductCartController::class, 'getCartData']);
    Route::get('/get-pre-defined-orders/{id}', [ProductCartController::class, 'getCustomerPreDefineCartOrders']);
    Route::post('/deleteCustomCart', [ProductCartController::class, 'deleteCart']);


    Route::post('/create-new-order', [ProductCartController::class, 'newOrder']);

    Route::post('/get-product-cart-availability-by-customer', [ProductCartController::class, 'getCustomerCarts']);

    Route::post('/delete-customer-cart-item', [ProductCartController::class, 'deleteCustomerCart']);
    Route::post('/delete-customer-cart', [ProductCartController::class, 'deleteAllCartData']);


    /* Hotel Controller Routes ----------  */
    Route::get('/get-hotel-data', [HotelController::class, 'index']);
    Route::post('/create-new-hotel', [HotelController::class, 'createNewHotel']);
    Route::get('/get-hotel-byid/{id}', [HotelController::class, 'fecthHotelById']);
    Route::post('/update-hotel-details/{id}', [HotelController::class, 'updateHotelDataById']);
    Route::get('/get_hotel_lowest_details/{id}', [HotelController::class, 'getLowestHotels']);

    Route::get('/get_hotel_roomdetails_by_id/{id}', [HotelController::class, 'getRoomCategoryDetailsById']);

    Route::get('/get_hotel_reservation_by_id/{id}', [HotelController::class, 'getHotelReservationDataById']);
    Route::post('/get_hotel_special_rates/{id}', [HotelController::class, 'getSpecialRatesById']);


    /* Hotel Detail Controller Routes ----------  */
    Route::get('/get-hoteldetails', [HotelDetailController::class, 'index']);
    Route::post('/create-new-hoteldetail', [HotelDetailController::class, 'createHotelDetails']);
    Route::get('/get-hoteldetail-byid/{id}', [HotelDetailController::class, 'fetchHotelDetailsById']);
    Route::post('/update-hotel-detailsdata/{id}', [HotelDetailController::class, 'updateHotelDetails']);
    Route::get('/fetch-hotel-joindata', [HotelDetailController::class, 'getHotelDetailsJoinData']);

    /* Hotel Inventory Controller Routes ----------  */
    Route::get('/get-inventory-data', [HotelInventoryController::class, 'index']);
    Route::post('/create-new-hotelinventory', [HotelInventoryController::class, 'createNewInventoryDetails']);
    Route::get('/fetch-hotel-invendata-byid/{id}', [HotelInventoryController::class, 'fetchDetailsById']);
    Route::post('/update-hotel-invendata/{id}', [HotelInventoryController::class, 'updateHotelInventoryDetails']);
    Route::get('/fetch-invendata-hotel', [HotelInventoryController::class, 'fetchDetailsWithHotelName']);
    Route::post('/create_hotel_inventory_new', [HotelInventoryController::class, 'createNewHotelInventory']);
    Route::get('/fetch_all_inventories', [HotelInventory::class, 'fetchAllInventories']);




    /* RoomRate Controller Routes ----------  */
    Route::get('/get-roomrate-data', [RoomRateController::class, 'index']);
    Route::post('/create-new-roomrate', [RoomRateController::class, 'createNewRoomRate']);
    Route::get('/fetch-roomrate-data-byid/{id}', [RoomRateController::class, 'findRoomRateById']);
    Route::post('/update-roomrate-data/{id}', [RoomRateController::class, 'updateRoomRateData']);
    Route::get('/fetch-roomrate-hotel', [RoomRateController::class, 'getRoomRateDataWithHotel']);
    Route::post('/create_new_room_rate', [RoomRateController::class, 'createNewHotelRoomRate']);

    /* ServiceRate Controller Routes ----------  */
    Route::get('/get-servicerate-data', [ServiceRateController::class, 'index']);
    Route::post('/create-new-servicerate', [ServiceRateController::class, 'createNewServiceRate']);
    Route::get('/fetch-servicerate-data-byid/{id}', [ServiceRateController::class, 'fetchServiceDataById']);
    Route::post('/update-servicerate-data/{id}', [ServiceRateController::class, 'updateServiceRateData']);
    // Route::get('/fetch-roomrate-hotel', [ServiceRateController::class, 'getRoomRateDataWithHotel']);


    /* HotelDiscount Controller Routes ----------  */
    Route::get('/get-hoteldiscount-data', [HotelDiscountController::class, 'index']);
    Route::post('/create-new-hoteldiscount', [HotelDiscountController::class, 'createNewDiscount']);
    Route::get('/fetch-hoteldiscount-data-byid/{id}', [HotelDiscountController::class, 'fetchDiscountById']);
    Route::post('/update-discount-data/{id}', [HotelDiscountController::class, 'updateHotelDiscountData']);
    // Route::get('/fetch-roomrate-hotel', [ServiceRateController::class, 'getRoomRateDataWithHotel']);


    /* Hotel Vendor Controller Routes ----------  */
    Route::get('/get-hotelvendor-data', [HotelVendorController::class, 'index']);
    Route::post('/create-new-hotelvendor', [HotelVendorController::class, 'createNewVendorDetails']);
    Route::get('/fetch-hotelvendor-data-byid/{id}', [HotelVendorController::class, 'findVendorDetailsById']);
    Route::post('/update-hotelvendor-data/{id}', [HotelVendorController::class, 'updateVendorDetails']);
    Route::get('/get-hotelvendor-data-by-user/{id}', [HotelVendorController::class, 'getDetailsByUserId']);
    // Route::get('/fetch-roomrate-hotel', [ServiceRateController::class, 'getRoomRateDataWithHotel']);


    /* HotelTermsConditions Controller Routes ----------  */
    Route::get('/get-hoteltermscond-data', [HotelTermsConditionsController::class, 'index']);
    Route::post('/create-new-hoteltermscond', [HotelTermsConditionsController::class, 'createNewHotelTermsConditions']);
    Route::get('/fetch-hoteltermscond-data-byid/{id}', [HotelTermsConditionsController::class, 'findTermsCondDetailsById']);
    Route::post('/update-hoteltermscond-data/{id}', [HotelTermsConditionsController::class, 'updateTermsAndConditions']);
    // Route::get('/fetch-roomrate-hotel', [ServiceRateController::class, 'getRoomRateDataWithHotel']);

    /* ///////////////////////////////////////////////////////////////////////////////////////////// */

    /* ///////////////////////////////////////////////////////////////////////////////////////////// */

    /* **************************************Product Search Controller API Routes ***************************************** */
    /* ///////////////////////////////////////////////////////////////////////////////////////////// */

    Route::get('/essential-search-prod', [SearchController::class, 'mainSearchLanding']);
    Route::post('/search_product_by_image', [SearchController::class, 'productSearchByImage']);
    Route::get('/essential-search-prod-bymanu', [SearchController::class, 'essentialSearchFilterByManufacture']);
    Route::post('/test-route', [TestController::class, 'confirmBooking']);


    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    /* ************************************** Singapoor Cities ***************************************** */
    /* ///////////////////////////////////////////////////////////////////////////////////////////// */

    Route::get('/fetch-all-cities', [AddressController::class, 'getAllCities']);

    /* ------------------------------------------------------------------------------- */
    /* ------------------------------------------------------------------------------- */
    /* -------------------------------Hotel Beds API Routes--------------------------- */
    /* ------------------------------------------------------------------------------- */
    /* ------------------------------------------------------------------------------- */

    Route::post('/check-availability_hotelbeds-api', [HotelBedsController::class, 'checkAvailability']);



    Route::post('/update_hotels_status', [HotelBedsController::class, 'updateHotelsStatus']);


    Route::post('/confirm-booking_hotelbeds-api', [HotelBedsController::class, 'confirmBooking']);
    Route::post('/get-hoteldetails_hotelbeds-api', [HotelBedsController::class, 'getHotelDetails']);
    Route::get('/get-countries_hotelbeds-api', [HotelBedsController::class, 'getCountryList']);
    Route::post('/get-destinations_hotelbeds-api', [HotelBedsController::class, 'getDestinationList']);
    Route::get('/get-hoteldetailwithminprice_hotelbeds-api', [HotelBedsController::class, 'getHotelListMinPriceHotelBeds']);
    Route::get('/get-hoteldetailwithminprice_hotelbeds-api_v2', [HotelBedsController::class, 'getHotelListMinPriceHotelBedsVersion2']);
    Route::get('/get-hoteldetailsbyid_hotelbeds-api/{id}', [HotelBedsController::class, 'getHotelByIdHotelBeds']);
    Route::post('/checkingroomavailability_hotelbeds-api', [HotelBedsController::class, 'getRoomAvailabilityByHotelCode']);
    Route::post('/booking-confirmationemail_hotelbeds-api/{id}', [HotelBedsController::class, 'emailRecipt']); //route working
    Route::post('/booking-cancellation_hotelbeds-api/{id}', [HotelBedsController::class, 'bookingCancellation']);
    Route::post('/get-availability-bygeolocation_hotelbeds-api', [HotelBedsController::class, 'filterHotelsByGeoLocation']);
    Route::post('/get-availability-byboardcode_hotelbeds-api', [HotelBedsController::class, 'getHotelByBoardCode']);
    Route::post('/get-hotel-rates-hotelbeds-api', [HotelBedsController::class, 'getHotelRates']);
    Route::post('/get_destination_wise_hotels', [HotelBedsController::class, 'getHotelByDestination']);

    Route::get('/get-user-currency', [HotelBedsController::class, 'getBookingsById']);

    Route::get('/get-hotel-details-by-user-current-loc/{lat}/{lon}', [HotelBedsController::class, 'availabilityBasedOnCurrentLocation']);
    Route::get('/get-hotelfacility-hotelbeds', [HotelBedsController::class, 'getHotelFacilities']);



    /* ------------------------------------------------------------------------------- */
    /* ------------------------------------------------------------------------------- */
    /* -------------------------------Hotel TBO API Routes--------------------------- */
    /* ------------------------------------------------------------------------------- */
    /* ------------------------------------------------------------------------------- */

    Route::get('/get-countrylist_hoteltbo-api', [HotelTBOController::class, 'getCountryList']);
    Route::get('/get-hoteldetails_hoteltbo-api', [HotelTBOController::class, 'getHotelDetails']);
    Route::get('/get-hotelcodes_hoteltbo-api', [HotelTBOController::class, 'getAllHotelCodesTBO']);
    Route::post('/get-searchrooms_hoteltbo-api', [HotelTBOController::class, 'searchRoomForAvailable']);
    Route::get('/get-hotelbyid_hoteltbo-api/{id}', [HotelTBOController::class, 'getHotelByIdTBO']);
    Route::get('/get-hoteldetailsminprice_hoteltbo_api', [HotelTBOController::class, 'getHotelDetailsWithMinPrice']);
    Route::post('/room_prebooking_hoteltbo_api', [HotelTBOController::class, 'preBookHotelTbo']);
    Route::post('/room_booking_hoteltbo_api', [HotelTBOController::class, 'bookHotelRoomTbo']);
    Route::post('/single_room_booking_hoteltbo_api', [HotelTBOController::class, 'singleBookHotelRoomTbo']);
    Route::get('/booking_confirmationemail_hoteltbo_api/{id}', [HotelTBOController::class, 'sendEmail']);

    Route::post('/update_hotels_status_tbo', [HotelTBOController::class, 'updateHotelsStatus']);

    Route::post('/booking-cancellation-hoteltbo', [HotelTBOController::class, 'bookingCancellationTbo']);


    Route::get('/fetch-mainsub_frommain-cat/{id}', [SubMainCategorySubController::class, 'getCategory2ByCategory1']);
    Route::get('/fetch-mainsub2_frommain-cat/{id}', [SubMainCategorySubController::class, 'getCategory3ByCategory2']);



    /* ------------------------------------------------------------------------------- */
    /* ------------------------------------------------------------------------------- */
    /* -------------------------------Hotel META API Routes--------------------------- */
    /* ------------------------------------------------------------------------------- */
    /* ------------------------------------------------------------------------------- */
    Route::get('/get_hotel_meta_hotels/{lat}/{lon}', [HotelMetaController::class, 'index']);
    Route::post('/fetch_single_hotel_rates/{id}', [HotelMetaController::class, 'fetchRatesForEachHotel']);
    Route::post('/search_hotel_by_latlon', [HotelMetaController::class, 'getHotelsByLatLon']);
    Route::post('/feed_rates_for_main_page', [HotelMetaController::class, 'rateDataFeed']);


    /* ------------------------------------------------------------------------------- */
    /* ------------------------------------------------------------------------------- */
    /* ******************** Appleholidays Hotel Bookings Routes  ***********************/
    /* --------------------------------------------------------------------------------- */
    /* -------------------------------------------------------------------------------- */

    Route::post('/hotels_preBooking', [HotelsPreBookings::class, 'addPreBooking']);

    Route::post('/single_hotels_preBooking', [HotelsPreBookings::class, 'singleHotelCheckout']);
    Route::post('/apple_booking_availability__applehotels/{id}', [BookingController::class, 'checkingAvailability']);
    Route::post('/apple_booking_confirm__applehotels/{id}', [BookingController::class, 'confirmBookingApple']);
    Route::get('/apple_booking_confirm_sendemail__applehotels/{id}', [BookingController::class, 'sendConfirmationEmail']);
    Route::post('/apple_booking_cancellation__applehotels/{id}', [BookingController::class, 'bookingCancellationRequest']);
    Route::post('/apple_booking_ammend__applehotels/{id}', [BookingController::class, 'ammendBooking']);
    Route::post('/update_hotels_status_aahaas', [BookingController::class, 'validateHotelBooking']);


    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    /* ************************************** Brands ***************************************** */
    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    Route::post('/create-new-brand', [BrandsController::class, 'createNewBrand']);
    Route::get('/fetch-all-brands', [BrandsController::class, 'getAllBrands']);


    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    /* ************************************** Payment Options  ***************************************** */
    /* ///////////////////////////////////////////////////////////////////////////////////////////// */

    Route::get('/fetch-all-payment-options', [PaymentOptionsController::class, 'getAllOptions']);

    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    /* **************************************  Product Details Listing  ***************************************** */
    /* ///////////////////////////////////////////////////////////////////////////////////////////// */

    Route::post('/create-new-product-detail', [ProductDetailsController::class, 'createListingDetails']);

    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    /* **************************************  Life Styles Routes  ***************************************** */
    /* ///////////////////////////////////////////////////////////////////////////////////////////// */


    Route::get('/get-all-life-styles-by-id/{id}', [LifeStylesController::class, 'getLifeStylesByID']);

    Route::get('/get-all-life-styles/{category1}/{category2}/{category3}/{category4}/{latlon}/{radius}/{limit}', [LifeStylesController::class, 'get_lifestyles']);

    Route::post('/add-new-life-styles-booking', [LifeStyleBookingController::class, 'addNewLifeStyleBooking']);

    Route::post('/uploadexcel', [ExcelController::class, 'uploadExcel']);

    /* ####################################### */
    Route::post('/hotel-bulk-upload', [HotelBulkController::class, 'hotelBulkUpload']);
    /* ####################################### */

    /* #############################  Life Style Vendor Controller ############################## */
    Route::get('/get-life-styles', [LifeStyleVendorController::class, 'getLifeStyleTypes']);
    Route::post('/createnewlifestylevendor', [LifeStyleVenController::class, 'createNewLifeStyleVendor']);


    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    /* **************************************  Education  ***************************************** */
    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    Route::post('/upload-education-lesson', [VideoController::class, 'uploadEducationVideo']);
    Route::post('/add-new-education-listing', [EducationListingsController::class, 'createEducationListing']);
    Route::post('/create-new-education-details', [EducationListingsController::class, 'createEducationDetails']);
    Route::post('/update-blackout-days', [EducationListingsController::class, 'updateBlackoutDays']);
    Route::get('/get-all-education-vendors', [EducationVendorController::class, 'getAllEducationVendors']);
    Route::get('/get-education-course-names', [EducationListingsController::class, 'getEducationCourseNames']);
    Route::post('/create-newedu-vendor', [EducationVendorController::class, 'createNewEduVendor']);
    Route::get('/get-education-service-locations/{id}', [EducationListingsController::class, 'getEducationServiceLocations']);
    Route::post('/get-education-sessions-by-lesson-id', [EducationSessionsController::class, 'getEducationSessionByLessonID']);
    Route::get('/get-education-resources-by-teacher-id/{id}', [VideoController::class, 'getEducationVideosByTeacherID']);
    Route::post('/add-new-education-inventory', [EducationInventoryController::class, 'addNewEducationInventory']);
    Route::post('/add-new-education-session', [EducationSessionsController::class, 'addNewEducationSession']);
    Route::post('/educationLinkUpdate', [EducationSessionsController::class, 'eduction_lesson_link_update']);


    Route::post('/add-education-booking', [EducationListingsController::class, 'addEducationBooking']);
    Route::get('/get-inventory-ids-by-listing-id/{id}', [EducationInventoryController::class, 'getInventoryIds']);
    Route::post('/get-time-slots-by-date', [EducationSessionsController::class, 'getTimeSlotsByDate']);
    Route::post('/get-time-slots-by-sessionid', [EducationSessionsController::class, 'getTimeSlotsBySession']);
    // Route::get('/get-all-educations', [EducationListingsController::class, 'getAllEducations']);

    Route::get('/get-all-educations/{category1}/{category2}/{category3}/{category4}', [EducationListingsController::class, 'getAllEducations']);

    Route::get('/get-all-educations-by-id/{id}', [EducationListingsController::class, 'getAllEducationsByID']);
    Route::post('/get-session-video-by-lesson-id', [EducationListingsController::class, 'getSessionVideoByLessonID']);



    // **** GET PUBLIC IP ****//
    Route::post('/getuserip', [AuthController::class, 'getUserCurrentLocation']);


    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    /* **************************************  Zoom Meeting  ***************************************** */
    /* ///////////////////////////////////////////////////////////////////////////////////////////// */

    Route::get('/meeting-list_zoom', [ZoomMeetingController::class, 'list']);
    Route::post('/create-meeting_zoom', [ZoomMeetingController::class, 'create']);

    Route::get('/meeting-by-zoom-id/{educationID}/{meetingID}', [ZoomMeetingController::class, 'getMeetingByID']);
    Route::get('/meeting-by-room-id_zoom/{id}', [ZoomMeetingController::class, 'get']);
    Route::patch('/update-meeting_zoom/{id}', [ZoomMeetingController::class, 'update']);
    Route::delete('/remove-meeting_zoom/{id}', [ZoomMeetingController::class, 'delete']);

    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
    // **** *********************************  Sabre Flights Routes  ************************************ **** //
    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //

    Route::post('/get-token', [SabreFlightController::class, 'getToken']);
    Route::post('/check-availability_Sabre_Flight', [SabreFlightController::class, 'checkFlightAvailability']);

    Route::get('/get-airport-codes', [AirportCodesController::class, 'getAirportCodes']);

    Route::post('/revalidating_Sabre_Flight', [SabreFlightController::class, 'reValidatingFlightDetails']);
    Route::post('/revalidating_Sabre_Flight_RT_MC', [SabreFlightController::class, 'reValidatingRTMC']);



    Route::post('/confirm-booking_Sabre_Flight', [SabreFlightController::class, 'confirmBooking']);
    Route::post('/cancel-booking_Sabre_Flight/{id}', [SabreFlightController::class, 'cancelFlightBooking']);
    Route::get('/airlineticket', [SabreFlightController::class, 'ticketview']);
    Route::post('/get-booking-details', [SabreFlightController::class, 'getBookingDetails']);

    //Flight Pre booking route
    Route::post('/flight_pre_booking_', [SabreFlightController::class, 'createPreBooking']);
    Route::get('/get_flight_pre_booking_data/{id}', [SabreFlightController::class, 'getFlightPreBookingData']);


    Route::post('/getTicket_v4', [SabreFlightController::class, 'getTicket']);
    Route::post('/decrtyp-value', [TestController::class, 'decryptEmail']);

    // Route::post('/request-payment-url', [PaymentController::class, 'requestPaymentUrl']);
    // Route::get('/get-payment-response/{payId}', [PaymentController::class, 'getPaymentResponse']);
    // Route::post('/get-payment-recipt/{paymentId}', [PaymentController::class, 'getPaymentRecipt']);

    // **** //////////////////////////New Payment URL Controller Routes///////////////////////////////////// **** //
    Route::post('/request-payment-url', [NewPaymentIPGController::class, 'createNewPaymentLink']);
    Route::post('/request-payment-url-mobile', [NewPaymentIPGController::class, 'createNewPaymentLinkMobile']);
    Route::get('/payment_checkout_&&/{sessionid}/{versionid}/{orderid}', [NewPaymentIPGController::class, 'getPaymentCheckout']);
    Route::post('/get-payment-response/{payId}', [NewPaymentIPGController::class, 'getPaymentRes']);


    Route::get('/all-currencies', [CurrencyController::class, 'getAllCurrecncies']);
    Route::get('/get-currency-byid/{currId}', [CurrencyController::class, 'getCurrencyById']);
    // Auth::routes(['verify' => true]);
    // Auth::routes(['verify' => true]);

    Route::post('/customer-new-shipping-address', [ShippingAddressController::class, 'createNewShippingAddress']);
    Route::get('/get-customer-shipping-address/{id}', [ShippingAddressController::class, 'getShippingDataByUser']);
    Route::post('/delete-shipping-data/{id}', [ShippingAddressController::class, 'deleteShippingData']);

    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
    // **** *********************************  Customer All Orders/Types Routes  ************************ **** //
    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //

    Route::get('/get-ess-orders/{id}/{catID}', [CustomerOrdersController::class, 'fetchEssentialsByCusId']);
    Route::get('/get-edu-orders/{id}', [CustomerOrdersController::class, 'fetchEducationByCusId']);
    Route::get('/get-ls-orders/{id}', [CustomerOrdersController::class, 'fetchLifeStyleCusId']);
    Route::get('/get-hotel-orders/{id}', [CustomerOrdersController::class, 'fetchHotelCusId']);
    Route::get('/fetch-all-order-userwise/{id}/{status}', [CustomerOrdersController::class, 'fetchAllOrderByUserId']);
    Route::get('/fetch-order-info-orderidwise/{id}', [CustomerOrdersController::class, 'getDetailsByOrderId']);
    Route::get('/fetch-order-status-count/{id}',  [CustomerOrdersController::class, 'getStatusCountByUserId']);

    //Mobile Api Routes
    Route::get('/getCustomerCardData/{id}', [CustomerOrdersController::class, 'getCustomerCardData']);
    Route::get('/getCustomerCancelledOrders/{id}', [CustomerOrdersController::class, 'getCustomerCancelledOrders']);
    Route::get('/getCustomerRecentOrders/{id}', [CustomerOrdersController::class, 'getCustomerRecentOrders']);
    Route::get('/getOrderDetailsByOrderID/{oid}', [CustomerOrdersController::class, 'getOrderDetailsByCusIDOrderID']);



    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
    // **** *********************************  Customer Notification Routes  ************************ **** //
    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //

    Route::get('/get-ess-notifications/{id}/{catID}', [CustomerNotificationController::class, 'fetchEssentialsNotifications']);
    Route::get('/get-ls-notifications/{id}', [CustomerNotificationController::class, 'fetchLifeStyleNotifications']);
    Route::get('/get-edu-notifications/{id}', [CustomerNotificationController::class, 'fetchEducationNotifications']);
    Route::get('/get-upcoming-education-sessions/{id}', [CustomerNotificationController::class, 'getUserUpcomingEducationSessions']);

    Route::get('/get-reminders/{id}', [CustomerNotificationController::class, 'getReminders']);
    Route::get('/get-hotels-notifications/{id}', [CustomerNotificationController::class, 'fetchHotelsNotifications']);


    Route::get('/push_notification/{id}', [CustomerNotificationController::class, 'pushNotifications']);
    // Route::get('/get-hotel-orders/{id}', [CustomerOrdersController::class, 'fetchHotelCusId']);

    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
    // **** *********************************  Search Controller Routes  ************************ **** //
    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
    Route::get('/mainDataSearch/{search}', [CustomerSearchController::class, 'searchEssentialProducts']);
    Route::post('/productSearchBy/{type}', [CustomerSearchController::class, 'productSearchByImage']);


    Route::get('/getPromotionOffers/{type}/{mainCategory}/', [CustomerSearchController::class, 'getPromotionOffers']);

    Route::get('/getAllPromotionOffers/{type}/{mainCategory}/{subCategory}', [CustomerSearchController::class, 'getAllPromotionOffers']);



    Route::post('/search-lifestyle', [CustomerSearchController::class, 'searchLifeStyleProducts']);
    // **** *********************************  Order Feedback Routes  ************************ **** //
    Route::post('/create-new-order-feedback', [OrderFeedbackController::class, 'createdOrderFeedBack']);

    // **** *********************************  Order Cancellation Routes  ************************ **** //
    Route::post('/create-new-order-cancellation', [CancelOrderController::class, 'cancelOrReturnOrder']);

    // ******************** Main Check out Controller Routes *******************
    Route::get('/get-main-customer-checkout-orders/{userid}', [MainCheckoutController::class, 'fetchOrderDataByUser']);



    Route::post('/create-checkout', [PaymentController::class, 'checkout']);

    Route::get('/checkout/success', [PaymentController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [PaymentController::class, 'cancel'])->name('checkout.cancel');

    Route::post('/create-checkout-order', [PaymentController::class, 'createCheckout']);

    Route::post('/cart-checkout-email/{orderid}', [CustomerCartCheckout::class, 'sendCartEmail']);

    Route::post('/create-new-class-request', [ClassRequestController::class, 'createNewClassRequest']);


    Route::post('/create-new-online-transfer', [OnlineTransferController::class, 'createNewOnlineTransfer']);

    /* -- ################################### RecentSearchController ROUTES ######################################## -- */

    Route::post('get_user_search_history', [RecentSearchController::class, 'getUserHistoryByUser']);
    Route::post('create_user_search_history', [RecentSearchController::class, 'createNewUserSearchRow']);
    Route::post('remove_user_search_history', [RecentSearchController::class, 'removeSearchHistoryByUser']);

    /* -- ################################### Product Review Controller ROUTES ######################################## -- */
    Route::post('/create_new_customer_prod_review', [ProductReviewController::class, 'createNewProductReview']);
    Route::get('/get_product_wise_review/{id}/{cat_id}', [ProductReviewController::class, 'fetchProducWiseReviews']);

    Route::get('/delete_review/{id}', [ProductReviewController::class, 'deleteReview']);

    /* -- ################################################################################################### -- */
    /* -- ################################################################################################### -- */
    /* -- ################################### ADMIN DASHBOARD ROUTES ######################################## -- */
    /* -- ################################################################################################### -- */
    /* -- ################################################################################################### -- */

    Route::get('/total_order_count', [DashboardController::class, 'fetchAllOrderCount']);
    Route::get('/total_hotel_count', [DashboardController::class, 'fetchActiveHotelCount']);
    Route::get('/total_cx_count', [DashboardController::class, 'fetchAllActiveCustomers']);

    /* -- ORDER DASHBOARD ROUTES -- */
    Route::get('/get_categorywise_order_count', [OrderDashboardController::class, 'getCategoryWiseOrderCount']);
    Route::get('/get_essential_recent_orders', [OrderDashboardController::class, 'getRecentEssentialOrders']);
    Route::get('/get_nonessential_recent_orders', [OrderDashboardController::class, 'getRecentNonEssentialOrders']);
    Route::get('/get_lifestyle_recent_orders', [OrderDashboardController::class, 'getRecentLifeStyleOrders']);
    Route::get('/get_education_recent_orders', [OrderDashboardController::class, 'getRecentEducationOrders']);
    Route::get('/get_all_essnoness_orders', [OrderDashboardController::class, 'getEssNonEsAllOrders']);

    Route::post('/get_all_payemnt_transaction_data', [OrderDashboardController::class, 'getOrderPayTransaction']);
    Route::post('/change_order_delivery_status', [OrderDashboardController::class, 'changeOrderStatus']);
    Route::get('/fetch_flight_reservation', [OrderDashboardController::class, 'fetchAllFLightReservations']);


    /* -- DELIVERY RATE DASHBOARD ROUTES -- */
    Route::get('/get_delivery_rates', [DeliveryRateController::class, 'getAllDeliveryRates']);
    Route::post('/create_new_delivery_rate', [DeliveryRateController::class, 'createNewDeliveryRate']);
    Route::post('/update_ext_delivery_rate', [DeliveryRateController::class, 'updateDeliveryRate']);


    /* -- DRIVER ALLOCATION/REGISTRATION DASHBOARD ROUTES -- */
    Route::post('/create_new_driver', [DriverRegController::class, 'createNewDriverUser']);
    Route::get('/get_registered_drivers', [DriverRegController::class, 'getDriversAll']);


    /* -- MAP DASHBOARD ROUTES -- */
    Route::get('/get_map_map/{lat}/{long}', [MapController::class, 'getmap']);

    Route::get('get-customer-cart-data-length/{id}', [ProductCartController::class, 'getCartDataLength']);


    /* -- LIFESTYLES DASHBOARD ROUTES -- */
    Route::post('/create_new_life_style', [LifeStyleCreatorController::class, 'createLifeStyle']);
    Route::get('/get_main_sub_categories', [LifeStyleCreatorController::class, 'getSubCategories']);
    Route::get('/get_life_style_details_data', [LifeStyleCreatorController::class, 'getLifeStyleDetailsData']);
    Route::post('/create_new_life_style_details', [LifeStyleCreatorController::class, 'createLifeStyleDetails']);
    Route::post('/create_life_style_inventory', [LifeStyleCreatorController::class, 'createNewLifeStyleInventory']);
    Route::get('/fetch_all_lifestyle_data', [LifeStyle::class, 'fetchAllLifeStyle']);

    Route::get('/get_lifestyle_inventories_by_id/{id}', [LifeStyle::class, 'fetchAllLifestylesInventoriesByID']);
    Route::post('/create_life_style_rate', [LifeStyleCreatorController::class, 'createNewLifeStyleRate']);



    /* -- TESTING ROUTES -- */
    Route::get('/get_currency/{value}', [Currencies::class, 'getCurrency']);
    Route::post('/convert_currency/{value}/{value1}', [Currencies::class, 'convertCurrency']);
    Route::post('/check_email', [SellerController::class, 'sendEmail']);
    // Route::post('/aahaas_hotel_push_meta', [CommonHotel_APIController::class, 'pushAahaasHotel']);
    Route::post('/hotel_meta_data_push', [HotelMeta::class, 'createHotelDetailsBeds']);

    Route::get('/get_hotel_distance', [HotelMeta::class, 'gethotelDistance']);





    //Routes For Dashboard (Lifestyle)
    Route::get('/get_lifestyle_orders/{id}', [LifeStylesController::class, 'getLifestyleOrders']);
    Route::get('/get_lifestyle_orders_by_checkout_id/{checkout_id}', [LifeStylesController::class, 'getLifestyleOrdersByCheckoutId']);






    //--------------------TBO_Hotels
    Route::post('/get_hotels_by_latlon', [TBOController::class, 'getHotelsByLatLon']);
    Route::post('/check', [TBOController::class, 'generateTBOToken']);
    Route::post('/hotelBlockRoom', [TBOController::class, 'hotelBlockRoom']);
    Route::post('/get_hotel_details/{id}/{provider}/{status}', [TBOController::class, 'hotelsDetails']);
    Route::post('/reValidate_cart_hotel_before_booking', [TBOController::class, 'reValidateCartHotelBeforeBooking']);
    Route::post('/add_hotel_to_cart', [HotelsBooking::class, 'hotelsPreBooking']);
    Route::post('/revalidate_on_checkout', [TBOController::class, 'revalidateWhenCheckout']);
    Route::post('/hotel_tbo_booking', [TBOController::class, 'hotelBooking']);

    Route::post('/search_tbo_hotels', [TBOController::class, 'searchHotels']);



    //-------------------Hotel_Ahs
    Route::post('/get_hotel_rates/{id}', [TBOController::class, 'getHotelRates']);
    Route::post('/update_hotel_status_cart', [TBOController::class, 'updateHotelStatusCart']);

    Route::get('/groupRatesLifestyles', [TBOController::class, 'groupRates']);




    //--------------------------FirebaseFCM

    Route::post('/save_fcm_tokens', [UserController::class, 'saveFCMTokens']);
});
