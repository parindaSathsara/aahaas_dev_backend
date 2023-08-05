<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EducationRates extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'edu_tbl_rate';

    protected $fillable = [
        'id',
        'edu_id',
        'edu_detail_id',
        'service_location_typeid',
        'edu_inventory_id',
        'pricing_type',
        'sale_start',
        'sale_end',
        'currency',
        'min_num_students',
        'max_num_students',
        'adult_course_fee',
        'child_course_fee',
        'senior_citizen_course_fee',
        'military_course_fee',
        'total_cost_course',
        'deadline_no_ofdays',
        'course_admission_deadline',
        'course_refund_policy',
        'course_mark_up',
        'mark_up_type',
        'cerated_at',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;
}
