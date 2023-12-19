<?php

namespace App\Http\Controllers\Customer\ProductReview;

use App\Http\Controllers\Controller;
use App\Models\Customer\ProductReview\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{

    public $product_review;

    public function __construct()
    {
        $this->product_review = new ProductReview();
    }


    //create new customer product review
    public function createNewProductReview(Request $request)
    {
        try {

            $prod_id = $request['prod_id'];
            $category = $request['category'];
            $cus_id = $request['cus_id'];
            $rating = $request['rating'];
            $comment = $request['comment'];
            $images = 'null';

            $reponse = $this->product_review->createNewProductReviewCustomer($prod_id, $category, $cus_id, $rating, $comment, $images);

            return $reponse;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function deleteReview($id)
    {
        $review = ProductReview::where('id', $id)->delete();

        return response([
            'status' => 200,
            'response' => $review
        ]);
    }


    //fetch all procust wise reviews
    public function fetchProducWiseReviews($id, $cat_id)
    {
        try {

            $reponse = $this->product_review->getReviewByProduct($id, $cat_id);

            return $reponse;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
