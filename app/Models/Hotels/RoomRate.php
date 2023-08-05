<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class RoomRate extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_room_rate';

    protected $fillable = [
        'hotel_id',
        'booking_startdate',
        'booking_enddate',
        'travel_startdate',
        'travel_enddate',
        'room_category',
        'room_type',
        'meal_plan',
        'Market_nationality',
        'currency',
        'adult_rate',
        'child_withbed_rate',
        'child_withoutbed_rate',
        'supplement_type',
        'compulsory',
        'service_type',
        'service',
        'package_addpax_rate',
        'package_childwith_bed_rate',
        'package_childno_bed_rate',
        'child_foc_age',
        'child_withno_bed_age',
        'child_withbed_age',
        'adult_age',
        'selling_point',
        'book_by_days',
        'special_rate',
        'payment_Policy',
        'cancellation_days_before',
        'cancellation_policy',
        'blackoutdates',
        'blackoutdays',
        'stop_sales_startdate',
        'stop_sales_enddate',
        'updated_by'
    ];

    public $timestamps = false;

    //create new hotel room rate
    public function createNewHotelRoomRate($HotelName, $BookinStartDate, $BookingEndDate, $TravelStartDate, $TravelEndDate, $RoomCategory, $RoomType, $MealPlan, $Market, $Currency, $AdultRate, $ChildWithBed, $ChildWithoutBed, $SupType, $Compulsory, $ServiceType, $Service, $PackageAddPaxRate, $PackageChildWBRate, $PackageChildWORate, $ChildFocAge,  $ChidlWOAge, $ChildWithBedRate, $AdultAge, $SellingPoint, $BookByDays, $SpecialRate, $PaymentPolicy, $CancelDaysBefore, $CancelPolicy, $BlackoutDates, $BlackoutDays)
    {
        try {

            RoomRate::create([
                'hotel_id' => $HotelName,
                'booking_startdate' => $BookinStartDate,
                'booking_enddate' => $BookingEndDate,
                'travel_startdate' => $TravelStartDate,
                'travel_enddate' => $TravelEndDate,
                'room_category' => $RoomCategory,
                'room_type' => $RoomType,
                'meal_plan' => $MealPlan,
                'Market_nationality' => $Market,
                'currency' => $Currency,
                'adult_rate' => $AdultAge,
                'child_withbed_rate' => $ChildWithBedRate,
                'child_withoutbed_rate' => $ChildWithoutBed,
                'supplement_type' => $SupType,
                'compulsory' => $Compulsory,
                'service_type' => $ServiceType,
                'service' => $Service,
                'package_addpax_rate' => $PackageAddPaxRate,
                'package_childwith_bed_rate' => $PackageChildWBRate,
                'package_childno_bed_rate' => $PackageChildWORate,
                'child_foc_age' => $ChildFocAge,
                'child_withno_bed_age' => $ChidlWOAge,
                'child_withbed_age' => $ChildWithBedRate,
                'adult_age' => $AdultAge,
                'selling_point' => $SellingPoint,
                'book_by_days' => $BookByDays,
                'special_rate' => $SpecialRate,
                'payment_Policy' => $PaymentPolicy,
                'cancellation_days_before' => $CancelDaysBefore,
                'cancellation_policy' => $CancelPolicy,
                'blackoutdates' => $BlackoutDates,
                'blackoutdays' => $BlackoutDays,
                'stop_sales_startdate' => '-',
                'stop_sales_enddate' => '-',
                'updated_by' => '-'
            ]);

            return response([
                'status' => 200,
                'response' => 'Data Created'
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
