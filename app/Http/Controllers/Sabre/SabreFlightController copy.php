<?php

namespace App\Http\Controllers\Sabre;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sabre\SabreFlightConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use IlluminateAgnostic\Collection\Support\Arr;
use PHPUnit\Framework\Constraint\Count;
use Symfony\Component\Console\Input\Input;

use function PHPSTORM_META\type;

class SabreFlightBackup extends Controller
{

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
        $ValidateArray['OTA_AirLowFareSearchRQ']['Version'] = '6.1.0';
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
        // return $flights;

        if (count($MainDepartureOne) >= 1) {
            for ($i = 0; $i < count($MainDepartureOne); $i++) {
                $ValidateArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'] =
                    ['RPH' => (string)($i + 1), 'DepartureDateTime' => $MainDepartureOne[$i], 'OriginLocation' => ['LocationCode' => $OriginLocation[$i]], 'DestinationLocation' => ['LocationCode' => $DestinationLocation[$i]], 'TPA_Extensions' => ['Flight' => $flights]];
            }
        }


        $ValidateArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'] = '50ITINS';


        $SubArray = [];

        $SubArray['OTA_AirLowFareSearchRQ'] = $ValidateArray['OTA_AirLowFareSearchRQ'];
        $SubArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail'] = [$ValidateArray['OTA_AirLowFareSearchRQ']['TravelerInfoSummary']['AirTravelerAvail']];
        $SubArray['OTA_AirLowFareSearchRQ']['POS']['Source'] = [$ValidateArray['OTA_AirLowFareSearchRQ']['POS']['Source']];
        $SubArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'] = [$ValidateArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation']];
        $SubArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'] = $ValidateArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'];


        try {

            $getReValidityData = Http::withHeaders($this->getHeaders())->post($ReValidateURL, $SubArray)->json();
            return response([
                'status' => 200,
                'data_set' => $getReValidityData,
                // 'data' => $MainDepartureOne
            ]);
        } catch (\Exception $ex) {
            return response([
                'exception_message' => $ex->getMessage()
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

        // **** ---Main Array Begin--- **** //

        $MainArray['OTA_AirLowFareSearchRQ']['Version'] = 'v4';
        // $MainArray['OTA_AirLowFareSearchRQ']['AvailableFlightsOnly'] = true;
        $MainArray['OTA_AirLowFareSearchRQ']['ResponseType'] = 'OTA';
        $MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']['PseudoCityCode'] = 'Z7B8';
        $MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['Type'] = '1';
        $MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['ID'] = '1';
        $MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['CompanyName']['Code'] = 'TN';
        // $MainArray['OTA_AirLowFareSearchRQ']['POS']['Source']['RequestorID']['CompanyName']['Content'] = 'TN';


        if (count($TripType) >= 1) {
            for ($x = 1; $x <= count($TripType); $x++) {

                if (in_array('One Way', $TripType)) {
                    $MainArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'][] = ['RPH' => (string)$x, 'DepartureDateTime' => $DepartureDate, 'OriginLocation' => ['LocationCode' => $FromLocation], 'DestinationLocation' => ['LocationCode' => $ToLocation]];
                } else if (in_array('Round Trip', $TripType)) {
                    $MainArray['OTA_AirLowFareSearchRQ']['OriginDestinationInformation'][] = ['RPH' => (string)$x, 'DepartureDateTime' => $ReturnDate, 'OriginLocation' => ['LocationCode' => $ToLocation], 'DestinationLocation' => ['LocationCode' => $FromLocation]];
                }
            }
        }


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


        $MainArray['OTA_AirLowFareSearchRQ']['TPA_Extensions']['IntelliSellTransaction']['RequestType']['Name'] = '50ITINS';

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

        // return $SubArray;





        // try {

        $getFilghtAvailability = Http::withHeaders($this->getHeaders())->post($Rest_URL, $SubArray)->json();

        $itenararyGroups = $getFilghtAvailability['groupedItineraryResponse']['itineraryGroups'];
        $legDescs = $getFilghtAvailability['groupedItineraryResponse']['legDescs'];
        $scheduleDescs = $getFilghtAvailability['groupedItineraryResponse']['scheduleDescs'];
        // $fareComponentDescs = $getFilghtAvailability['groupedItineraryResponse']['fareComponentDescs'];
        // $taxDescs = $getFilghtAvailability['groupedItineraryResponse']['taxDescs'];
        // $taxSummaryDescs = $getFilghtAvailability['groupedItineraryResponse']['taxSummaryDescs'];

        // for ($i = 0; $i < count($itenararyGroups); $i++) {
        //     $flightData[] = $itenararyGroups[$i]['itineraries'];
        //     for ($x = 0; $x < count($flightData[$i]); $x++) {
        //         $flightItenararyFinalData[] = $flightData[$i][$x];
        //     }
        // }


        // $fareDataSet = [];

        // for ($i = 0; $i < count($flightItenararyFinalData); $i++) {
        //     $flightRefs[] = $flightItenararyFinalData[$i]['pricingInformation'];

        // foreach ($flightItenararyFinalData[$i]['pricingInformation'] as $mapData) {
        //     foreach ($mapData['fare']['passengerInfoList'] as $pricingInf) {

        //         foreach ($pricingInf['passengerInfo']['fareComponents'] as $fareRefs) {
        //             foreach ($fareComponentDescs as $fareData) {
        //                 if ($fareRefs['ref'] == $fareData['id']) {
        //                     $fareDataSet[] = ['index' => $i, $fareData];
        //                 }
        //             }
        //         }


        //         foreach ($pricingInf['passengerInfo']['taxes'] as $taxes) {
        //             foreach ($taxDescs as $taxesDesc) {
        //                 if ($taxes['ref'] == $taxesDesc['id']) {
        //                     $taxesDataSet[] = ['index' => $i, $taxesDesc];
        //                 }
        //             }
        //         }

        //         foreach ($pricingInf['passengerInfo']['taxSummaries'] as $taxSumDet) {
        //             foreach ($taxSummaryDescs as $taxesSummary) {
        //                 if ($taxSumDet['ref'] == $taxesSummary['id']) {
        //                     $taxSummary[] = ['index' => $i, $taxesSummary];
        //                 }
        //             }
        //         }
        //     }
        // }

        // foreach ($flightItenararyFinalData[$i]['legs'] as $itenaryRef) {
        //     foreach ($legDescs as $legData) {
        //         if ($legData['id'] == $itenaryRef['ref']) {
        //             foreach ($legData['schedules'] as $scheduledRefs) {
        //                 foreach ($scheduleDescs as $schedule) {
        //                     if ($scheduledRefs['ref'] == $schedule['id']) {
        //                         $scheduleData[] = $schedule;
        //                     }
        //                 }
        //             }
        //             // $fareDetail = "";
        //             $fareDetail = Arr::where($fareDataSet, function ($refComponnet, $key) use ($i) {
        //                 return $refComponnet['index'] === $i;
        //             });
        //             $taxData = Arr::where($taxesDataSet, function ($taxComponent, $key) use ($i) {
        //                 return $taxComponent['index'] === $i;
        //             });

        //             $taxSum = Arr::where($taxSummary, function ($taxesSum, $key) use ($i) {
        //                 return $taxesSum['index'] === $i;
        //             });


        //             $forData[] = ['refKey' => $itenaryRef['ref'], 'scheduleRefs' => $legData['schedules'], 'scheduleData' => $scheduleData, 'fareData' => $fareDetail, 'taxesData' => $taxData,'taxesSummary'=>$taxSum, 'dataFlight' => $flightItenararyFinalData[$i]];
        //             $scheduleData = [];
        //         }
        //     }
        // }
        // }

        // foreach ($itenaryFlight['pricingInformation'] as $mapData) {

        //     foreach ($mapData['fare']['passengerInfoList'] as $pricingInf) {

        //         foreach ($pricingInf['passengerInfo']['fareComponents'] as $fareRefs) {
        //             foreach ($fareComponentDescs as $fareData) {
        //                 if ($fareRefs['ref'] === $fareData['id']) {

        //                     $fareDataSet[] = $fareData;
        //                 }
        //             }
        //         }


        //         foreach ($pricingInf['passengerInfo']['taxes'] as $taxes) {

        //             foreach ($taxDescs as $taxesDesc) {

        //                 if ($taxes['ref'] === $taxesDesc['id']) {

        //                     $taxesDataSet[] = $taxesDesc;
        //                 }
        //             }
        //         }

        //         foreach ($pricingInf['passengerInfo']['taxSummaries'] as $taxSumDet) {
        //             foreach ($taxSummaryDescs as $taxesSummary) {
        //                 if ($taxSumDet['ref'] === $taxesSummary['id']) {
        //                     $taxSummary[] =  $taxesSummary;
        //                 }
        //             }
        //         }
        //     }
        // }




        $x = [];
        $it = [];

        foreach ($itenararyGroups as $itenararyGroup) {
            foreach ($itenararyGroup['itineraries'] as $itenaryFlight) {
                foreach ($itenaryFlight['legs'] as $itenaryRef) {
                    foreach ($legDescs as $legData) {
                        if ($legData['id'] == $itenaryRef['ref']) {
                            foreach ($legData['schedules'] as $scheduledRefs) {
                                foreach ($scheduleDescs as $schedule) {
                                    if ($scheduledRefs['ref'] == $schedule['id']) {

                                        $scheduleData[] = $schedule;
                                    }
                                }
                            }
                            // $forData[] = ['refKey' => $itenaryRef['ref'], 'id' => $itenaryFlight['id'], 'fare' => $fareDataSet, 'taxesDataSet' => $taxesDataSet, 'taxSummary' => $taxSummary, 'itenaryGroupData' => $itenararyGroup['groupDescription'], 'legsData' => $legData, 'scheduleData' => $scheduleData,  'dataFlight' => $itenaryFlight];
                            $forData[] = ['refKey' => $itenaryRef['ref'], 'id' => $itenaryFlight['id'], 'itenaryGroupData' => $itenararyGroup['groupDescription'], 'legsData' => $legData, 'scheduleData' => $scheduleData,  'dataFlight' => $itenaryFlight];
                            $scheduleData = [];
                        }
                    }
                }
                $scheduleData = [];
            }
        }


        $filtered_collection = Arr::where($forData, function ($value, $key) use ($FromLocation) {
            foreach ($value['scheduleData'] as $scheduleValue) {
                return $scheduleValue['departure']['airport'] == $FromLocation;
            }
        });




        $requestNew = new \Illuminate\Http\Request();
        $requestNew->setMethod('POST');


        foreach ($filtered_collection as $collectionData) {
            foreach ($collectionData['scheduleData'] as $scheduleData) {
                $fldate[] = $DepartureDate;
                $departure[] = $scheduleData['departure']['airport'];
                $arrival[] = $scheduleData['arrival']['airport'];
                $flightCode[] = $scheduleData['carrier']['marketing'];
                $flightNumber[] = $scheduleData['carrier']['marketingFlightNumber'];
            }

            $uniqueArrCodes = array_unique($flightCode);
            $uniqueArrNumbers = array_unique($flightNumber);

            if (count($uniqueArrCodes) == 1 && count($uniqueArrNumbers) == 1) {
                $finalFlightCodes = $flightCode[0];
                $finalFlightNumbers = $flightNumber[0];
            } else {
                $finalFlightCodes = implode(',', $flightCode);
                $finalFlightNumbers = implode(',', $flightNumber);
            }

            $requestNew->request->add([
                'maindep_datetime_one' => $DepartureDate,
                'departure_datetime' => implode(',', $fldate),
                'arrival_datetime' => implode(',', $fldate),
                'origin_location' => implode(',', $departure),
                'dest_location' => implode(',', $arrival),
                'passenger_type' => $request->input('passenger_type'),
                'seat_count' => $request->input('seat_count'),
                'flight_code' => $finalFlightCodes,
                'flight_number' => $finalFlightNumbers,
                'main_origin_location' => $departure[0],
                'main_dest_location' =>  $arrival[count($arrival) - 1]
            ]);


            $validatedData[] = ['id' => $collectionData['id'], 'validateData' => $this->reValidatingFlightDetails(
                $requestNew
            )];

            $departure = [];
            $arrival = [];
            $flightCode = [];
            $flightNumber = [];
            $fldate = [];
        }

        return $validatedData;





        // return response([
        //     'status' => 200,
        //     'data_set' => $filtered_collection,
        //     // 'sabreData' => $getFilghtAvailability,
        // ]);
    }








    /* Ticket Booking Process____ Booking creation  */
    public function confirmBooking(Request $request)
    {
        $Rest_URL = 'https://api.cert.platform.sabre.com/v2.4.0/passenger/records?mode=create';
        $BookingArray = [];

        $PaxEmail = explode(',', $request->input('pax_email'));
        $ContactNumber = explode(',', $request->input('contact_number'));
        $PersonFirstName = explode(',', $request->input('first_name'));
        $PersonSurname = explode(',', $request->input('surname'));
        $FlightCodes = explode(',', $request->input('flight_code'));
        $FlightNumber = explode(',', $request->input('flight_number'));
        $DestinationLocation = explode(',', $request->input('dest_loccation'));
        $OriLocation = explode(',', $request->input('ori_loccation'));

        //* Flight Details *// 
        $DepartureDateTime = explode(',', $request->input('departure_datetime'));

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
                $BookingArray['CreatePassengerNameRecordRQ']['TravelItineraryAddInfo']['CustomerInfo']['ContactNumbers']['ContactNumber'][] = ['Phone' => $ContactNumber[$x], 'PhoneUseType' => 'M'];
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
                    ['DepartureDateTime' => $DepartureTimeArray[$c], 'FlightNumber' => $FlightNumber[$c], 'NumberInParty' => '1', 'ResBookDesigCode' => 'Y', 'Status' => 'NN', 'DestinationLocation' => ['LocationCode' => $DestinationLocation[$c]], 'MarketingAirline' => ['Code' => $FlightCodes[$c], 'FlightNumber' => $FlightNumber[$c]], 'OriginLocation' => ['LocationCode' => $OriLocation[$c]]];
            }
        }


        $BookingArray['CreatePassengerNameRecordRQ']['AirBook']['RedisplayReservation'] = ['NumAttempts' => 3, 'WaitInterval' => 3000];


        $BookingArray['CreatePassengerNameRecordRQ']['PostProcessing']['EndTransaction']['Source']['ReceivedFrom'] = 'API';

        $BookingResponse = Http::withHeaders($this->getHeaders())->post($Rest_URL, $BookingArray)->json();

        return $BookingResponse;
    }

    /* Get Booking By Booking Confirmation Reference Number */
    public function getBookingDetails(Request $request)
    {
        $BookingDetails = [];

        $Rest_URL = 'https://api.cert.platform.sabre.com/v1/trip/orders/getBooking';

        $BookingDetails['confirmationId'] = $request->input('booking_ref');
        $BookingDetails['bookingSource'] = 'SABRE';

        $getBookingRefData = Http::withHeaders($this->getHeaders())->post($Rest_URL, $BookingDetails)->json();

        return $getBookingRefData;
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

    public function ticketview()
    {
        return view('Mails.AirlineTicket');
    }
}
