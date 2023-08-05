<?php

namespace App\Http\Controllers\Sabre;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sabre\SabreFlightConfig;
use App\Models\Sabre\FlightPayment;
use App\Models\Sabre\FlightResevation;
use App\Models\Sabre\FlightPreBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use IlluminateAgnostic\Collection\Support\Arr;
use PHPUnit\Framework\Constraint\Count;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use function PHPSTORM_META\type;

class SabreFlightController extends Controller
{

    public $FlightPreBook;

    public function __construct()
    {
        $this->FlightPreBook = new FlightPreBooking();
    }

    public function getToken()
    {
        $RestEndpoint = 'https://api.cert.platform.sabre.com/v2/auth/token';
        $tokenResponse = Http::withHeaders([
            'Authorization' => 'Basic VmpFNk56azNNVHBhTjBJNE9rRkI6UVhSbFkwZzVOak09',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'grant_type' => 'client_credentials'
        ])->post($RestEndpoint)->json();
        $token = $tokenResponse['access_token'];
        // $PlainText = (string)'grant_type=client_credentials';
        // $contentType = 'Text';

        return $token;
    }

    function getHeaders()
    {
        $Headers = [];

        $config = SabreFlightConfig::getInstance();
        $restURL = $config->getRestProperty('environment');

        $Username = base64_encode('V2:7971:Z7B8:AA');
        $Password = base64_encode('AtecH963');

        $ColonSeperator = base64_encode($Username . ':' . $Password);

        $final = base64_encode($ColonSeperator);

        $Headers['Authorization'] = 'Bearer ' . $this->getToken(); //VmpFNmFIZ3plVzg1ZHpkeWN6SnpkSFoyTmpwRVJWWkRSVTVVUlZJNlJWaFU6T1U5RWFXeEVhakU9 //VmpFNk56azNNVHBhTjBJNE9rRkI6UVdGd2NERXhNakk9
        $Headers['Content-Type'] = 'application/json';
        // $Headers['grant_type'] = 'client_credentials';

        return $Headers;
    }



    public function reValidatingFlightDetails(Request $request)
    {
        $ReValidateURL = 'https://api.cert.platform.sabre.com/v4/shop/flights/revalidate';

        //Departure/Arrival time with timestamp
        $MainDepartureOne = explode(',', date('Y-m-d\TH:i:s', strtotime($request->input('maindep_datetime_one'))));
        $MainDepartureTwo = explode(',', date('Y-m-d\TH:i:s', strtotime($request->input('maindep_datetime_two'))));

        $MainArrivalOne = explode(',', date('Y-m-d\TH:i:s', strtotime($request->input('mainarr_datetime_one'))));
        $MainArrivalTwo = explode(',', date('Y-m-d\TH:i:s', strtotime($request->input('mainarr_datetime_two'))));

        $DepartureDateTime = explode(',', $request->input('departure_datetime'));
        $ArrivalDateTime = explode(',', $request->input('arrival_datetime'));

        $OriginLocation = explode(',', $request->input('origin_location'));
        $DestinationLocation = explode(',', $request->input('dest_location'));

        /* --- Flight Codes --- */
        $flightCode = explode(',', $request->input('flight_code'));
        $flightNumber = explode(',', $request->input('flight_number'));

        $DepartureArray = [];
        $ArrivalArray = [];

        foreach ($DepartureDateTime as $DepDateTime) {
            $DepartureArray[] = date('Y-m-d\TH:i:s', strtotime($DepDateTime));
        }

        foreach ($ArrivalDateTime as $ArrDateTime) {
            $ArrivalArray[] = date('Y-m-d\TH:i:s', strtotime($ArrDateTime));
        }


        $PassengerType = explode(',', $request->input('passenger_type'));
        $SeatCount = (int)$request->input('seat_count');

        $ValidateArray = [];
        $PaxArray = array();

        $adultCount = 0;
        $childCount = 0;
        $infCount = 0;

        /* --- Json array components --- */
        $ValidateArray['OTA_AirLowFareSearchRQ']['Version'] = '6.4.0';
        $ValidateArray['OTA_AirLowFareSearchRQ']['ResponseType'] = 'GIR';
        $ValidateArray['OTA_AirLowFareSearchRQ']['TravelPreferences']['TPA_Extensions']['VerificationItinCallLogic']['Value'] = 'B';
        $ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['SeatsRequested'][] = $SeatCount;

        if (count($PassengerType) >= 1) {
            foreach ($PassengerType as $paxtype) {
                if ($paxtype == 'ADT') {
                    $adultCount += 1;
                } else if ($paxtype == 'CNN') {
                    $childCount += 1;
                } else if ($paxtype == 'INF') {
                    $infCount += 1;
                }
            }
        }

        $passengers = array_unique($PassengerType);

        $arrrayF = [$adultCount, $childCount, $infCount];

        foreach ($passengers as $paxKey) {
            if ($paxKey == 'ADT') {
                $ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'][] = ['Code' => 'ADT', 'Quantity' => $adultCount];
            } else if ($paxKey == 'CNN') {
                $ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'][] = ['Code' => 'CNN', 'Quantity' => $childCount];
            } else if ($paxKey == 'INF') {
                $ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'][] = ['Code' => 'INF', 'Quantity' => $infCount];
            }
        }

        $ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']['PseudoCityCode'] = 'Z7B8';
        $ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['Type'] = '1';
        $ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['ID'] = '1';
        $ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['CompanyName']['Code'] = 'TN';

        $flights = [];

        if (count($flightCode) >= 1) {
            for ($xx = 0; $xx < count($flightCode); $xx++) {
                $flights[] = ['Number' => (int)$flightNumber[$xx], 'DepartureDateTime' => $DepartureArray[$xx], 'ArrivalDateTime' => $ArrivalArray[$xx], 'Type' => 'A', 'ClassOfService' => 'K', 'OriginLocation' => ['LocationCode' => $OriginLocation[$xx]], 'DestinationLocation' => ['LocationCode' => $DestinationLocation[$xx]], 'Airline' => ['Operating' => $flightCode[$xx], 'Marketing' => $flightCode[$xx]]];
            }
        }
        // return $DepartureArray;

        if (count($MainDepartureOne) >= 1) {
            for ($i = 0; $i < count($MainDepartureOne); $i++) {
                $ValidateArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'][] =
                    ['RPH' => (string)($i + 1), 'DepartureDateTime' => $DepartureArray[$i], 'OriginLocation' => ['LocationCode' => $OriginLocation[$i]], 'DestinationLocation' => ['LocationCode' => $DestinationLocation[$i]], 'TPA_Extensions' => ['Flight' => $flights]];
            }
        }


        $ValidateArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'] = '200ITINS';


        $SubArray = [];

        $SubArray['OTA_AirLowFareSearchRQ'] = $ValidateArray['OTA_AirLowFareSearchRQ'];
        $SubArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail'] = [$ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']];
        $SubArray['OTA_AirLowFareSearchRQ']['POS']['Source'] = [$ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']];
        $SubArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'] = $ValidateArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'];
        $SubArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'] = $ValidateArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'];

        // return $SubArray;

        session()->push('flight.revalidator', $SubArray);

        try {

            $getReValidityData = Http::withHeaders($this->getHeaders())->post($ReValidateURL, $SubArray)->json();
            // return $getReValidityData;

            $legsDescs = $getReValidityData['groupedItineraryResponse']['legDescs'];
            $scheduleDescs = $getReValidityData['groupedItineraryResponse']['scheduleDescs'];
            $itenerary = $getReValidityData['groupedItineraryResponse']['itineraryGroups'];



            foreach ($legsDescs as $legs) {


                foreach ($legs['schedules'] as $key) {
                    foreach ($scheduleDescs as $scheduleData) {
                        if ($key['ref'] == $scheduleData['id']) {
                            $scheduleDt[] = $scheduleData;
                        }
                    }
                }
            }

            foreach ($itenerary as $it) {
                foreach ($it['itineraries'] as $pricing) {
                    $pricingInfo[] = $pricing['pricingInformation'];
                }
            }


            $getReValidityData['groupedItineraryResponse']['scheduleDescs'] = $scheduleDt;
            $getReValidityData['groupedItineraryResponse']['pricingSource'] = $pricingInfo;
            // return $getReValidityData;



            return response([
                'status' => 200,
                'data_set' => $getReValidityData,
                'data' => $MainDepartureOne
            ]);
        } catch (\Exception $ex) {
            return response([
                'status' => 500,
                'exception_message' => $ex->getMessage()
            ]);
        }
    }




    //RoundTrip Multi City Validator
    public function reValidatingRTMC(Request $request)
    {
        $ReValidateURL = 'https://api.cert.platform.sabre.com/v4/shop/flights/revalidate';

        //Departure/Arrival time with timestamp
        $MainDepartureOne = explode(',', date('Y-m-d\TH:i:s', strtotime($request->input('maindep_datetime_one'))));

        $DepartureDateTime = explode(',', $request->input('departure_datetime'));
        $ArrivalDateTime = explode(',', $request->input('arrival_datetime'));

        $OriginLocation = explode(',', $request->input('origin_location'));
        $DestinationLocation = explode(',', $request->input('dest_location'));

        /* --- Flight Codes --- */
        $flightCode = explode(',', $request->input('flight_code'));
        $flightNumber = explode(',', $request->input('flight_number'));



        /* --- Main Locations --- */
        $mainDepLocations = explode(',', $request->input('mainDepLocation'));
        $mainArrLocations = explode(',', $request->input('mainArrLocation'));

        $locationIndexes = explode(',', $request->input('locationIndexes'));

        $DepartureArray = [];
        $ArrivalArray = [];

        foreach ($DepartureDateTime as $DepDateTime) {
            $DepartureArray[] = date('Y-m-d\TH:i:s', strtotime($DepDateTime));
        }

        foreach ($ArrivalDateTime as $ArrDateTime) {
            $ArrivalArray[] = date('Y-m-d\TH:i:s', strtotime($ArrDateTime));
        }


        $PassengerType = explode(',', $request->input('passenger_type'));
        $SeatCount = (int)$request->input('seat_count');

        $ValidateArray = [];
        $PaxArray = array();

        $adultCount = 0;
        $childCount = 0;
        $infCount = 0;

        /* --- Json array components --- */
        $ValidateArray['OTA_AirLowFareSearchRQ']['Version'] = '6.4.0';
        $ValidateArray['OTA_AirLowFareSearchRQ']['ResponseType'] = 'GIR';
        $ValidateArray['OTA_AirLowFareSearchRQ']['TravelPreferences']['TPA_Extensions']['VerificationItinCallLogic']['Value'] = 'B';
        $ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['SeatsRequested'][] = $SeatCount;

        if (count($PassengerType) >= 1) {
            foreach ($PassengerType as $paxtype) {
                if ($paxtype == 'ADT') {
                    $adultCount += 1;
                } else if ($paxtype == 'CNN') {
                    $childCount += 1;
                } else if ($paxtype == 'INF') {
                    $infCount += 1;
                }
            }
        }

        $passengers = array_unique($PassengerType);

        $arrrayF = [$adultCount, $childCount, $infCount];

        foreach ($passengers as $paxKey) {
            if ($paxKey == 'ADT') {
                $ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'][] = ['Code' => 'ADT', 'Quantity' => $adultCount];
            } else if ($paxKey == 'CNN') {
                $ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'][] = ['Code' => 'CNN', 'Quantity' => $childCount];
            } else if ($paxKey == 'INF') {
                $ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'][] = ['Code' => 'INF', 'Quantity' => $infCount];
            }
        }

        $ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']['PseudoCityCode'] = 'Z7B8';
        $ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['Type'] = '1';
        $ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['ID'] = '1';
        $ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['CompanyName']['Code'] = 'TN';

        $flights = [];


        $mainDepDates = [];


        for ($xx = 0; $xx < count($locationIndexes); $xx++) {
            $flights[$locationIndexes[$xx]][] = ['Number' => (int)$flightNumber[$xx], 'DepartureDateTime' => $DepartureArray[$xx], 'ArrivalDateTime' => $ArrivalArray[$xx], 'Type' => 'A', 'ClassOfService' => 'K', 'OriginLocation' => ['LocationCode' => $OriginLocation[$xx]], 'DestinationLocation' => ['LocationCode' => $DestinationLocation[$xx]], 'Airline' => ['Operating' => $flightCode[$xx], 'Marketing' => $flightCode[$xx]]];
            $mainDepDates[$locationIndexes[$xx]][] = $DepartureArray[$xx];
        }


        // if (count($flightCode) >= 1) {
        //     for ($xx = 0; $xx < count($flightCode); $xx++) {
        //         $flights[] = ['Number' => (int)$flightNumber[$xx], 'DepartureDateTime' => $DepartureArray[$xx], 'ArrivalDateTime' => $ArrivalArray[$xx], 'Type' => 'A', 'ClassOfService' => 'K', 'OriginLocation' => ['LocationCode' => $OriginLocation[$xx]], 'DestinationLocation' => ['LocationCode' => $DestinationLocation[$xx]], 'Airline' => ['Operating' => $flightCode[$xx], 'Marketing' => $flightCode[$xx]]];
        //     }
        // }


        // return $DepartureArray;

        if (count($mainDepLocations) >= 1) {
            for ($i = 0; $i < count($mainDepLocations); $i++) {
                $ValidateArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'][] =
                    ['RPH' => (string)($i + 1), 'DepartureDateTime' => $mainDepDates[$i][0], 'OriginLocation' => ['LocationCode' => $mainDepLocations[$i]], 'DestinationLocation' => ['LocationCode' => $mainArrLocations[$i]], 'TPA_Extensions' => ['Flight' => $flights[$i]]];
            }
        }

        // return $ValidateArray;

        $ValidateArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'] = '200ITINS';


        $SubArray = [];

        $SubArray['OTA_AirLowFareSearchRQ'] = $ValidateArray['OTA_AirLowFareSearchRQ'];
        $SubArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail'] = [$ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']];
        $SubArray['OTA_AirLowFareSearchRQ']['POS']['Source'] = [$ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']];
        $SubArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'] = $ValidateArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'];
        $SubArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'] = $ValidateArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'];

        session()->push('flight.revalidator', $SubArray);

        try {

            $getReValidityData = Http::withHeaders($this->getHeaders())->post($ReValidateURL, $SubArray)->json();



            $iteneraryData = $getReValidityData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['legs'];

            // return $iteneraryData;
            $legsDescs = $getReValidityData['groupedItineraryResponse']['legDescs'];
            $scheduleDescs = $getReValidityData['groupedItineraryResponse']['scheduleDescs'];

            $itenerary = $getReValidityData['groupedItineraryResponse']['itineraryGroups'];


            $legsD = [];
            // for ($i = 0; $i < count($legsDescs); $i++) {
            foreach ($iteneraryData as $key) {
                foreach ($legsDescs as $leg) {
                    if ($key['ref'] == $leg['id']) {
                        $legsD[] = $leg;
                    }
                }
            }
            // }


            for ($i = 0; $i < count($legsD); $i++) {
                foreach ($legsD[$i]['schedules'] as $key) {
                    foreach ($scheduleDescs as $scheduleData) {
                        if ($key['ref'] == $scheduleData['id']) {
                            $scheduleDt[$i][] =  $scheduleData;
                        }
                    }
                }
            }


            foreach ($itenerary as $it) {
                foreach ($it['itineraries'] as $pricing) {
                    $pricingInfo[] = $pricing['pricingInformation'];
                }
            }




            $getReValidityData['groupedItineraryResponse']['scheduleDescs'] = $scheduleDt;
            $getReValidityData['groupedItineraryResponse']['pricingSource'] = $pricingInfo;

            return response([
                'status' => 200,
                'data_set' => $getReValidityData,
                'data' => $MainDepartureOne
            ]);
        } catch (\Exception $ex) {
            return response([
                'exception_message' => throw $ex
            ]);
        }
    }




    // **** One Way && Round Trip Flight Availability Checking ****//
    public function checkFlightAvailability(Request $request)
    {
        $Rest_URL = 'https://api.cert.platform.sabre.com/v4/offers/shop';

        $MainArray = [];
        $PaxArray = array();
        $dateNow = Carbon::now()->toDateTimeString();

        $TripType = explode(',', $request->input('trip_type'));
        $DepartureDate = date('Y-m-d\TH:i:s', strtotime($request->input('dep_date')));
        $ReturnDate = date('Y-m-d\TH:i:s', strtotime($request->input('return_date')));
        $FromLocation = $request->input('from_location');
        $ToLocation = $request->input('to_location');
        $PassengerCount = (int)$request->input('passenger_count');
        $SeatCount = (int)$request->input('seat_count');
        $PassengerType = explode(',', $request->input('passenger_type'));
        $cabinClass = $request->input("cabin_code");

        // **** ---Main Array Begin--- **** //

        $MainArray['OTA_AirLowFareSearchRQ']['Version'] = 'v4';
        // $MainArray['OTA_AirLowFareSearchRQ']['AvailableFlightsOnly'] = true;
        $MainArray['OTA_AirLowFareSearchRQ']['ResponseType'] = 'OTA';
        $MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']['PseudoCityCode'] = 'Z7B8';
        $MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['Type'] = '1';
        $MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['ID'] = '1';
        $MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['CompanyName']['Code'] = 'TN';
        // $MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['CompanyName']['Content'] = 'TN';

        /* #################################-- Multi City Parameters for Availability Search --################################# */

        $MultiDepartureDate = explode(',', $request->input('dep_date'));
        $MultiFromLocation = explode(',', $request->input('from_location'));
        $MultiToLocation = explode(',', $request->input('to_location'));

        $MultiDep = [];
        foreach ($MultiDepartureDate as $multidate) {
            $MultiDep[] = date('Y-m-d\TH:i:s', strtotime($multidate));
        }

        if (count($MultiDepartureDate) >= 1) {
            for ($x = 0; $x < count($MultiDepartureDate); $x++) {

                if (in_array('One Way', $TripType)) {
                    $MainArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'][] = ['RPH' => (string)($x + 1), 'DepartureDateTime' => $MultiDep[$x], 'OriginLocation' => ['LocationCode' => $MultiFromLocation[$x]], 'DestinationLocation' => ['LocationCode' => $MultiToLocation[$x]]];
                } else if (in_array('Round Trip', $TripType)) {
                    $MainArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'][] = ['RPH' => (string)($x + 1), 'DepartureDateTime' => $MultiDep[$x], 'OriginLocation' => ['LocationCode' => $MultiFromLocation[$x]], 'DestinationLocation' => ['LocationCode' => $MultiToLocation[$x]]];
                } else if (in_array('Multi City', $TripType)) {
                    $MainArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'][] = ['RPH' => (string)($x + 1), 'DepartureDateTime' => $MultiDep[$x], 'OriginLocation' => ['LocationCode' => $MultiFromLocation[$x]], 'DestinationLocation' => ['LocationCode' => $MultiToLocation[$x]]];
                }
            }
        }

        $MainArray['OTA_AirLowFareSearchRQ']['TravelPreferences']['CabinPref']['Cabin'] = $cabinClass;
        $MainArray['OTA_AirLowFareSearchRQ']['TravelPreferences']['CabinPref']['PreferLevel'] = 'Preferred';
        $MainArray['OTA_AirLowFareSearchRQ']['TravelPreferences']['ValidInterlineTicket'] = true;
        $MainArray['OTA_AirLowFareSearchRQ']['TravelPreferences']['Baggage']['RequestType'] = 'A';
        $MainArray['OTA_AirLowFareSearchRQ']['TravelPreferences']['Baggage']['Description'] = true;
        $MainArray['OTA_AirLowFareSearchRQ']['TravelPreferences']['VendorPref'] = [];

        $MainArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['SeatsRequested'] = [$SeatCount];

        if (count($PassengerType) >= 1) {
            $arrayLength = count($PassengerType);
            for ($i = 0; $i < $arrayLength; $i++) {
                $key = $PassengerType[$i];
                $PaxArray[] = array_count_values($PassengerType);
            }
        }

        $arr = [];
        $adultCount = 0;
        $childCount = 0;
        $infCount = 0;


        foreach ($PassengerType as $key) {
            if ($key == "ADT") {
                $adultCount += 1;
            } else if ($key == "CNN") {
                $childCount += 1;
            } else if ($key == "INF") {
                $infCount += 1;
            }
        }

        $passengers = array_unique($PassengerType);

        $arrrayF = [$adultCount, $childCount, $infCount];

        foreach ($passengers as $key) {
            if ($key == "ADT") {
                $MainArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'][] = ['Code' => 'ADT', 'Quantity' => $adultCount, 'TPA_Extensions' => ['VoluntaryChanges' => ['Match' => 'Info']]];
            } else if ($key == "CNN") {
                $MainArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'][] = ['Code' => 'CNN', 'Quantity' => $childCount, 'TPA_Extensions' => ['VoluntaryChanges' => ['Match' => 'Info']]];
            } else if ($key == "INF") {
                $MainArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'][] = ['Code' => 'INF', 'Quantity' => $infCount, 'TPA_Extensions' => ['VoluntaryChanges' => ['Match' => 'Info']]];
            }
        }


        $MainArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'] = '200ITINS';

        $SubArray = [];

        $SubArray['OTA_AirLowFareSearchRQ']['Version'] = $MainArray['OTA_AirLowFareSearchRQ']['Version'];
        // $SubArray['OTA_AirLowFareSearchRQ']['AvailableFlightsOnly'] = $MainArray['OTA_AirLowFareSearchRQ']['AvailableFlightsOnly'];
        $SubArray['OTA_AirLowFareSearchRQ']['ResponseType'] = $MainArray['OTA_AirLowFareSearchRQ']['ResponseType'];
        $SubArray['OTA_AirLowFareSearchRQ']['POS']['Source'] = [$MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']];
        $SubArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'] = $MainArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'];
        $SubArray['OTA_AirLowFareSearchRQ']['TravelPreferences'] = $MainArray['OTA_AirLowFareSearchRQ']['TravelPreferences'];
        $SubArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['SeatsRequested'] = $MainArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['SeatsRequested'];
        $SubArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail'] = [$MainArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']];
        $SubArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'] = $MainArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'];
        $SubArray['OTA_AirLowFareSearchRQ']['TravelPreferences']['CabinPref'] = [$MainArray['OTA_AirLowFareSearchRQ']['TravelPreferences']['CabinPref']];

        // return $SubArray;
        Session::put('flight.availability', $SubArray);
        // try {

        // return $SubArray;

        $getFilghtAvailability = Http::withHeaders($this->getHeaders())->post($Rest_URL, $SubArray)->json();

        // return $getFilghtAvailability;

        // return $getFilghtAvailability['groupedItineraryResponse']['statistics']['itineraryCount'];

        if ($getFilghtAvailability['groupedItineraryResponse']['statistics']['itineraryCount'] === 0) {
            return response([
                'status' => 404,
                'message' => 'No flight data available'
            ]);
        } else {

            $itenararyGroups = $getFilghtAvailability['groupedItineraryResponse']['itineraryGroups'];


            $legDescs = $getFilghtAvailability['groupedItineraryResponse']['legDescs'];
            $scheduleDescs = $getFilghtAvailability['groupedItineraryResponse']['scheduleDescs'];
            $fareComponentDescs = $getFilghtAvailability['groupedItineraryResponse']['fareComponentDescs'];
            $taxDescs = $getFilghtAvailability['groupedItineraryResponse']['taxDescs'];
            $taxSummaryDescs = $getFilghtAvailability['groupedItineraryResponse']['taxSummaryDescs'];

            for ($i = 0; $i < count($itenararyGroups); $i++) {
                $flightData[] = $itenararyGroups[$i]['itineraries'];
                for ($x = 0; $x < count($flightData[$i]); $x++) {
                    $flightItenararyFinalData[] = $flightData[$i][$x];
                }
            }




            $x = 0;
            $it = [];



            $flightCodesArray = [];


            if ($TripType[0] == "One Way") {
                foreach ($itenararyGroups as $itenararyGroup) {
                    foreach ($itenararyGroup['itineraries'] as $itenaryFlight) {

                        $totPrice = $itenaryFlight['pricingInformation'][0]['fare']['totalFare'];

                        foreach ($itenaryFlight['legs'] as $itenaryRef) {
                            foreach ($legDescs as $legData) {
                                if ($legData['id'] == $itenaryRef['ref']) {
                                    foreach ($legData['schedules'] as $scheduledRefs) {
                                        foreach ($scheduleDescs as $schedule) {
                                            if ($scheduledRefs['ref'] == $schedule['id']) {
                                                $flightCodesArray[] = $schedule['carrier']['marketing'];

                                                $flightCode[] = $schedule['carrier']['marketing'];
                                                $scheduleData[] = $schedule;
                                            }
                                        }
                                    }
                                    // $forData[] = ['refKey' => $itenaryRef['ref'], 'id' => $itenaryFlight['id'], 'fare' => $fareDataSet, 'taxesDataSet' => $taxesDataSet, 'taxSummary' => $taxSummary, 'itenaryGroupData' => $itenararyGroup['groupDescription'], 'legsData' => $legData, 'scheduleData' => $scheduleData,  'dataFlight' => $itenaryFlight];
                                    $forData[] = ['refKey' => $itenaryRef['ref'], 'id' => $itenaryFlight['id'], 'flightCodes' => $flightCode, 'totalFare' => $totPrice, 'itenaryGroupData' => $itenararyGroup['groupDescription'], 'legsData' => $legData, 'scheduleData' => $scheduleData,  'dataFlight' => $itenaryFlight];
                                    $scheduleData = [];
                                    $flightCode = [];
                                }
                            }
                        }
                    }
                }
            } else {
                foreach ($itenararyGroups as $itenararyGroup) {

                    foreach ($itenararyGroup['itineraries'] as $itenaryFlight) {

                        $totPrice = $itenaryFlight['pricingInformation'][0]['fare']['totalFare'];

                        foreach ($itenaryFlight['legs'] as $legRef) {
                            $legRefIds[] = $legRef['ref'];
                            foreach ($legDescs as $legData) {

                                if ($legData['id'] == $legRef['ref']) {
                                    $legG[] = $legData;
                                    foreach ($legData['schedules'] as $scheduledRefs) {
                                        $scheduleRef[$x][] = $scheduledRefs['ref'];


                                        foreach ($scheduleDescs as $schedule) {
                                            if ($scheduledRefs['ref'] == $schedule['id']) {

                                                $flightCodesArray[] = $schedule['carrier']['marketing'];
                                                $scheduleData[$x][] = $schedule;


                                                $flightCode[] = $schedule['carrier']['marketing'];
                                            }
                                        }
                                    }
                                }
                            }
                            $x = $x + 1;
                        }

                        $forData[] = ['id' => $itenaryFlight['id'], 'flightCodes' => $flightCode, 'totalFare' => $totPrice, 'itenaryGroupData' => $itenararyGroup['groupDescription'], 'dataFlight' => $itenaryFlight, 'legsData' => $legG, 'legsRef' => $legRefIds, 'scheduleRef' => $scheduleRef, 'scheduleData' => $scheduleData];

                        $legRefIds = [];
                        $flightCode = [];
                        $scheduleRef = [];
                        $legG = [];
                        $scheduleData = [];
                        $x = 0;
                    }
                }
            }

            // return $totPrice;




            $pricingArray = [];

            foreach ($forData as $key) {
                $pricingArray[] = $key['totalFare']['totalPrice'];
            }

            // $filtered_collection = Arr::where($forData, function ($value, $key) use ($FromLocation) {
            //     foreach ($value['scheduleData'] as $scheduleValue) {
            //         return $scheduleValue['departure']['airport'] == $FromLocation;
            //     }
            // });
            $requestNew = new \Illuminate\Http\Request();
            $requestNew->setMethod('POST');

            // return $ARR;

            return response([
                'status' => 200,
                'data_set' => $forData,
                'pricing' => [min($pricingArray), max($pricingArray)],
                'flightCodes' => array_values(array_unique($flightCodesArray)),
                'sabreData' => $getFilghtAvailability,
            ]);
        }
    }



    /* Ticket Booking Process____ Booking creation  */
    public function confirmBooking(Request $request)
    {
        try {
            $Rest_URL = 'https://api.cert.platform.sabre.com/v2.4.0/passenger/records?mode=create';
            $BookingArray = [];


            $OrderId = $request['order_id'];
            $PaxEmail = explode(',', $request['pax_email']);
            $ContactNumber = explode(',', $request['contact_number']);
            $PersonFirstName = explode(',', $request['first_name']);
            $PersonSurname = explode(',', $request['surname']);
            $FlightCodes = explode(',', $request['flight_code']);
            $FlightNumber = explode(',', $request['flight_number']);
            $DestinationLocation = explode(',', $request['dest_loccation']);
            $OriLocation = explode(',', $request['ori_loccation']);
            $PaxCount = strval($request['pax_count']);
            $UserEmail = $request['user_email'];
            $UserId = $request['user_id'];
            $ResevationName = $request['reservation_name'];
            $TotalAmount = $request['total_amount'];

            $BaggageDetails = $request['baggeges'];

            //* Flight Details *//

            $DepartureDateTime = explode(',', $request['departure_datetime']);

            $DepartureTimeArray = [];

            foreach ($DepartureDateTime as $key) {
                $DepartureTimeArray[] = date('Y-m-d\TH:i:s', strtotime($key));
            }

            $BookingArray['CreatePassengerNameRecordRQ']['version'] = '2.4.0';
            // $BookingArray['CreatePassengerNameRecordRQ']['targetCity'] = 'Z7B8';
            // $BookingArray['CreatePassengerNameRecordRQ']['haltOnAirPriceError'] = false;
            $BookingArray['CreatePassengerNameRecordRQ']['TravelItineraryAddInfo']['AgencyInfo']['Ticketing']['TicketType'] = '7TAW';

            if (count($ContactNumber) >= 1) {
                for ($x = 0; $x < count($ContactNumber); $x++) {
                    $BookingArray['CreatePassengerNameRecordRQ']['TravelItineraryAddInfo']['CustomerInfo']['ContactNumbers']['ContactNumber'][] = ['Phone' => $ContactNumber[$x], 'PhoneUseType' => 'H'];
                }
            }

            if (count($PersonFirstName) >= 1) {
                for ($i = 0; $i < count($PersonFirstName); $i++) {
                    $BookingArray['CreatePassengerNameRecordRQ']['TravelItineraryAddInfo']['CustomerInfo']['PersonName'][] = ['NameNumber' => (string)$i + 1 . '.' . '1', 'GivenName' => $PersonFirstName[$i], 'Surname' => $PersonSurname[$i]];
                }
            }

            if (count($DepartureTimeArray) >= 1) {
                for ($c = 0; $c < count($DepartureTimeArray); $c++) {
                    // $BookingArray['CreatePassengerNameRecordRQ']['AirBook']['HaltOnStatus'][] = ['Code' => 'NO', 'Code' => 'NN', 'Code' => 'UC', 'Code' => 'US', 'Code' => 'UN'];
                    $BookingArray['CreatePassengerNameRecordRQ']['AirBook']['OriginDestinationInformation']['FlightSegment'][] =
                        ['DepartureDateTime' => $DepartureTimeArray[$c], 'FlightNumber' => $FlightNumber[$c], 'NumberInParty' => $PaxCount, 'ResBookDesigCode' => 'Y', 'Status' => 'NN', 'DestinationLocation' => ['LocationCode' => $DestinationLocation[$c]], 'MarketingAirline' => ['Code' => $FlightCodes[$c], 'FlightNumber' => $FlightNumber[$c]], 'OriginLocation' => ['LocationCode' => $OriLocation[$c]]];
                }
            }

            $BookingArray['CreatePassengerNameRecordRQ']['AirBook']['RedisplayReservation'] = ['NumAttempts' => 3, 'WaitInterval' => 3000];


            $BookingArray['CreatePassengerNameRecordRQ']['PostProcessing']['EndTransaction']['Source']['ReceivedFrom'] = 'API';


            session()->push('flight.booking', $BookingArray);

            $BookingResponse = Http::withHeaders($this->getHeaders())->post($Rest_URL, $BookingArray)->json();

            $validator = $BookingResponse['CreatePassengerNameRecordRS']['ApplicationResults']['status'];

            // return $validator;

            if ($validator === 'Complete') {

                $BookingRef = $BookingResponse['CreatePassengerNameRecordRS']['ItineraryRef']['ID'];

                return response()->json([
                    'status' => 200,
                    'bookingDetails' => $this->getBookingDetails($BookingRef, $BaggageDetails, $UserEmail, $UserId, $ResevationName, $TotalAmount, $OrderId),
                ]);
            } else if ($validator === 'Incomplete') {
                return response()->json([
                    'status' => 500,
                    'error_response' => 'Error on booking flight',
                ]);
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' =>  throw $exception
            ]);
        }
    }

    /* Get Booking By Booking Confirmation Reference Number */
    public function getBookingDetails($BookRef, $BaggageData, $userEmail, $userId, $reserveName, $amount, $oid) //$BookRef, $BaggageData, $userEmail, $userId, $reserveName, $amount
    {

        $currentTime = \Carbon\Carbon::now()->toDateTimeString();

        $bag = array();

        foreach (explode(',', $BaggageData) as $bg) {
            $bag[] = $bg;
        }

        // return response([$BookRef, $BaggageData, $userEmail, $userId, $reserveName, $amount]);

        $BookingDetails = [];

        // return explode(',', $BaggageData);

        $Rest_URL = 'https://api.cert.platform.sabre.com/v1/trip/orders/getBooking';


        $BookingDetails['confirmationId'] = $BookRef;
        $BookingDetails['bookingSource'] = 'SABRE';

        $getBookingRefData = Http::withHeaders($this->getHeaders())->post($Rest_URL, $BookingDetails)->json();

        // return $getBookingRefData;

        $Confirmation  = $getBookingRefData['flights'][0]['confirmationId'];

        $JsonUrlAirport = storage_path('flightJson/AirportCodes.json');
        $GetFileContent = file_get_contents($JsonUrlAirport);
        $JsonData = json_decode($GetFileContent, true);
        $JsonData = array_filter($JsonData);

        /* ##################################### */

        $JsonUrlAirplane = storage_path('flightJson/AirplaneCodes.json');
        $GetFileContent2 = file_get_contents($JsonUrlAirplane);
        $JsonData2 = json_decode($GetFileContent2, true);
        $JsonData2 = array_filter($JsonData2);

        $arrayHidden = [];

        $count = 0;

        if ($getBookingRefData) {

            for ($i = 0; $i < count($getBookingRefData['flights']); $i++) {

                $count = $count + 1;

                $arrayHidden[] = $flightData['hiddenStopAirportCode'] ?? null;

                $FromTitle = collect($JsonData)->where('iata_code', '=', $getBookingRefData['flights'][$i]['fromAirportCode'])->values();
                $ToTitle = collect($JsonData)->where('iata_code', '=', $getBookingRefData['flights'][$i]['toAirportCode'])->values();

                $AirlineTitle = collect($JsonData2)->where('AirlineCode', '=', $getBookingRefData['flights'][$i]['airlineCode'])->values();

                if ($getBookingRefData['flights'][$i]['hiddenStopAirportCode'] ?? null != null) {

                    // return $count++;

                    $HiddenTitle = collect($JsonData)->where('iata_code', '=', $getBookingRefData['flights'][$i]['hiddenStopAirportCode'])->values();

                    FlightResevation::create([
                        'booking_ref' => $BookRef,
                        'confirm_ref' => $getBookingRefData['flights'][$i]['confirmationId'],
                        'reservation_name' => $reserveName,
                        'contact_info' => $userEmail,
                        'flight_code' => $getBookingRefData['flights'][$i]['airlineCode'],
                        'flight_no' => $getBookingRefData['flights'][$i]['flightNumber'],
                        'flight_title' => $AirlineTitle[0]['AlternativeBusinessName'],
                        'departure_fromCode' => $getBookingRefData['flights'][$i]['fromAirportCode'],
                        'departure_fromTitle' => $FromTitle[0]['city_name'],
                        'arrival_toCode' => $getBookingRefData['flights'][$i]['toAirportCode'],
                        'arrival_toTitle' => $ToTitle[0]['city_name'],
                        'departure_time' => $getBookingRefData['flights'][$i]['departureDate'] . ' ' . $getBookingRefData['flights'][$i]['departureTime'],
                        'arrival_time' => $getBookingRefData['flights'][$i]['arrivalDate'] . ' ' . $getBookingRefData['flights'][$i]['arrivalTime'],
                        'total_duration' => $getBookingRefData['flights'][$i]['durationInMinutes'],
                        'dep_terminal' => $getBookingRefData['flights'][$i]['departureTerminalName'] ?? null, //$flightData['departureTerminalName'] ? $flightData['departureTerminalName'] : $flightData['arrivalTerminalName'],
                        'arr_terminal' => $getBookingRefData['flights'][$i]['arrivalTerminalName'] ?? null,
                        'baggage_details' => gettype($BaggageData) === 'string' ? $BaggageData : $bag[$i],
                        'flight_class' => $getBookingRefData['flights'][$i]['cabinTypeName'],
                        'booking_status' => 'CONFIRMED',
                        'user_id' => $userId,
                        'created_at' => $currentTime,
                        'hidden_stopCode' => $getBookingRefData['flights'][$i]['hiddenStopAirportCode'],
                        'hidden_stopTitle' => $HiddenTitle[0]['city_name'],
                        'hidden_stopDeparture' => $getBookingRefData['flights'][$i]['hiddenStopDepartureDate'] . ' ' . $getBookingRefData['flights'][$i]['hiddenStopDepartureTime'],
                        'hidden_stopArrival' => $getBookingRefData['flights'][$i]['hiddenStopArrivalDate'] . ' ' . $getBookingRefData['flights'][$i]['hiddenStopArrivalTime'],
                        'order_id' => $oid
                    ]);



                    FlightPayment::create([
                        'booking_ref' => $BookRef,
                        'confirmation_ref' => $getBookingRefData['flights'][$i]['confirmationId'],
                        'payment_amount' => $amount,
                        'payment_status' => 'SUCCESS',
                        'user_id' => $userId,
                        'created_at' => $currentTime
                    ]);
                } else {

                    FlightResevation::create([
                        'booking_ref' => $BookRef,
                        'confirm_ref' => $getBookingRefData['flights'][$i]['confirmationId'],
                        'reservation_name' => $reserveName,
                        'contact_info' => $userEmail,
                        'flight_code' => $getBookingRefData['flights'][$i]['airlineCode'],
                        'flight_no' => $getBookingRefData['flights'][$i]['flightNumber'],
                        'flight_title' => $AirlineTitle[0]['AlternativeBusinessName'],
                        'departure_fromCode' => $getBookingRefData['flights'][$i]['fromAirportCode'],
                        'departure_fromTitle' => $FromTitle[0]['city_name'],
                        'arrival_toCode' => $getBookingRefData['flights'][$i]['toAirportCode'],
                        'arrival_toTitle' => $ToTitle[0]['city_name'],
                        'departure_time' => $getBookingRefData['flights'][$i]['departureDate'] . ' ' . $getBookingRefData['flights'][$i]['departureTime'],
                        'arrival_time' => $getBookingRefData['flights'][$i]['arrivalDate'] . ' ' . $getBookingRefData['flights'][$i]['arrivalTime'],
                        'total_duration' => $getBookingRefData['flights'][$i]['durationInMinutes'],
                        'dep_terminal' => $getBookingRefData['flights'][$i]['departureTerminalName'] ?? null, //$flightData['departureTerminalName'] ? $flightData['departureTerminalName'] : $flightData['arrivalTerminalName'],
                        'arr_terminal' => $getBookingRefData['flights'][$i]['arrivalTerminalName'] ?? null,
                        'baggage_details' => gettype($BaggageData) === 'string' ? $BaggageData : $bag[$i],
                        'flight_class' => $getBookingRefData['flights'][$i]['cabinTypeName'],
                        'booking_status' => 'CONFIRMED',
                        'user_id' => $userId,
                        'created_at' => $currentTime,
                        'hidden_stopCode' => null,
                        'hidden_stopTitle' => null,
                        'hidden_stopDeparture' => null,
                        'hidden_stopArrival' => null,
                        'order_id' => $oid
                    ]);

                    FlightPayment::create([
                        'booking_ref' => $BookRef,
                        'confirmation_ref' => $getBookingRefData['flights'][$i]['confirmationId'],
                        'payment_amount' => $amount,
                        'payment_status' => 'SUCCESS',
                        'user_id' => $userId,
                        'created_at' => $currentTime
                    ]);
                }
            }
        }

        return $this->sendMailAirTicket($BookRef);
    }


    public function sendMailAirTicket($id)
    {
        $booking_ref = $id; //$request['booking_ref'];

        // return $booking_ref;

        $ReservationData = DB::table('tbl_flight_resevation')->where('booking_ref', '=', $booking_ref)->get();
        $SingleDataSet = DB::table('tbl_flight_resevation')->where('booking_ref', '=', $booking_ref)->first();

        $UserEmail = $SingleDataSet->contact_info;
        $ConfirmNumber = $SingleDataSet->confirm_ref;
        $BookingNumber = $SingleDataSet->booking_ref;
        $reservationName = $SingleDataSet->reservation_name;

        $dataset = ['reserveData' => $ReservationData, 'booking_ref' => $BookingNumber, 'confirm_num' => $ConfirmNumber, 'reservation_name' => $reservationName];

        // date('H'.'hrs'.':')

        // return $dataset;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('Mails.AirlineTicket', $dataset);
        // return $pdf->download('pdf_file.pdf');

        // return view('Mails.AirlineTicket', $dataset);

        try {
            Mail::send('Mails.AirlineTicket', $dataset, function ($message) use ($UserEmail, $ConfirmNumber, $pdf) {
                $message->to($UserEmail);
                $message->subject('Confirmation Email on your Booking Reference: #' . $ConfirmNumber . '.');
                $message->attachData($pdf->output(), $ConfirmNumber . '_' . 'Aahaas_AirTicket.pdf', ['mime' => 'application/pdf',]);
            });

            return response()->json([
                'status' => 200,
                'message' => 'Booking Confirmed and Confirmation Mail sent your email'
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 400,
                'message' => $ex->getMessage()
            ]);
            // return 
        }

        // return $ReservationData;
    }

    public function cancelFlightBooking($bookingId)
    {
        $CancelArray = [];

        $Rest_URL = 'https://api.cert.platform.sabre.com/v1/trip/orders/cancelBooking';

        $CancelArray['targetPcc'] = 'Z7B8';
        $CancelArray['confirmationId'] = $bookingId;
        $CancelArray['cancelAll'] = true;

        $cancelResponse = Http::withHeaders($this->getHeaders())->post($Rest_URL, $CancelArray)->json();

        return $cancelResponse;
    }

    public function reValidatorTwoWayMultiCity(Request $request)
    {
    }

    public function ticketview()
    {
        return view('Mails.AirlineTicket');
    }

    //create pre booking for flight
    public function createPreBooking(Request $request)
    {
        try {
            $orderId = $request['order_id'];
            $userId = $request['user_id'];
            $userEmail = $request['user_email'];
            $fName = $request['first_name'];
            $lName = $request['surname'];
            $resName = $request['reservation_name'];
            $flightCode = $request['flight_code'];
            $flightNum = $request['flight_number'];
            $oriLoc = $request['ori_loccation'];
            $destLoc = $request['dest_loccation'];
            $depDateTime = $request['departure_datetime'];
            $currency = $request['currency'];
            $baseFair = $request['basefair'];
            $taxFair = $request['taxfair'];
            $totAmount = $request['total_amount'];
            $baggeges = $request['baggeges'];
            $paxCount = $request['pax_count'];
            $session = $request['session'];
            $contact_num = $request['contact_number'];

            $response = $this->FlightPreBook->createNewFlightPreBooking($orderId, $userId, $userEmail, $fName, $lName, $resName, $contact_num, $flightCode, $flightNum, $oriLoc, $destLoc, $depDateTime, $currency, $baseFair, $taxFair, $totAmount, $baggeges, $paxCount, $session);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
        //FlightPreBook
    }

    //get pre booking data by order id
    public function getFlightPreBookingData($id)
    {
        try {

            $response = $this->FlightPreBook->getPreBookDataById($id);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    //test route for flight booking ticketr
    public function getTicket(Request $request)
    {
        $Rest_URL = 'https://api.cert.platform.sabre.com/v1.3.0/air/ticket';

        $ticketRes = Http::withHeaders($this->getHeaders())->post($Rest_URL, $request)->json();

        return $ticketRes;
    }
}
