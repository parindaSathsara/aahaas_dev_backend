<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use App\Models\Education\EducationResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vimeo\Laravel\Facades\Vimeo;
use Vimeo\Laravel\VimeoManager;

class VideoController extends Controller
{
    public function uploadEducationVideo(Request $request, VimeoManager $vimeo)
    {
        try {


            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $public = public_path($file);
                $uri = $vimeo->upload($file, array(
                    'name' => $request->input('fileName'),
                    'description' => $request->input('fileDescription'),
                ));

                EducationResources::create([
                    'education_id' => $request->input('education_id'),
                    'resource_name' => $request->input('fileName'),
                    'resource_description' => $request->input('fileDescription'),
                    'resource_link' => $uri,
                    'resource_type' => "PreRecorded",
                    'education_date' => $currentTime,
                    'education_vendor' => $request->input('educationVendor')
                ]);
            } else {
                return "No Any Data";
            }
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'message' => throw $exception
            ]);
        }
    }


    public function getEducationVideosByTeacherID($id)
    {
        $educationVideos = DB::table('edu_tbl_lesson_resources')
            ->select("*")
            ->where('education_vendor', $id)
            ->get();

        return response()->json([
            'status' => 200,
            'educationVideos' => $educationVideos,
        ]);
    }
}
