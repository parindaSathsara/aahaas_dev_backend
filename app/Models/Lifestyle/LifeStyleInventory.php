<?php

namespace App\Models\Lifestyle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class LifeStyleInventory extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_lifestyle_inventory';

    protected $fillable = [
        'lifestyle_id',
        'pickup_location',
        'inventory_date',
        'pickup_time',
        'max_adult_occupancy',
        'max_children_occupancy',
        'max_total_occupancy',
        'total_inventory',
        'allotment',
        'used',
        'balance',
        'vehicle_type',
        'inclusions',
        'exclusions',
        'longitude',
        'latitude',
        'updated_by',
    ];

    public $timestamps = false;

    public function createLifeStyleNewInventory($LifeStyle, $City, $InventoryDate, $StartTime, $EndTime, $MaxAdult, $MaxChild, $MaxTotal, $Allotments, $Used, $Balance, $VehicleType, $Inc, $Exc, $Lat, $Long, $Vendor)
    {
        try {
            LifeStyleInventory::create([
                'lifestyle_id' => $LifeStyle,
                'pickup_location' => $City,
                'inventory_date' => $InventoryDate,
                'pickup_time' => $StartTime . '-' . $EndTime,
                'max_adult_occupancy' => $MaxAdult,
                'max_children_occupancy' => $MaxChild,
                'max_total_occupancy' => $MaxTotal,
                'total_inventory' => $MaxAdult + $MaxChild,
                'allotment' => $Allotments,
                'used' => $Used,
                'balance' => $Balance,
                'vehicle_type' => $VehicleType,
                'inclusions' => $Inc,
                'exclusions' => $Exc,
                'longitude' => $Long,
                'latitude' => $Lat,
                'updated_by'=> $Vendor,
            ]);

            return response([
                'status' => 200,
                'data_response' => 'Data created'
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
