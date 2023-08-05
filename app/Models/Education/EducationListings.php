<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class EducationListings extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'edu_tbl_education';

    protected $fillable = [
        'education_id',
        'course_name',
        'course_description',
        'education_type',
        'medium',
        'course_mode',
        'couse_type',
        'group_type',
        'sessions',
        'free_session',
        'payment_method',
        'status',
        'image_path',
        'intro_video_id',
        'user_active',
        'vendor_id',

        'created_at',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;
}
