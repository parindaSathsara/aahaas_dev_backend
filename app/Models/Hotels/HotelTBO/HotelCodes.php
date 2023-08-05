<?php

namespace App\Models\Hotels\HotelTBO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelCodes extends Model
{
    use HasFactory;

    public static function getHotelCodesTBO()
    {
        $hotelCodes = [
            1000000,
            1000001,
            1000002,
            1000003,
            1000004,
            1000005,
            1000006,
            1000007,
            1000008,
            1000009,
            1000011,
            1000012,
            1000013,
            1000014,
            1000016,
            1000018,
            1000019,
            1000020,
            1000025,
            1000026,
            1000027,
            1000028,
            1000029,
            1000030,
            1000031,
            1000032,
            1000037,
            1000038,
            1000040,
            1000041,
            1000042,
            1000043,
            1000044,
            1000045,
            1000046,
            1000051
        ];

        return $hotelCodes;
    }
}
