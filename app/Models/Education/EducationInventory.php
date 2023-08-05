<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EducationInventory extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'edu_tbl_inventory';

    protected $fillable = [
        'id',
        'edu_id',
        'service_location_typeid',
        'course_inv_startdate',
        'course_inv_enddate',
        'course_day',
        'session_no',
        'common_session',
        'course_startime',
        'course_endtime',
        'max_adult_occupancy',
        'max_child_occupancy',
        'max_total_occupancy',
        'total_inventory',
        'used_inventory',
        'blackout_date',
        'blackout_day',
        'inclusions',
        'exclusions',

        'created_at',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;
}
