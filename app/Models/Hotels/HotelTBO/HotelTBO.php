<?php

namespace App\Models\Hotels\HotelTBO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelTBO extends Model
{
    use HasFactory;

    public $Username;
    public $Password;

    public function __construct()
    {
        $this->Username = config('services.hoteltbo.username');
        $this->Password = config('services.hoteltbo.password');
    }

    function userAuth()
    {
        $Credentials = ['Username' => $this->Username, 'Password' => $this->Password];

        return $Credentials;
    }

    function getHeaders()
    {
        
    }
}
