<?php

namespace App\Models\Education;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class EducationDetails extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'edu_tbl_details';

    protected $fillable = [
        'id',
        'edu_id',
        'curriculum',
        'no_of_hours_session',
        'duration',
        'max_age_of_course',
        'min_age_of_course',
        'opening_time',
        'closing_time',
        'closed_days',
        'closed_dates',
        'location_type_id',
        
        'created_at',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;
}
