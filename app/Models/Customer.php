<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_customer';

    protected $fillable = [
        'customer_id',
        'customer_fname',
        'contact_number',
        'customer_email',
        'customer_nationality',
        'customer_profilepic',
        'customer_status',
        'customer_address',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;

    //deactivate customer profile
    public function deactCustomerAccount($id)
    {
        try {
            DB::table('tbl_customer')->where('customer_id', $id)->update(['customer_status' => 'Deactivate']);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
