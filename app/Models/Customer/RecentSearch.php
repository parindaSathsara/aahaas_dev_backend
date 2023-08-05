<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class RecentSearch extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_recent_searches';

    protected $fillable = [
        'search_ref',
        'uid',
        'uip'
    ];

    public $timestamps = false;

    //retreive all the user search history by user
    public function fetchRecentSearchByUser($uid, $uip)
    {
        try {

            $RecentData = DB::table('user_recent_searches')->where('uid', $uid)->orWhere('uip', $uip)->groupBy('search_ref')->orderBy('created_at', 'DESC')->get();


            return response([
                'status' => 200,
                'data_response' => $RecentData
            ]);
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }

    //create user search by user
    public function createNewUserSearch($searchdata, $uid, $uip)
    {
        try {

            $CreateNew = RecentSearch::create([
                'search_ref' => $searchdata,
                'uid' => $uid,
                'uip' => $uip
            ]);

            return response([
                'status' => 200,
                'data_response' => $CreateNew
            ]);
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }

    //delete user search history by user
    public function deleteUserSearchByUser($uid, $uip)
    {
        try {

            $DeleteRow = DB::table('user_recent_searches')->where('uid', $uid)->orWhere('uip', $uip)->delete();

            return response($DeleteRow, 200);
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
}
