<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EducationResources extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'edu_tbl_lesson_resources';

    protected $fillable = [
        'resource_id',
        'resource_name',
        'resource_description',
        'resource_link',
        'resource_type',
        'education_date'
    ];

    public $timestamps = false;
}
