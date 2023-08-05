<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EducationServiceLocation extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'edu_tbl_servicelocation';

    protected $fillable = [
        'id',
        'edu_vendor_id',
        'location_type1',
        'location_type2',
        'longtitude',
        'latitude',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
