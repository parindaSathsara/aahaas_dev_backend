<?php

namespace App\Models\VendorUser;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;

class VendorUser extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'vendor_users';

    protected $fillable = [
        'auto_id',
        'country',
        'login_email',
        'password',
        'status',
        'user_id'
    ];

    //create seller login
    public function createSellerLogin($auto_id, $country, $loginemail, $loginpassword, $userid)
    {

        $Count1 = DB::table('vendor_users')->where('login_email', $loginemail)->count();
        $Count2 = DB::table('vendor_users')->where('user_id', $userid)->count();

        if ($Count1 || $Count2 > 0) {
            return response([
                'status' => 501,
                'message' => 'failed'
            ]);
        } else {

            $newLogin = VendorUser::create([
                'auto_id' => $auto_id,
                'country' => $country,
                'login_email' => $loginemail,
                'password' => Hash::make($loginpassword),
                'status' => 'Pending',
                'user_id' => $userid
            ]);
        }


        if ($newLogin) {

            return response([
                'status' => 200,
                'login_data' => $newLogin,
                'mail_response' => $this->sendVerifyEmail($loginemail)
            ]);
        } else {
            return response([
                'status' => 500,
                'message' => 'failed'
            ]);
        }
    }

    public function sendVerifyEmail($email)
    {

        try {
            $otp_code = mt_rand(100000, 900000);

            DB::table('vendor_otp')->insert([
                'otp' => $otp_code,
                'email' => $email
            ]);

            $dataset = ['otp' => $otp_code];
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('SellerRegistration.SellerRegistration', $dataset);

            Mail::send('SellerRegistration.SellerRegistration', $dataset, function ($message) use ($email) {
                $message->to($email);
                $message->subject('Aahaas Seller - Registration Code');
            });

            return response([
                'status' => 200,
                'message' => 'Seller registration mail sent'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //code verify
    public function verifySecurityCode($code, $email)
    {
        try {

            $query = DB::table('vendor_otp')->where(['otp' => $code, 'email' => $email])->orderBy('otp', 'DESC')->first();

            // return $code;

            if ($query === null) {

                return response([
                    'status' => 500,
                    'data_response' => 'Wrong verification code!'
                ]);
            } else {
                return response([
                    'status' => 200,
                    'data_response' => $query
                ]);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function checkRegisteredUsers($id)
    {
        try {
            $count =  DB::table('vendor_users')->where('user_id', $id)->count();

            return response([
                'status' => 200,
                'count' => $count
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
