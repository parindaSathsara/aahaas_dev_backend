<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotels\HotelInventory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HotelInventoryController extends Controller
{

    public $hotelinventory;

    public function __construct() {
        $this->hotelinventory = new HotelInventory();
    }

    /* fetch all the inventory details function starting */
    public function index()
    {
        $allInventoryDetails = DB::table('tbl_hotel_inventory')->get();

        return response()->json([
            'status' => 200,
            'inventory_data' => $allInventoryDetails
        ]);
    }
    /* fetch all the inventory details function ending */

    /* create new hotel inventory function starting */
    public function createNewInventoryDetails(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $room_category = $request->input('room_category');
            $room_type = $request->input('room_type');
            $no_of_rooms = $request->input('no_of_rooms');
            $no_of_beds = $request->input('no_of_beds');
            $max_adult_occu = $request->input('max_adult_occupancy');
            $max_child_occu = $request->input('max_child_occupancy');
            $allotment = $request->input('allotment');
            $used = $request->input('used');
            $balance = $request->input('balance');
            // $child_age_range = $request->input('child_age_range');

            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required',
                'room_type' => 'required',
                'room_category' => 'required',
                'no_of_rooms' => 'required',
                'no_of_beds' => 'required',
                'max_adult_occupancy' => 'required',
                'max_child_occupancy' => 'required',
                'allotment' => 'required',
                'used' => 'required',
                'balance' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            }
            if (!Auth::check()) {
                return response()->json([
                    'status' => 403,
                    'login_error' => 'Session Expired, Please log again'
                ]);
            }

            /* Validations for Room Type */

            if ($room_type == 'Single') {
                if ($max_adult_occu > 1) {
                    return response()->json([
                        'status' => 403,
                        'error_message' => 'Maximum adult px count 1'
                    ]);
                }
            } else if ($room_type == 'Double') {
                if ($max_adult_occu > 2) {
                    return response()->json([
                        'status' => 403,
                        'error_message' => 'Maximum adult px count is 2'
                    ]);
                }
            } else if ($room_type == 'Triple') {
                if ($max_adult_occu > 3) {
                    return response()->json([
                        'status' => 403,
                        'error_message' => 'Maximum adult px count is 3'
                    ]);
                }
            } else if ($room_type == 'Quadtriple') {
                if ($max_adult_occu > 4) {
                    return response()->json([
                        'status' => 403,
                        'error_message' => 'Maximum adult px count is 4'
                    ]);
                }
            }

            $total_inventory = (int)$max_adult_occu + (int)$max_child_occu;

            $newInventoryData = HotelInventory::create([
                'hotel_id' => $request->input('hotel_id'),
                'room_category' => $request->input('room_category'),
                'room_type' => $request->input('room_type'),
                'no_of_rooms' => $no_of_rooms,
                'no_of_beds' => $no_of_beds,
                'max_adult_occupancy' => $max_adult_occu,
                'max_child_occupancy' => $max_child_occu,
                'total_inventory' => $total_inventory,
                'allotment' => $allotment,
                'used' => $used,
                'balance' => $balance,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
                'updated_by' => $request->input('updated_by')
            ]);

            return response()->json([
                'status' => 200,
                'hotel_success' => 'Hotel Incentory Data added to system',
                'total_inventory' => $total_inventory
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* create new hotel inventory function ending */

    /* fetch hotel inventory details by id function starting */
    public function fetchDetailsById($id)
    {
        $detailsById = HotelInventory::find($id)->first();

        return response()->json([
            'status' => 200,
            'inventoryDetails' => $detailsById
        ]);
    }
    /* fetch hotel inventory details by id function ending */

    /* Update hotel inventory details function starting */
    public function updateHotelInventoryDetails(Request $request, $id)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();

            $room_category = $request->input('room_category');
            $room_type = $request->input('room_type');
            $no_of_rooms = $request->input('no_of_rooms');
            $no_of_beds = $request->input('no_of_beds');
            $max_adult_occupancy = $request->input('max_adult_occupancy');
            $max_child_occupancy = $request->input('max_child_occupancy');
            $allotment = $request->input('allotment');
            $used = $request->input('used');

            $validator = Validator::make($request->all(), [
                'room_type' => 'required',
                'room_category' => 'required',
                'no_of_rooms' => 'required',
                'no_of_beds' => 'required',
                'max_adult_occupancy' => 'required',
                'max_child_occupancy' => 'required',
                'allotment' => 'required',
                'used' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'validation_error' => $validator->messages()
                ]);
            }
            if (!Auth::check()) {
                return response()->json([
                    'status' => 403,
                    'login_error' => 'Session Expired, Please log again'
                ]);
            }

            /* Validations for Room Type */

            if ($room_type == 'Single') {
                if ($max_adult_occupancy > 1) {
                    return response()->json([
                        'status' => 403,
                        'error_message' => 'Maximum adult px count 1'
                    ]);
                }
            } else if ($room_type == 'Double') {
                if ($max_adult_occupancy > 2) {
                    return response()->json([
                        'status' => 403,
                        'error_message' => 'Maximum adult px count is 2'
                    ]);
                }
            } else if ($room_type == 'Triple') {
                if ($max_adult_occupancy > 3) {
                    return response()->json([
                        'status' => 403,
                        'error_message' => 'Maximum adult px count is 3'
                    ]);
                }
            } else if ($room_type == 'Quadtriple') {
                if ($max_adult_occupancy > 4) {
                    return response()->json([
                        'status' => 403,
                        'error_message' => 'Maximum adult px count is 4'
                    ]);
                }
            }

            $total_inventory = (int)$max_adult_occupancy + (int)$max_child_occupancy;
            $balance = (int)$allotment - (int)$used;

            //update query for update inventory details
            $updateInventoryData = DB::select(DB::raw("UPDATE tbl_hotel_inventory SET room_category='$room_category', room_type='$room_type', no_of_rooms='$no_of_rooms',
            no_of_beds='$no_of_beds', max_adult_occupancy='$max_adult_occupancy', max_child_occupancy='$max_child_occupancy', total_inventory='$total_inventory',
            allotment='$allotment', used='$used', balance='$balance', updated_at='$currentTime' WHERE id='$id'"));

            return response()->json([
                'status' => 200,
                'message' => 'Data updated successfully'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 401,
                'message' => throw $exception
            ]);
        }
    }
    /* Update hotel inventory details function ending */

    /* Fetch data of Inventory details with hotel name function starting */
    public function fetchDetailsWithHotelName()
    {
        $detailsWithHotelName = DB::table('tbl_hotel')
            ->join('tbl_hotel_inventory', 'tbl_hotel.id', '=', 'tbl_hotel_inventory.hotel_id')
            ->select(
                'tbl_hotel.hotel_name',
                'tbl_hotel_inventory.room_category',
                'tbl_hotel_inventory.room_type',
                'tbl_hotel_inventory.no_of_rooms',
                'tbl_hotel_inventory.no_of_beds',
                'tbl_hotel_inventory.max_adult_occupancy',
                'tbl_hotel_inventory.max_child_occupancy',
                'tbl_hotel_inventory.total_inventory',
                'tbl_hotel_inventory.allotment',
                'tbl_hotel_inventory.used',
                'tbl_hotel_inventory.balance'
            )
            ->get();

        return response()->json([
            'status' => 200,
            'dataWithHotel' => $detailsWithHotelName
        ]);
    }
    /* Fetch data of Inventory details with hotel name function ending */

    //create new hotel inventory data row
    public function createNewHotelInventory(Request $request)
    {
        try {
            
            $HotelName = $request['hotel_name'];
            $RoomCat = $request['room_category'];
            $RoomType = $request['room_type'];
            $NoOfRooms = $request['no_of_rooms'];
            $NoOfBeds = $request['no_of_beds'];
            $MaxAdult = $request['max_adult'];
            $MaxChild = $request['max_child'];
            $MinChild = $request['min_child'];
            $TotalInven = $request['total_inven'];
            $Allotments = $request['allotments'];
            $Used = $request['used'];
            $Balance = $request['balance'];


            $response = $this->hotelinventory->createNewHotelInventoryRow($HotelName, $RoomCat,$RoomType,$NoOfRooms,$NoOfBeds,$MaxAdult,$MaxChild,$MinChild, $TotalInven, $Allotments,$Used, $Balance);

            return $response;

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
