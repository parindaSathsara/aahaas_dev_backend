<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class EducationBookings extends Model
{
    use HasFactory, HasFactory, Notifiable;

    protected $table = 'edu_tbl_booking';

    protected $fillable = [
        'education_id',
        'booking_id',
        'session_id',
        'discount_id',
        'preffered_booking_date',
        'booking_date', 
        'totalPrice',
        'discount_amount',
        'student_name',
        'student_age',
        'status',
        'student_type',
        'user_id',
        'rate_id'
    ];

    public $timestamps = false;
}
