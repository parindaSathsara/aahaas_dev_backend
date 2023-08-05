<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EducationCategory extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'edu_tbl_category';

    protected $fillable = [
        'id',
        'edu_type',
        'category_one',
        'category_two',
        'category_three',
        'category_four',
        'additional_data1',
        'additional_data2',
        'additional_data3',
        'additional_data4',
        'additional_data5',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
