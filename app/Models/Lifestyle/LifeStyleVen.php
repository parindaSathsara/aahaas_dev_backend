<?php

namespace App\Models\Lifestyle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class LifeStyleVen extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_lifestyle_vendor';

    protected $fillable = [
        'seller_name',
        'lifestyle_type',
        'type_name',
        'official_address',
        'official_email1',
        'additional_email',
        'contact_number1',
        'additional_contact',
        'key_contact_name1',
        'key_contact_email1',
        'key_contact_number1',
        'key_contact_name2',
        'key_contact_email2',
        'key_contact_number2',
        'created_at',
        'user_id'
    ];

    public $timestamps = false;
}
