<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Seller extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_seller';

    protected $fillable = [
        'seller_code',
        'company_env',
        'seller_name',
        'seller_email',
        'company_name',
        'company_address',
        'br_number',
        'br_copyimage',
        'nic_no',
        'nic_image',
        'seller_contact',
        'company_contact',
        'seller_profilepic',
        'status',
        'latlon'
    ];

    public $timestamps = false;

    public function setFileNamesAttribute($value)
    {
        $this->attributes['filenames'] = json_encode($value);
    }

    //create seller business profile
    public function createVendorBusinessProfile($seller_code, $company_env, $seller_name, $seller_email, $company_name, $company_address, $br_number, $nic_no, $seller_contact, $company_contact, $status, $lat_lon, $br_Img_array, $nic_Img_array, $profile_Img_array)
    {
        try {

            $new_sellerProfile = Seller::create([
                'seller_code' => $seller_code,
                'company_env' => $company_env,
                'seller_name' => $seller_name,
                'seller_email' => $seller_email,
                'company_name' => $company_name,
                'company_address' => $company_address,
                'br_number' => $br_number,
                'br_copyimage' => implode(',', $br_Img_array),
                'nic_no' => $nic_no,
                'nic_image' => implode(',', $nic_Img_array),
                'seller_contact' => $seller_contact,
                'company_contact' => $company_contact,
                'seller_profilepic' => implode(',', $profile_Img_array),
                'status' => $status,
                'latlon' => $lat_lon
            ]);

            return response([
                'status' => 200,
                'data_response' => 'Business profile successfully created',
                'dataset' => $new_sellerProfile
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
