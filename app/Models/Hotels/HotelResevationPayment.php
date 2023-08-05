<?php

namespace App\Models\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HotelResevationPayment extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_hotel_resevation_payments';

    protected $fillable = [
        'resevation_no',
        'total_amount',
        'paid_amount',
        'balance_payment',
        'amendment_refund',
        'payment_method',
        'payment_status',
        'booking_status',
        'payment_slip_image'
    ];

    public $timestamps = false;

    //create a new hotel reservation payment

    public function createNewHotelReservationPayment($bookingRef, $totalAmount)
    {
        try {
            HotelResevationPayment::create([
                'resevation_no' => $bookingRef,
                'total_amount' => $totalAmount,
                'paid_amount' => 0.00,
                'balance_payment' => $totalAmount,
                'payment_method' => 'Card',
                'payment_status' => 'Paid',
                'payment_slip_image' => '-'
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
