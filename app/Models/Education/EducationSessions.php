<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class EducationSessions extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'edu_tbl_sessions';

    protected $fillable = [
        'session_id',
        'education_id',
        'inventory_id',
        'day',
        'sesssion',
        'start_date' ,
        'end_date' ,
        'start_time' ,
        'end_time' ,
        'video_link',
        'session_type'
    ];

    public $timestamps = false;
}
