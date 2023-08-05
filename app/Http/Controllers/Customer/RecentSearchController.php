<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\RecentSearch;
use Illuminate\Http\Request;

class RecentSearchController extends Controller
{
    public $recentSearch;

    public function __construct()
    {
        $this->recentSearch = new RecentSearch();
    }

    //fetch user search history by user
    public function getUserHistoryByUser(Request $request)
    {
        try {
            $UserId = $request['uid'];
            $ClientIP = $request['uip'];

            $response = $this->recentSearch->fetchRecentSearchByUser($UserId, $ClientIP);
            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //create new search row by user
    public function createNewUserSearchRow(Request $request)
    {
        try {

            $SearchData = $request['search_text'];
            $UID = $request['uid'];
            $UIP = $request['uip'];

            $response = $this->recentSearch->createNewUserSearch($SearchData, $UID, $UIP);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function removeSearchHistoryByUser(Request $request)
    {
        try {
            $UserId = $request['uid'];
            $ClientIP = $request['uip'];

            $response = $this->recentSearch->deleteUserSearchByUser($UserId, $ClientIP);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
