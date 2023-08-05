<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ForgotController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\Currency\CurrencyController;
use App\Http\Controllers\Customer\CustomerOrdersController;
use App\Http\Controllers\Customer\OrderFeedbackController;
use App\Http\Controllers\Customer\SearchController as CustomerSearchController;
use App\Http\Controllers\Customer\ShippingAddressController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\MainCategoryController;
use App\Http\Controllers\SubMainCategoryController;
use App\Http\Controllers\SubMainCategorySubController;
use App\Http\Controllers\SubMiniCategoryController;
use App\Http\Controllers\ProductListingController;
use App\Http\Controllers\DiscountTypeController;
use App\Http\Controllers\Education\EducationInventoryController;
use App\Http\Controllers\Education\EducationListingsController;
use App\Http\Controllers\Education\EducationSessionsController;
use App\Http\Controllers\Education\EducationVendorController;
use App\Http\Controllers\Education\VideoController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\Flights\AirportCodesController;
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
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\Sabre\SabreFlightController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ZoomMeeting\ZoomMeetingController;
use App\Models\Education\EducationListings;
use App\Models\Education\EducationSessions;

use Illuminate\Support\Facades\Auth;

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
});



Route::group(['middleware' => 'api'], function () {
    // Route::post('/login-user', ['as' => 'login', 'uses' => 'AuthController@userLoginWeb']);
    Route::post('/login-user', [AuthController::class, 'userLoginWeb'])->name('login');

    Route::post('/new-user-registration', [AuthController::class, 'registerUser'])->name('userregistration');


    // Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationEmail'])->middleware('auth:sanctum');
    Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');

    /* User Controller Routes ----------  */
    // Route::get('/get-all-user', [UserController::class, 'index']);
    Route::get('/get-user-by-id/{id}', [UserController::class, 'getUserById']);
    Route::post('/update-user/{id}', [UserController::class, 'updateUserData']);
    Route::delete('/remove-user/{id}', [UserController::class, 'userDeletion']);

    /* Auth Controller Routes ----------  */
    Route::post('/google-login-user', [AuthController::class, 'getGoogleUserData']);
    Route::post('/facebook-login-user', [AuthController::class, 'getFacebookUserData']);
    Route::get('/get-existing-user/{id}', [AuthController::class, 'googleUserDataCheck']);
    Route::get('/get-existing-user-facebook/{id}', [AuthController::class, 'googleUserDataCheck']);
    Route::post('/mobile-user', [AuthController::class, 'mobileUserCreation']);
    Route::get('/authenicate-user-byid/{id}', [AuthController::class, 'getCurrentUserById']);
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route::get('/user-verify-view', [AuthController::class, 'verifyMail']);


    /* Customer Controller Routes ----------  */
    Route::get('/get-all-customers', [CustomerController::class, 'index']);
    Route::get('/generate-cx-auto-id', [CustomerController::class, 'generateCustomerId']);
    Route::post('/create-new-customer', [CustomerController::class, 'registerNewCustomer']);
    Route::get('/get-customers-count', [CustomerController::class, 'getCustomerCount']);
    Route::get('/get-customer-data-byid/{id}', [CustomerController::class, 'getCustomerDataById']);
    Route::post('/update-customer-profile/{id}', [CustomerController::class, 'updateCustomerProfile']);

    /* Forgot Controller Routes ----------  */
    Route::post('/forgot-password', [ForgotController::class, 'forgotPassword']);
    Route::post('/userreset', [ForgotController::class, 'resetPassword']);

    // Route::post('/forgot-view', [ForgotController::class, 'fogotview']);

    /* ---- ---- ---- ---- ---- ---- */

    /* Address Controller Routes ----------  */
    Route::post('/create-address', [AddressController::class, 'createAddress']);
    Route::post('/update-address/{id}', [AddressController::class, 'updateAddress']);

    /* Seller Controller Routes ----------  */
    Route::get('/fetch-all-sellers', [SellerController::class, 'index']);
    Route::post('/create-new-seller', [SellerController::class, 'createSeller']);
    Route::get('/generate-seller-auto-id', [SellerController::class, 'generateSellerAutoId']);
    Route::post('/seller-activation/{id}', [SellerController::class, 'sellerActivation']);
    Route::get('/seller-by-id/{id}', [SellerController::class, 'getSellerDetailsById']);

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

    Route::post('/check-inventory-availability', [ProductListingController::class, 'confirmProductOrder']);

    Route::get('/get-list-data', [ProductListingController::class, 'getProductListingData']);


    Route::get('/get-product-inventory/{id}', [ProductListingController::class, 'getProductInventory']);
    Route::get('/get-list-data-with-discount/{id}/{limit}', [ProductListingController::class, 'getProdListingWithDicounts']);
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
    Route::get('/get-prodviewdata-byid/{id}', [ProductViewController::class, 'viewProductDataById']);
    Route::get('/get-variations-byid/{id}', [ProductViewController::class, 'getVariationsByID']);
    Route::get('/get-variation1-byid/{id}', [ProductViewController::class, 'sqlGroupByVariationOne']);
    Route::get('/get-variation2-byid/{id}', [ProductViewController::class, 'sqlGroupByVariationTwo']);
    Route::get('/get-variation3-byid/{id}', [ProductViewController::class, 'sqlGroupByVariationThree']);
    Route::get('/get-variant1-byid/{id}', [ProductViewController::class, 'sqlGroupByVariantOne']);
    Route::get('/get-variant2-byid/{id}', [ProductViewController::class, 'sqlGroupByVariantTwo']);
    Route::get('/get-variant3-byid/{id}', [ProductViewController::class, 'sqlGroupByVariantThree']);
    Route::get('/get-variant-byvariation/{id}', [ProductViewController::class, 'getVariantByVariation']);

    /* ProductCart Controller Routes ----------  */
    Route::get('/add-prod-to-cart', [ProductCartController::class, 'productsAddToCart']);
    Route::post('/add-to-cart', [ProductCartController::class, 'addCart']);
    Route::post('/create-new-cart', [ProductCartController::class, 'createNewCart']);
    Route::get('/get-carts/{id}', [ProductCartController::class, 'getCarts']);
    Route::get('/get-customer-cart-data/{id}', [ProductCartController::class, 'getCartData']);
    Route::get('/get-pre-defined-orders/{id}', [ProductCartController::class, 'getCustomerPreDefineCartOrders']);

    Route::post('/create-new-order', [ProductCartController::class, 'newOrder']);

    Route::post('/get-product-cart-availability-by-customer', [ProductCartController::class, 'getCustomerCarts']);

    Route::post('/delete-customer-cart-item', [ProductCartController::class, 'deleteCustomerCart']);
    Route::post('/delete-customer-cart', [ProductCartController::class, 'deleteAllCartData']);


    /* Hotel Controller Routes ----------  */
    Route::get('/get-hotel-data', [HotelController::class, 'index']);
    Route::post('/create-new-hotel', [HotelController::class, 'createNewHotel']);
    Route::get('/get-hotel-byid/{id}', [HotelController::class, 'fecthHotelById']);
    Route::post('/update-hotel-details/{id}', [HotelController::class, 'updateHotelDataById']);
    Route::get('/get_hotel_lowest_details', [HotelController::class, 'getLowestHotels']);

    Route::get('/get_hotel_roomdetails_by_id/{id}', [HotelController::class, 'getRoomCategoryDetailsById']);


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






    /* RoomRate Controller Routes ----------  */
    Route::get('/get-roomrate-data', [RoomRateController::class, 'index']);
    Route::post('/create-new-roomrate', [RoomRateController::class, 'createNewRoomRate']);
    Route::get('/fetch-roomrate-data-byid/{id}', [RoomRateController::class, 'findRoomRateById']);
    Route::post('/update-roomrate-data/{id}', [RoomRateController::class, 'updateRoomRateData']);
    Route::get('/fetch-roomrate-hotel', [RoomRateController::class, 'getRoomRateDataWithHotel']);

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
    Route::post('/confirm-booking_hotelbeds-api', [HotelBedsController::class, 'confirmBooking']);
    Route::get('/get-hoteldetails_hotelbeds-api', [HotelBedsController::class, 'getHotelDetails']);
    Route::get('/get-countries_hotelbeds-api', [HotelBedsController::class, 'getCountryList']);
    Route::get('/get-destinations_hotelbeds-api', [HotelBedsController::class, 'getDestinationList']);
    Route::get('/get-hoteldetailwithminprice_hotelbeds-api', [HotelBedsController::class, 'getHotelListMinPriceHotelBeds']);
    Route::get('/get-hoteldetailsbyid_hotelbeds-api/{id}', [HotelBedsController::class, 'getHotelByIdHotelBeds']);
    Route::post('/checkingroomavailability_hotelbeds-api', [HotelBedsController::class, 'getRoomAvailabilityByHotelCode']);
    Route::post('/booking-confirmationemail_hotelbeds-api/{id}', [HotelBedsController::class, 'emailRecipt']);
    Route::post('/booking-cancellation_hotelbeds-api/{id}', [HotelBedsController::class, 'bookingCancellation']);
    Route::post('/get-availability-bygeolocation_hotelbeds-api', [HotelBedsController::class, 'filterHotelsByGeoLocation']);
    Route::post('/get-availability-byboardcode_hotelbeds-api', [HotelBedsController::class, 'getHotelByBoardCode']);
    Route::post('/get-hotel-rates-hotelbeds-api', [HotelBedsController::class, 'getHotelRates']);

    Route::get('/get-user-currency', [HotelBedsController::class, 'getBookingsById']);
    Route::get('/get-email', [HotelBedsController::class, 'email']);


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
    Route::get('/booking_confirmationemail_hoteltbo_api/{id}', [HotelTBOController::class, 'sendEmail']);


    Route::get('/fetch-mainsub_frommain-cat/{id}', [SubMainCategorySubController::class, 'getCategory2ByCategory1']);
    Route::get('/fetch-mainsub2_frommain-cat/{id}', [SubMainCategorySubController::class, 'getCategory3ByCategory2']);


    /* ------------------------------------------------------------------------------- */
    /* ------------------------------------------------------------------------------- */
    /* ******************** Appleholidays Hotel Bookings Routes  ***********************/
    /* --------------------------------------------------------------------------------- */
    /* -------------------------------------------------------------------------------- */

    Route::post('/hotels_preBooking', [HotelsPreBookings::class, 'addPreBooking']);
    Route::post('/apple_booking_availability__applehotels/{id}', [BookingController::class, 'checkingAvailability']);
    Route::post('/apple_booking_confirm__applehotels/{id}', [BookingController::class, 'confirmBookingApple']);
    Route::post('/apple_booking_confirm_sendemail__applehotels/{id}', [BookingController::class, 'sendConfirmationEmail']);
    Route::post('/apple_booking_cancellation__applehotels/{id}', [BookingController::class, 'bookingCancellationRequest']);
    Route::post('/apple_booking_ammend__applehotels/{id}', [BookingController::class, 'ammendBooking']);


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
    Route::get('/get-all-life-styles', [LifeStylesController::class, 'get_lifestyles']);
    Route::post('/add-new-life-styles-booking', [LifeStyleBookingController::class, 'addNewLifeStyleBooking']);
    Route::post('/uploadexcel', [ExcelController::class, 'uploadExcel']);

    /* #############################  Life Style Vendor Controller ############################## */
    Route::get('/get-life-styles', [LifeStyleVendorController::class, 'getLifeStyleTypes']);
    Route::post('/createnewlifestylevendor', [LifeStyleVenController::class, 'createNewLifeStyleVendor']);


    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    /* **************************************  Education  ***************************************** */
    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    Route::post('/upload-education-video-vimeo', [VideoController::class, 'uploadEducationVideo']);
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
    Route::get('/get-all-educations', [EducationListingsController::class, 'getAllEducations']);
    Route::get('/get-all-educations-by-id/{id}', [EducationListingsController::class, 'getAllEducationsByID']);
    Route::post('/get-session-video-by-lesson-id', [EducationListingsController::class, 'getSessionVideoByLessonID']);

    Route::get('/get-upcoming-education-sessions/{id}', [EducationListingsController::class, 'getUserUpcomingEducationSessions']);

    // **** GET PUBLIC IP ****//
    Route::post('/getuserip', [AuthController::class, 'getUserCurrentLocation']);


    /* ///////////////////////////////////////////////////////////////////////////////////////////// */
    /* **************************************  Zoom Meeting  ***************************************** */
    /* ///////////////////////////////////////////////////////////////////////////////////////////// */

    Route::get('/meeting-list_zoom', [ZoomMeetingController::class, 'list']);
    Route::post('/create-meeting_zoom', [ZoomMeetingController::class, 'create']);
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
    Route::post('/send-mail/{id}', [SabreFlightController::class, 'sendMailAirTicket']);


    Route::post('/decrtyp-value', [TestController::class, 'decryptEmail']);

    Route::post('/request-payment-url', [PaymentController::class, 'requestPaymentUrl']);
    Route::post('/get-payment-response', [PaymentController::class, 'getPaymentResponse']);
    Route::post('/get-payment-recipt/{paymentId}', [PaymentController::class, 'getPaymentRecipt']);


    Route::get('/all-currencies', [CurrencyController::class, 'getAllCurrecncies']);
    Route::get('/get-currency-byid/{currId}', [CurrencyController::class, 'getCurrencyById']);
    // Auth::routes(['verify' => true]);
    // Auth::routes(['verify' => true]);

    Route::post('/customer-new-shipping-address', [ShippingAddressController::class, 'createNewShippingAddress']);

    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
    // **** *********************************  Customer All Orders/Types Routes  ************************ **** //
    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //

    Route::get('/get-ess-orders/{id}', [CustomerOrdersController::class, 'fetchEssentialsByCusId']);
    Route::get('/get-edu-orders/{id}', [CustomerOrdersController::class, 'fetchEducationByCusId']);
    Route::get('/get-ls-orders/{id}', [CustomerOrdersController::class, 'fetchLifeStyleCusId']);
    Route::get('/get-hotel-orders/{id}', [CustomerOrdersController::class, 'fetchHotelCusId']);

    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
    // **** *********************************  Search Controller Routes  ************************ **** //
    // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
    Route::post('/search-essentials', [CustomerSearchController::class, 'searchEssentialProducts']);
    Route::post('/search-lifestyle', [CustomerSearchController::class, 'searchLifeStyleProducts']);

    // **** *********************************  Order Feedback Routes  ************************ **** //
    Route::post('/create-new-order-feedback', [OrderFeedbackController::class, 'createOrderFeedback']);
});


// // Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationEmail'])->middleware('auth:sanctum');
// Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');

// /* User Controller Routes ----------  */
// // Route::get('/get-all-user', [UserController::class, 'index']);
// Route::get('/get-user-by-id/{id}', [UserController::class, 'getUserById']);
// Route::post('/update-user/{id}', [UserController::class, 'updateUserData']);
// Route::delete('/remove-user/{id}', [UserController::class, 'userDeletion']);

// /* Auth Controller Routes ----------  */
// Route::post('/google-login-user', [AuthController::class, 'getGoogleUserData']);
// Route::post('/facebook-login-user', [AuthController::class, 'getFacebookUserData']);
// Route::get('/get-existing-user/{id}', [AuthController::class, 'googleUserDataCheck']);
// Route::get('/get-existing-user-facebook/{id}', [AuthController::class, 'googleUserDataCheck']);
// Route::post('/mobile-user', [AuthController::class, 'mobileUserCreation']);
// Route::get('/authenicate-user-byid/{id}', [AuthController::class, 'getCurrentUserById']);
// Route::post('/logout', [AuthController::class, 'logout']);
// // Route::get('/user-verify-view', [AuthController::class, 'verifyMail']);


// /* Customer Controller Routes ----------  */
// Route::get('/get-all-customers', [CustomerController::class, 'index']);
// Route::get('/generate-cx-auto-id', [CustomerController::class, 'generateCustomerId']);
// Route::post('/create-new-customer', [CustomerController::class, 'registerNewCustomer']);
// Route::get('/get-customers-count', [CustomerController::class, 'getCustomerCount']);
// Route::get('/get-customer-data-byid/{id}', [CustomerController::class, 'getCustomerDataById']);
// Route::post('/update-customer-profile/{id}', [CustomerController::class, 'updateCustomerProfile']);

// /* Forgot Controller Routes ----------  */
// Route::post('/forgot-password', [ForgotController::class, 'forgotPassword']);
// Route::post('/userreset', [ForgotController::class, 'resetPassword']);

// // Route::post('/forgot-view', [ForgotController::class, 'fogotview']);

// /* ---- ---- ---- ---- ---- ---- */

// /* Address Controller Routes ----------  */
// Route::post('/create-address', [AddressController::class, 'createAddress']);
// Route::post('/update-address/{id}', [AddressController::class, 'updateAddress']);
// Route::get('/get-addresses-by-id/{id}', [AddressController::class, 'getAddressByID']);

// /* Seller Controller Routes ----------  */
// Route::get('/fetch-all-sellers', [SellerController::class, 'index']);
// Route::post('/create-new-seller', [SellerController::class, 'createSeller']);
// Route::get('/generate-seller-auto-id', [SellerController::class, 'generateSellerAutoId']);
// Route::post('/seller-activation/{id}', [SellerController::class, 'sellerActivation']);
// Route::get('/seller-by-id/{id}', [SellerController::class, 'getSellerDetailsById']);

// /* Main Category Controller Routes ----------  */
// Route::get('/fetch-all-main-cat', [MainCategoryController::class, 'index']);
// Route::post('/create-new-main-cat', [MainCategoryController::class, 'createMainCategory']);
// Route::get('/generate-maincat-auto-id', [MainCategoryController::class, 'generateMainCatId']);

// /* Sub Main Category Controller Routes ----------  */
// Route::get('/fetch-all-sub-main-cat', [SubMainCategoryController::class, 'index']);
// Route::post('/create-new-sub-main-cat', [SubMainCategoryController::class, 'createSubMainCategory']);
// Route::get('/generate-submaincat-auto-id', [SubMainCategoryController::class, 'generateSubMainCatId']);

// /* Sub Main Sub Category Controller Routes ----------  */
// Route::get('/fetch-all-sub-mainsub-cat', [SubMainCategorySubController::class, 'index']);
// Route::post('/create-new-sub-mainsub-cat', [SubMainCategorySubController::class, 'createSubMainSubCategory']);
// Route::get('/generate-submaincsubat-auto-id', [SubMainCategorySubController::class, 'generateSubMainSubCatId']);

// /* Sub Mini Category Controller Routes ----------  */
// Route::get('/fetch-all-sub-mini-cat', [SubMiniCategoryController::class, 'index']);
// Route::post('/create-new-sub-min-cat', [SubMiniCategoryController::class, 'createSubMiniCategory']);
// Route::get('/generate-submini-auto-id', [SubMiniCategoryController::class, 'generateSubMiniCatId']);

// /* Product Listing Controller Routes ----------  */
// Route::get('/fetch-all-listings', [ProductListingController::class, 'index']);
// Route::get('/fetch-listings-seller/{id}', [ProductListingController::class, 'fetchSellerProducts']);
// Route::post('/create-new-listing', [ProductListingController::class, 'createNewListing']);
// Route::post('/create-variations', [ProductListingController::class, 'createVariations']);
// Route::post('/confirm-order', [ProductListingController::class, 'confirmProductOrder']);

// Route::post('/check-inventory-availability', [ProductListingController::class, 'confirmProductOrder']);

// Route::get('/get-list-data', [ProductListingController::class, 'getProductListingData']);


// Route::get('/get-product-inventory/{id}', [ProductListingController::class, 'getProductInventory']);
// Route::get('/get-list-data-with-discount/{id}/{mainId}/{subId}/{limit}', [ProductListingController::class, 'getProdListingWithDicounts']);
// // Route::get('/generate-listing-category-id', [ProductListingController::class, 'generateListingId']);
// // Route::get('/generate-listing-category-id', [ProductListingController::class, 'generateListingId']);

// /* Discount Listing Controller Routes ----------  */
// Route::get('/fetch-all-discounttypes', [DiscountTypeController::class, 'index']);
// Route::post('/create-new-discounttype', [DiscountTypeController::class, 'createNewDiscountType']);
// Route::post('/create-new-promotion', [PromotionsController::class, 'createNewListingPromotion']);

// /* Filter Categories Controller Routes ----------  */
// Route::get('/fetch-all-mainsub', [FilterController::class, 'getMainSubCategories']);
// Route::get('/fetch-all-submainsub', [FilterController::class, 'getSubMainSubCategories']);
// Route::get('/fetch-all-manufacture', [FilterController::class, 'getManufactures']);

// Route::get('/fetch-all-categories', [FilterController::class, 'getCategories']);

// /* ProductView Controller Routes ---------- */
// Route::get('/get-prodviewdata-byid/{id}', [ProductViewController::class, 'viewProductDataById']);
// Route::get('/get-variations-byid/{id}', [ProductViewController::class, 'getVariationsByID']);
// Route::get('/get-variation1-byid/{id}', [ProductViewController::class, 'sqlGroupByVariationOne']);
// Route::get('/get-variation2-byid/{id}', [ProductViewController::class, 'sqlGroupByVariationTwo']);
// Route::get('/get-variation3-byid/{id}', [ProductViewController::class, 'sqlGroupByVariationThree']);
// Route::get('/get-variant1-byid/{id}', [ProductViewController::class, 'sqlGroupByVariantOne']);
// Route::get('/get-variant2-byid/{id}', [ProductViewController::class, 'sqlGroupByVariantTwo']);
// Route::get('/get-variant3-byid/{id}', [ProductViewController::class, 'sqlGroupByVariantThree']);
// Route::get('/get-variant-byvariation/{id}', [ProductViewController::class, 'getVariantByVariation']);

// /* ProductCart Controller Routes ----------  */
// Route::get('/add-prod-to-cart', [ProductCartController::class, 'productsAddToCart']);
// Route::post('/add-to-cart', [ProductCartController::class, 'addCart']);
// Route::post('/create-new-cart', [ProductCartController::class, 'createNewCart']);
// Route::get('/get-carts/{id}', [ProductCartController::class, 'getCarts']);
// Route::get('/get-customer-cart-data/{id}', [ProductCartController::class, 'getCartData']);
// Route::get('/get-pre-defined-orders/{id}', [ProductCartController::class, 'getCustomerPreDefineCartOrders']);
// Route::post('/deleteCustomCart', [ProductCartController::class, 'deleteCart']);


// Route::post('/create-new-order', [ProductCartController::class, 'newOrder']);

// Route::post('/get-product-cart-availability-by-customer', [ProductCartController::class, 'getCustomerCarts']);

// Route::post('/delete-customer-cart-item', [ProductCartController::class, 'deleteCustomerCart']);
// Route::post('/delete-customer-cart', [ProductCartController::class, 'deleteAllCartData']);


// /* Hotel Controller Routes ----------  */
// Route::get('/get-hotel-data', [HotelController::class, 'index']);
// Route::post('/create-new-hotel', [HotelController::class, 'createNewHotel']);
// Route::get('/get-hotel-byid/{id}', [HotelController::class, 'fecthHotelById']);
// Route::post('/update-hotel-details/{id}', [HotelController::class, 'updateHotelDataById']);
// Route::get('/get_hotel_lowest_details', [HotelController::class, 'getLowestHotels']);

// Route::get('/get_hotel_roomdetails_by_id/{id}', [HotelController::class, 'getRoomCategoryDetailsById']);


// /* Hotel Detail Controller Routes ----------  */
// Route::get('/get-hoteldetails', [HotelDetailController::class, 'index']);
// Route::post('/create-new-hoteldetail', [HotelDetailController::class, 'createHotelDetails']);
// Route::get('/get-hoteldetail-byid/{id}', [HotelDetailController::class, 'fetchHotelDetailsById']);
// Route::post('/update-hotel-detailsdata/{id}', [HotelDetailController::class, 'updateHotelDetails']);
// Route::get('/fetch-hotel-joindata', [HotelDetailController::class, 'getHotelDetailsJoinData']);

// /* Hotel Inventory Controller Routes ----------  */
// Route::get('/get-inventory-data', [HotelInventoryController::class, 'index']);
// Route::post('/create-new-hotelinventory', [HotelInventoryController::class, 'createNewInventoryDetails']);
// Route::get('/fetch-hotel-invendata-byid/{id}', [HotelInventoryController::class, 'fetchDetailsById']);
// Route::post('/update-hotel-invendata/{id}', [HotelInventoryController::class, 'updateHotelInventoryDetails']);
// Route::get('/fetch-invendata-hotel', [HotelInventoryController::class, 'fetchDetailsWithHotelName']);






// /* RoomRate Controller Routes ----------  */
// Route::get('/get-roomrate-data', [RoomRateController::class, 'index']);
// Route::post('/create-new-roomrate', [RoomRateController::class, 'createNewRoomRate']);
// Route::get('/fetch-roomrate-data-byid/{id}', [RoomRateController::class, 'findRoomRateById']);
// Route::post('/update-roomrate-data/{id}', [RoomRateController::class, 'updateRoomRateData']);
// Route::get('/fetch-roomrate-hotel', [RoomRateController::class, 'getRoomRateDataWithHotel']);

// /* ServiceRate Controller Routes ----------  */
// Route::get('/get-servicerate-data', [ServiceRateController::class, 'index']);
// Route::post('/create-new-servicerate', [ServiceRateController::class, 'createNewServiceRate']);
// Route::get('/fetch-servicerate-data-byid/{id}', [ServiceRateController::class, 'fetchServiceDataById']);
// Route::post('/update-servicerate-data/{id}', [ServiceRateController::class, 'updateServiceRateData']);
// // Route::get('/fetch-roomrate-hotel', [ServiceRateController::class, 'getRoomRateDataWithHotel']);


// /* HotelDiscount Controller Routes ----------  */
// Route::get('/get-hoteldiscount-data', [HotelDiscountController::class, 'index']);
// Route::post('/create-new-hoteldiscount', [HotelDiscountController::class, 'createNewDiscount']);
// Route::get('/fetch-hoteldiscount-data-byid/{id}', [HotelDiscountController::class, 'fetchDiscountById']);
// Route::post('/update-discount-data/{id}', [HotelDiscountController::class, 'updateHotelDiscountData']);
// // Route::get('/fetch-roomrate-hotel', [ServiceRateController::class, 'getRoomRateDataWithHotel']);


// /* Hotel Vendor Controller Routes ----------  */
// Route::get('/get-hotelvendor-data', [HotelVendorController::class, 'index']);
// Route::post('/create-new-hotelvendor', [HotelVendorController::class, 'createNewVendorDetails']);
// Route::get('/fetch-hotelvendor-data-byid/{id}', [HotelVendorController::class, 'findVendorDetailsById']);
// Route::post('/update-hotelvendor-data/{id}', [HotelVendorController::class, 'updateVendorDetails']);
// // Route::get('/fetch-roomrate-hotel', [ServiceRateController::class, 'getRoomRateDataWithHotel']);


// /* HotelTermsConditions Controller Routes ----------  */
// Route::get('/get-hoteltermscond-data', [HotelTermsConditionsController::class, 'index']);
// Route::post('/create-new-hoteltermscond', [HotelTermsConditionsController::class, 'createNewHotelTermsConditions']);
// Route::get('/fetch-hoteltermscond-data-byid/{id}', [HotelTermsConditionsController::class, 'findTermsCondDetailsById']);
// Route::post('/update-hoteltermscond-data/{id}', [HotelTermsConditionsController::class, 'updateTermsAndConditions']);
// // Route::get('/fetch-roomrate-hotel', [ServiceRateController::class, 'getRoomRateDataWithHotel']);

// /* ///////////////////////////////////////////////////////////////////////////////////////////// */

// /* ///////////////////////////////////////////////////////////////////////////////////////////// */

// /* **************************************Product Search Controller API Routes ***************************************** */
// /* ///////////////////////////////////////////////////////////////////////////////////////////// */

// Route::get('/essential-search-prod', [SearchController::class, 'mainSearchLanding']);
// Route::get('/essential-search-prod-bymanu', [SearchController::class, 'essentialSearchFilterByManufacture']);
// Route::post('/test-route', [TestController::class, 'confirmBooking']);

// /* ///////////////////////////////////////////////////////////////////////////////////////////// */
// /* ************************************** Singapoor Cities ***************************************** */
// /* ///////////////////////////////////////////////////////////////////////////////////////////// */

// Route::get('/fetch-all-cities', [AddressController::class, 'getAllCities']);

// /* ------------------------------------------------------------------------------- */
// /* ------------------------------------------------------------------------------- */
// /* -------------------------------Hotel Beds API Routes--------------------------- */
// /* ------------------------------------------------------------------------------- */
// /* ------------------------------------------------------------------------------- */

// Route::post('/check-availability_hotelbeds-api', [HotelBedsController::class, 'checkAvailability']);
// Route::post('/confirm-booking_hotelbeds-api', [HotelBedsController::class, 'confirmBooking']);
// Route::get('/get-hoteldetails_hotelbeds-api', [HotelBedsController::class, 'getHotelDetails']);
// Route::get('/get-countries_hotelbeds-api', [HotelBedsController::class, 'getCountryList']);
// Route::get('/get-destinations_hotelbeds-api', [HotelBedsController::class, 'getDestinationList']);
// Route::get('/get-hoteldetailwithminprice_hotelbeds-api', [HotelBedsController::class, 'getHotelListMinPriceHotelBeds']);
// Route::get('/get-hoteldetailsbyid_hotelbeds-api/{id}', [HotelBedsController::class, 'getHotelByIdHotelBeds']);
// Route::post('/checkingroomavailability_hotelbeds-api', [HotelBedsController::class, 'getRoomAvailabilityByHotelCode']);
// Route::post('/booking-confirmationemail_hotelbeds-api/{id}', [HotelBedsController::class, 'emailRecipt']);
// Route::post('/booking-cancellation_hotelbeds-api/{id}', [HotelBedsController::class, 'bookingCancellation']);
// Route::post('/get-availability-bygeolocation_hotelbeds-api', [HotelBedsController::class, 'filterHotelsByGeoLocation']);
// Route::post('/get-availability-byboardcode_hotelbeds-api', [HotelBedsController::class, 'getHotelByBoardCode']);
// Route::post('/get-hotel-rates-hotelbeds-api', [HotelBedsController::class, 'getHotelRates']);

// Route::get('/get-user-currency', [HotelBedsController::class, 'getBookingsById']);


// /* ------------------------------------------------------------------------------- */
// /* ------------------------------------------------------------------------------- */
// /* -------------------------------Hotel TBO API Routes--------------------------- */
// /* ------------------------------------------------------------------------------- */
// /* ------------------------------------------------------------------------------- */

// Route::get('/get-countrylist_hoteltbo-api', [HotelTBOController::class, 'getCountryList']);
// Route::get('/get-hoteldetails_hoteltbo-api', [HotelTBOController::class, 'getHotelDetails']);
// Route::get('/get-hotelcodes_hoteltbo-api', [HotelTBOController::class, 'getAllHotelCodesTBO']);
// Route::post('/get-searchrooms_hoteltbo-api', [HotelTBOController::class, 'searchRoomForAvailable']);
// Route::get('/get-hotelbyid_hoteltbo-api/{id}', [HotelTBOController::class, 'getHotelByIdTBO']);
// Route::get('/get-hoteldetailsminprice_hoteltbo_api', [HotelTBOController::class, 'getHotelDetailsWithMinPrice']);
// Route::post('/room_prebooking_hoteltbo_api', [HotelTBOController::class, 'preBookHotelTbo']);
// Route::post('/room_booking_hoteltbo_api', [HotelTBOController::class, 'bookHotelRoomTbo']);
// Route::get('/booking_confirmationemail_hoteltbo_api/{id}', [HotelTBOController::class, 'sendEmail']);


// Route::get('/fetch-mainsub_frommain-cat/{id}', [SubMainCategorySubController::class, 'getCategory2ByCategory1']);
// Route::get('/fetch-mainsub2_frommain-cat/{id}', [SubMainCategorySubController::class, 'getCategory3ByCategory2']);


// /* ------------------------------------------------------------------------------- */
// /* ------------------------------------------------------------------------------- */
// /* ******************** Appleholidays Hotel Bookings Routes  ***********************/
// /* --------------------------------------------------------------------------------- */
// /* -------------------------------------------------------------------------------- */

// Route::post('/hotels_preBooking', [HotelsPreBookings::class, 'addPreBooking']);
// Route::post('/apple_booking_availability__applehotels/{id}', [BookingController::class, 'checkingAvailability']);
// Route::post('/apple_booking_confirm__applehotels/{id}', [BookingController::class, 'confirmBookingApple']);
// Route::get('/apple_booking_confirm_sendemail__applehotels/{id}', [BookingController::class, 'sendConfirmationEmail']);
// Route::post('/apple_booking_cancellation__applehotels/{id}', [BookingController::class, 'bookingCancellationRequest']);
// Route::post('/apple_booking_ammend__applehotels/{id}', [BookingController::class, 'ammendBooking']);


// /* ///////////////////////////////////////////////////////////////////////////////////////////// */
// /* ************************************** Brands ***************************************** */
// /* ///////////////////////////////////////////////////////////////////////////////////////////// */
// Route::post('/create-new-brand', [BrandsController::class, 'createNewBrand']);
// Route::get('/fetch-all-brands', [BrandsController::class, 'getAllBrands']);




// /* ///////////////////////////////////////////////////////////////////////////////////////////// */
// /* ************************************** Payment Options  ***************************************** */
// /* ///////////////////////////////////////////////////////////////////////////////////////////// */

// Route::get('/fetch-all-payment-options', [PaymentOptionsController::class, 'getAllOptions']);

// /* ///////////////////////////////////////////////////////////////////////////////////////////// */
// /* **************************************  Product Details Listing  ***************************************** */
// /* ///////////////////////////////////////////////////////////////////////////////////////////// */

// Route::post('/create-new-product-detail', [ProductDetailsController::class, 'createListingDetails']);

// /* ///////////////////////////////////////////////////////////////////////////////////////////// */
// /* **************************************  Life Styles Routes  ***************************************** */
// /* ///////////////////////////////////////////////////////////////////////////////////////////// */


// Route::get('/get-all-life-styles-by-id/{id}', [LifeStylesController::class, 'getLifeStylesByID']);
// Route::get('/get-all-life-styles', [LifeStylesController::class, 'get_lifestyles']);
// Route::post('/add-new-life-styles-booking', [LifeStyleBookingController::class, 'addNewLifeStyleBooking']);
// Route::post('/uploadexcel', [ExcelController::class, 'uploadExcel']);

// /* #############################  Life Style Vendor Controller ############################## */
// Route::get('/get-life-styles', [LifeStyleVendorController::class, 'getLifeStyleTypes']);
// Route::post('/createnewlifestylevendor', [LifeStyleVenController::class, 'createNewLifeStyleVendor']);


// /* ///////////////////////////////////////////////////////////////////////////////////////////// */
// /* **************************************  Education  ***************************************** */
// /* ///////////////////////////////////////////////////////////////////////////////////////////// */
// Route::post('/upload-education-video-vimeo', [VideoController::class, 'uploadEducationVideo']);
// Route::post('/add-new-education-listing', [EducationListingsController::class, 'createEducationListing']);
// Route::post('/create-new-education-details', [EducationListingsController::class, 'createEducationDetails']);
// Route::post('/update-blackout-days', [EducationListingsController::class, 'updateBlackoutDays']);
// Route::get('/get-all-education-vendors', [EducationVendorController::class, 'getAllEducationVendors']);
// Route::get('/get-education-course-names', [EducationListingsController::class, 'getEducationCourseNames']);
// Route::post('/create-newedu-vendor', [EducationVendorController::class, 'createNewEduVendor']);
// Route::get('/get-education-service-locations/{id}', [EducationListingsController::class, 'getEducationServiceLocations']);
// Route::post('/get-education-sessions-by-lesson-id', [EducationSessionsController::class, 'getEducationSessionByLessonID']);
// Route::get('/get-education-resources-by-teacher-id/{id}', [VideoController::class, 'getEducationVideosByTeacherID']);
// Route::post('/add-new-education-inventory', [EducationInventoryController::class, 'addNewEducationInventory']);
// Route::post('/add-new-education-session', [EducationSessionsController::class, 'addNewEducationSession']);
// Route::post('/educationLinkUpdate', [EducationSessionsController::class, 'eduction_lesson_link_update']);


// Route::post('/add-education-booking', [EducationListingsController::class, 'addEducationBooking']);
// Route::get('/get-inventory-ids-by-listing-id/{id}', [EducationInventoryController::class, 'getInventoryIds']);
// Route::post('/get-time-slots-by-date', [EducationSessionsController::class, 'getTimeSlotsByDate']);
// Route::post('/get-time-slots-by-sessionid', [EducationSessionsController::class, 'getTimeSlotsBySession']);
// Route::get('/get-all-educations', [EducationListingsController::class, 'getAllEducations']);
// Route::get('/get-all-educations-by-id/{id}', [EducationListingsController::class, 'getAllEducationsByID']);
// Route::post('/get-session-video-by-lesson-id', [EducationListingsController::class, 'getSessionVideoByLessonID']);

// Route::get('/get-upcoming-education-sessions/{id}', [EducationListingsController::class, 'getUserUpcomingEducationSessions']);

// // **** GET PUBLIC IP ****//
// Route::post('/getuserip', [AuthController::class, 'getUserCurrentLocation']);


// /* ///////////////////////////////////////////////////////////////////////////////////////////// */
// /* **************************************  Zoom Meeting  ***************************************** */
// /* ///////////////////////////////////////////////////////////////////////////////////////////// */

// Route::get('/meeting-list_zoom', [ZoomMeetingController::class, 'list']);
// Route::post('/create-meeting_zoom', [ZoomMeetingController::class, 'create']);
// Route::get('/meeting-by-room-id_zoom/{id}', [ZoomMeetingController::class, 'get']);
// Route::patch('/update-meeting_zoom/{id}', [ZoomMeetingController::class, 'update']);
// Route::delete('/remove-meeting_zoom/{id}', [ZoomMeetingController::class, 'delete']);

// // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
// // **** *********************************  Sabre Flights Routes  ************************************ **** //
// // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //

// Route::post('/get-token', [SabreFlightController::class, 'getToken']);
// Route::post('/check-availability_Sabre_Flight', [SabreFlightController::class, 'checkFlightAvailability']);

// Route::get('/get-airport-codes', [AirportCodesController::class, 'getAirportCodes']);

// Route::post('/revalidating_Sabre_Flight', [SabreFlightController::class, 'reValidatingFlightDetails']);
// Route::post('/revalidating_Sabre_Flight_RT_MC', [SabreFlightController::class, 'reValidatingRTMC']);



// Route::post('/confirm-booking_Sabre_Flight', [SabreFlightController::class, 'confirmBooking']);
// Route::post('/cancel-booking_Sabre_Flight/{id}', [SabreFlightController::class, 'cancelFlightBooking']);
// Route::get('/airlineticket', [SabreFlightController::class, 'ticketview']);
// Route::post('/get-booking-details', [SabreFlightController::class, 'getBookingDetails']);


// Route::post('/decrtyp-value', [TestController::class, 'decryptEmail']);

// Route::post('/request-payment-url', [PaymentController::class, 'requestPaymentUrl']);
// Route::post('/get-payment-response', [PaymentController::class, 'getPaymentResponse']);
// Route::post('/get-payment-recipt/{paymentId}', [PaymentController::class, 'getPaymentRecipt']);


// Route::get('/all-currencies', [CurrencyController::class, 'getAllCurrecncies']);
// Route::get('/get-currency-byid/{currId}', [CurrencyController::class, 'getCurrencyById']);
// // Auth::routes(['verify' => true]);
// // Auth::routes(['verify' => true]);

// Route::post('/customer-new-shipping-address', [ShippingAddressController::class, 'createNewShippingAddress']);

// // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
// // **** *********************************  Customer All Orders/Types Routes  ************************ **** //
// // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //

// Route::get('/get-ess-orders/{id}', [CustomerOrdersController::class, 'fetchEssentialsByCusId']);
// Route::get('/get-edu-orders/{id}', [CustomerOrdersController::class, 'fetchEducationByCusId']);
// Route::get('/get-ls-orders/{id}', [CustomerOrdersController::class, 'fetchLifeStyleCusId']);
// Route::get('/get-hotel-orders/{id}', [CustomerOrdersController::class, 'fetchHotelCusId']);

// // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
// // **** *********************************  Search Controller Routes  ************************ **** //
// // **** ///////////////////////////////////////////////////////////////////////////////////////////// **** //
// Route::post('/search-essentials', [CustomerSearchController::class, 'searchEssentialProducts']);
// Route::post('/search-lifestyle', [CustomerSearchController::class, 'searchLifeStyleProducts']);

// // **** *********************************  Order Feedback Routes  ************************ **** //
// Route::post('/create-new-order-feedback', [OrderFeedbackController::class, 'createOrderFeedback']);
