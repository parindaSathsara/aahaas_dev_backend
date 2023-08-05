<?php

namespace App\Models\Lifestyle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class LifeStyleDetails extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_lifestyle_detail';

    protected $fillable = [
        'lifestyle_id',
        'entrance',
        'guide',
        'meal',
        'meal_transfer',
        'covid_safe',
        'operating_dates',
        'operating_days',
        'opening_time',
        'closing_time',
        'closed_days',
        'closed_dates',
        'updated_by',
    ];

    public $timestamps = false;

    //fetch life style details
    public function fetchLifeStyleDetails()
    {
        try {

            $Query = DB::table('tbl_lifestyle_detail')->join('tbl_lifestyle', 'tbl_lifestyle_detail.lifestyle_id', '=', 'tbl_lifestyle.lifestyle_id')->get();

            return response([
                'status' => 200,
                'data_response' => $Query
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //create life style detail data row
    public function createLifeStyleDetailRow($LifeStyle, $Entrance, $Guide, $Meal, $MealTrans, $CovidTrans, $OperatingDates, $OperatingDays, $OpeningTime, $ClosingTime, $ClosedDays, $ClosedDates, $Vendor)
    {
        try {

            LifeStyleDetails::create([
                'lifestyle_id' => $LifeStyle,
                'entrance' => $Entrance,
                'guide' => $Guide,
                'meal' => $Meal,
                'meal_transfer' => $MealTrans,
                'covid_safe' => $CovidTrans,
                'operating_dates' => $OperatingDates,
                'operating_days' => $OperatingDays,
                'opening_time' => $OpeningTime,
                'closing_time' => $ClosingTime,
                'closed_days' => $ClosedDays,
                'closed_dates' => $ClosedDates,
                'updated_by' => $Vendor,
            ]);

            return response([
                'status' => 200,
                'message' => 'Data Saved'
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
