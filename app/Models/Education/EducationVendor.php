<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EducationVendor extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'edu_tbl_vendor';

    protected $fillable = [
        'vendor_name',
        'vendor_email',
        'vendor_tel_no',
        'vendor_website',
        'vendor_reservation_process',
        'vendor_type',
        'no_of_experiance',
        'edu_qualifications',
        'additional_details',
        'user_id',
        'mark_up',
        'created_at',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;
}
