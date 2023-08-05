<?php

namespace App\Models\Lifestyle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class LifeStyle extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_lifestyle';

    protected $fillable = [
        'lifestyle_city',
        'lifestyle_attraction_type',
        'lifestyle_name',
        'lifestyle_description',
        'latitude',
        'longitude',
        'address',
        'micro_location',
        'tripadvisor',
        'preferred',
        'selling_points',
        'pref_start_date',
        'pref_end_date',
        'vendor_id',
        'provider',
        'provider_id',
        'active_status',
        'image',
        'category1',
        'category2',
        'category3',
        'category4',
        'created_at',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;


    //create new life style

    public function createNewLifeStyleData($city, $attraction, $lifestyle_name, $description, $latitude, $longitude, $address, $micro_location, $tripadvisor, $start_date, $end_date, $images, $cat1, $cat2, $cat3)
    {
        try {

            LifeStyle::create([
                'lifestyle_city' => $city,
                'lifestyle_attraction_type' => $attraction,
                'lifestyle_name' => $lifestyle_name,
                'lifestyle_description' => $description,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $address,
                'micro_location' => $micro_location,
                'tripadvisor' => $tripadvisor,
                'preferred',
                'selling_points',
                'pref_start_date' => $start_date,
                'pref_end_date' => $end_date,
                'vendor_id',
                'provider',
                'provider_id',
                'active_status' => '1',
                'image' => implode(',', $images),
                'category1' => '3',
                'category2' => $cat1,
                'category3' => $cat2,
                'category4' => $cat3
            ]);

            return response([
                'status' => 200,
                'data_response' => 'Completed'
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }



    public function fetchAllLifestylesInventoriesByID($id)
    {
        $lifeStylesInventory = DB::table('tbl_lifestyle_inventory')
            ->where('tbl_lifestyle_inventory.lifestyle_id', $id)
            ->get();

        return response([
            'status' => 200,
            'data_response' => $lifeStylesInventory
        ]);
    }


    //get categories
    public function getMainCategories()
    {
        $category1 = DB::table('tbl_submaincategory')->get();
        $category2 = DB::table('tbl_submaincategorysub')->get();
        $category3 = DB::table('tbl_submaincategorysubsub')->get();

        return response([
            'status' => 200,
            'category1' => $category1,
            'category2' => $category2,
            'category3' => $category3
        ]);
    }

    //get all life styles
    public function fetchAllLifeStyle()
    {
        try {

            $Query = DB::table('tbl_lifestyle')->get();

            return response([
                'status' => 200,
                'data_response' => $Query
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
