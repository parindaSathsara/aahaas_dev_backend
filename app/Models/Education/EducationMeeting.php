<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EducationMeeting extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'edu_tbl_meetings';

    protected $fillable = [
        'uuid',
        'meeting_id',
        'host_id',
        'host_email',
        'session_topic',
        'type',
        'status',
        'start_time',
        'end_time',
        'duration',
        'timezone',
        'link_created_at',
        'start_url',
        'join_url',
        'password',
        'auto_recording',
        'allow_multiple_devices',
        'session_id',
        'created_at',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;
}
