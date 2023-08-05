<?php

namespace App\Models\Driver;

use App\Models\NotifyGateway\NotifyGateway;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class DriverReg extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_driver';

    public $smsGateway;

    protected $fillable = [
        'driver_name',
        'contact_number',
        'nic',
        'profile_image',
        'vehicle_type',
        'vehicle_no',
        'other_documents',
        'other_remarks',
        'username',
        'password',
        'status'
    ];

    public $timestamps = false;

    public function __construct()
    {
        $this->smsGateway = new NotifyGateway();
    }

    //get registered drivers
    public function fetchRegisteredDrivers()
    {
        $driver_set = DB::table('tbl_driver')->get();

        return response([
            'status' => 200,
            'data_response' => $driver_set
        ]);
    }

    //create new user (driver) 
    public function createNewDriver($full_name, $contact_number, $driver_nic, $vehicle_type, $vehicle_no, $other_remarks, $prof_image, $other_docs, $Username, $password, $rand_char)
    {
        try {

            DriverReg::create([
                'driver_name' => $full_name,
                'contact_number' => $contact_number,
                'nic' => $driver_nic,
                'profile_image' => implode(',', $prof_image),
                'vehicle_type' => $vehicle_type,
                'vehicle_no' => $vehicle_no,
                'other_documents' => implode(',', $other_docs),
                'other_remarks' => $other_remarks,
                'username' => $Username,
                'password' => $password,
                'status' => 'Active'
            ]);

            $sms_response = $this->smsGateway->sendRegisrationDriverSms($contact_number, $Username, $rand_char);

            return $sms_response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
