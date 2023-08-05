<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ClassRequest extends Model
{
     use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_education_classreq';

    protected $fillable = [
        'edu_category',
        'edu_description',
        'user_id',
    ];

    public $timestamps = false;
}
