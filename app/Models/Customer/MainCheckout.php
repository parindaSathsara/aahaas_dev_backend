<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MainCheckout extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_checkouts';

    protected $fillable = [
        'checkout_id',
        'essnoness_id',
        'lifestyle_id',
        'education_id',
        'hotel_id',
        'flight_id',
        'main_category_id',
        'quantity',
        'each_item_price',
        'total_price',
        'discount_price',
        'bogof_item_name',
        'delivery_charge',
        'discount_type',
        'child_rate',
        'adult_rate',
        'discountable_child_rate',
        'discountable_adult_rate',
        'flight_trip_type',
        'flight_total_price',
        'related_order_id',
        'currency',
        'status',
        'delivery_status',
        'delivery_date',
        'delivery_address',
        'cx_id',
    ];

    public $timestamps = false;

    //create new row
    public function createNewRow($orderId, $baseFair, $currency, $cxId, $preid)
    {
        MainCheckout::create([
            'checkout_id' => $orderId,
            'essnoness_id',
            'lifestyle_id',
            'education_id',
            'hotel_id',
            'flight_id' => $preid,
            'main_category_id' => '6',
            'quantity',
            'each_item_price',
            'total_price' => $baseFair,
            'discount_price',
            'bogof_item_name',
            'delivery_charge',
            'discount_type',
            'child_rate',
            'adult_rate',
            'discountable_child_rate',
            'discountable_adult_rate',
            'flight_trip_type',
            'flight_total_price',
            'related_order_id' => $preid,
            'currency' => $currency,
            'status' => 'Booked',
            'delivery_status',
            'delivery_date',
            'delivery_address',
            'cx_id' => $cxId,
        ]);
    }

    //hotelbeds checkout
    public function checkoutOrderHotel($oid, $hotelid, $totalAmount, $currency, $user_Id, $hotelpreid)
    {
        try {
            MainCheckout::create([
                'checkout_id' => $oid,
                'essnoness_id' => null,
                'lifestyle_id' => null,
                'education_id' => null,
                'hotel_id' => $hotelid,
                'flight_id' => null,
                'main_category_id' => '4',
                'quantity' => null,
                'each_item_price' => null,
                'total_price' => $totalAmount,
                'discount_price' => 0.00,
                'bogof_item_name' => null,
                'delivery_charge' => null,
                'discount_type' => null,
                'child_rate' => '-',
                'adult_rate' => '-',
                'discountable_child_rate' => null,
                'discountable_adult_rate' => null,
                'flight_trip_type' => null,
                'flight_total_price' => null,
                'related_order_id' => $hotelpreid,
                'currency' => $currency,
                'status' => 'Booked',
                'delivery_status' => null,
                'delivery_date' => null,
                'delivery_address' => null,
                'cx_id' => $user_Id,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
