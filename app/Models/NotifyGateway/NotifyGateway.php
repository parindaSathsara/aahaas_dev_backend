<?php

namespace App\Models\NotifyGateway;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class NotifyGateway extends Model
{
    use HasFactory;

    public $userid;
    public $APIKEY;
    public $senderid;

    public function __construct()
    {
        $this->userid = '24774';
        $this->APIKEY = 'O1AjL4GTK03H0JeVj411';
        $this->senderid = 'NotifyDEMO';
    }

    //send sms to driver with their username and password
    public function sendRegisrationDriverSms($to_number, $username, $password)
    {
        try {
            $SmsArray = [];

            $sms_msg = 'Hi ' . $username . ', Please use below username and password to login to your aahaas driver account. Username: ' . $username . ' & Password: ' . $password . '. Team Aahaas';

            $SmsArray['user_id'] = $this->userid;
            $SmsArray['api_key'] = $this->APIKEY;
            $SmsArray['sender_id'] = $this->senderid;
            $SmsArray['to'] = $to_number;
            $SmsArray['message'] = $sms_msg;

            $response = Http::post('https://app.notify.lk/api/v1/send', $SmsArray);

            return response([
                'status' => 200,
                'data_response' => $response
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
