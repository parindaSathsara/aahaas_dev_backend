<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentIPG extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'payments_ipg';

    protected $fillable = [
        'order_id',
        'amount',
        'cx_email',
    ];

    public $timestamps = false;


    //create new payment ipg row
    public function createNewPaymentIpgRow($orderid, $amount, $cxemail)
    {

        try {

            PaymentIPG::create([
                'order_id' => $orderid,
                'amount' => $amount,
                'cx_email' => $cxemail,
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
