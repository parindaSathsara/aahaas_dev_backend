<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;


class HotelInventory extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_inventory';

    protected $fillable = [
        'hotel_id',
        'room_category',
        'room_type',
        'no_of_rooms',
        'no_of_beds',
        'max_adult_occupancy',
        'max_child_occupancy',
        'total_inventory',
        'allotment',
        'used',
        'balance',
        'updated_by'
    ];

    public $timestamps = false;

    //create new life style inventory
    public function createNewHotelInventoryRow($HotelName, $RoomCat, $RoomType, $NoOfRooms, $NoOfBeds, $MaxAdult, $MaxChild, $MinChild, $TotalInven, $Allotments, $Used, $Balance)
    {
        try {

            HotelInventory::create([
                'hotel_id' => $HotelName,
                'room_category' => $RoomCat,
                'room_type' => $RoomType,
                'no_of_rooms' => $NoOfRooms,
                'no_of_beds' => $NoOfBeds,
                'max_adult_occupancy' => $MaxAdult,
                'max_child_occupancy' => $MaxChild,
                'total_inventory' => $TotalInven,
                'allotment' => $Allotments,
                'used' => $Used,
                'balance' => $Balance,
                'updated_by' => '-'
            ]);

            return response([
                'status' => 200,
                'response' => 'Data Inserted'
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //fetch all inventories
    public function fetchAllInventories()
    {
        $Query = DB::table('tbl_hotel_inventory')->get();

        return response([
            'status' => 200,
            'response' => $Query
        ]);
    }
}
