<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ExcelTest extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'excel_test';

    protected $fillable = [
        'test1',
        'test2',
        'test3',
        'test4',
        'test5',
        'test6'
    ];

    public $timestamps = false;
}
