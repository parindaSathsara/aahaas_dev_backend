<?php

namespace App\Models\Customer\ProductReview;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class ProductReview extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'product_ratings';

    protected $fillable = [
        'product_id',
        'category',
        'customer_id',
        'rating',
        'comment',
        'images'
    ];

    public $timestamps = false;

    //function to create product review
    public function createNewProductReviewCustomer($prod_id, $category, $cus_id, $rating, $comment, $images)
    {
        try {

            $new_review = ProductReview::create([
                'product_id' => $prod_id,
                'category' => $category,
                'customer_id' => $cus_id,
                'rating' => $rating,
                'comment' => $comment,
                'images' => $images
            ]);

            return response([
                'status' => 200,
                'data_response' => $new_review
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //get all product wise reviews
    public function getReviewByProduct($id, $cat_id)
    {
        try {

            $prod_reviews = DB::table('product_ratings')->where(['product_id' => $id, 'category' => $cat_id])
                ->join('tbl_customer', 'product_ratings.customer_id', '=', 'tbl_customer.customer_id')
                ->select('*', 'product_ratings.created_at AS ReviewCreatedAt')
                ->get();


            $review_count = DB::table('product_ratings')->where(['product_id' => $id, 'category' => $cat_id])->count();

            $rates = DB::table('product_ratings')->where(['product_id' => $id, 'category' => $cat_id])->select('rating')->get();

            $count = array();

            foreach ($rates as $rate) {
                $count[] = $rate->rating;
            }

            $array_count = array_count_values($count);

            // return $prod_reviews;

            if (count($array_count) === 0) {

                return response([
                    'status' => 404,
                    'data_response' => '0'
                ]);
            } else {

                $rating = ((1 * $array_count['1']) + (2 * $array_count['2']) + (3 * $array_count['3']) + (4 * $array_count['4']) + (5 * $array_count['5'])) / ($array_count['1'] + $array_count['2'] + $array_count['3'] + $array_count['4'] + $array_count['5']);

                $final_rating = number_format((float)$rating, 1, '.', '');

                return response([
                    'status' => 200,
                    'data_response' => $prod_reviews,
                    'count' => $review_count,
                    'rating' => $final_rating
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
