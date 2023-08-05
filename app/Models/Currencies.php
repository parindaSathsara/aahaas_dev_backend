<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class Currencies extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'table_currency';

    protected $fillable = [
        'base',
        'to_currency',
        'symbol',
        'code',
        'rate',
    ];

    public $timestamps = false;

    //fetching currency rates
    public function getCurrency($value)
    {
        try {

            $res1 = DB::table('table_currency')->where('base', $value)->get();

            // return $res1;

            $res2 = DB::table('currency_symbols')->where('currency', $value)->first();

            $currency_array = array();

            foreach ($res1 as $curr) {
                $currency_array[$curr->code] = [$curr->rate];
            }

            return response([
                'status' => 200,
                'base' => $value,
                'symbol' => $res2->symbol,
                'rates' => $currency_array
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //Currency conversion
    public function convertCurrency($currency_type, $amount)
    {
        try {

            $Rate = DB::table('table_currency')->where(['base' => 'LKR', 'to_currency' => $currency_type])->first();

            $Amount = (float)$amount / (float)$Rate->rate;

            return number_format((float)$Amount, 2, '.', '');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
