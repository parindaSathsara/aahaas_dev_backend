<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use App\Models\Education\ClassRequest;
use Illuminate\Http\Request;

class ClassRequestController extends Controller
{
    public function createNewClassRequest(Request $request)
    {
        try {

            $Title = $request['catetitle'];
            $Description = $request['description'];
            $UserId = $request['userid'];

            ClassRequest::create([
                'edu_category' => 'No Category',
                'edu_description' => $Description,
                'user_id' => $UserId,
            ]);

            return response([
                'status' => 200
            ]);
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
}
