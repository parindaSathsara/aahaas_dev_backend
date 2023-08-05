<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Brands extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_brands';

    protected $fillable = [
        'brand_id',
        'brandName',
        'brandDescription',
        'category1',
        'category2',
        'category3',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;
}
