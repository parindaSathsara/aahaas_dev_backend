<?php

namespace App\Http\Controllers\ZoomMeeting;

use App\Http\Controllers\Controller;
use App\Models\Education\EducationMeeting;
use App\Models\Education\EducationResources;
use Illuminate\Http\Request;
use App\Traits\ZoomMeetingTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ZoomMeetingController extends Controller
{
    use ZoomMeetingTrait;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    public function list(Request $request)
    {
        $path = 'users/me/meetings';
        $response = $this->zoomGet($path);

        $data = json_decode($response->body(), true);
        $data['meetings'] = array_map(function (&$m) {
            $m['start_at'] = $this->toUnixTimeStamp($m['start_time'], $m['timezone']);
            return $m;
        }, $data['meetings']);

        return [
            'success' => $response->ok(),
            'data' => $data,
        ];
    }


    public function create(Request $request)
    {
        $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();



        $validator = Validator::make($request->all(), [
            'topic' => 'required|string',
            'start_time' => 'required|date',
            'agenda' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'data' => $validator->errors(),
            ];
        }



        $data = $validator->validated();

        $path = 'users/me/meetings';
        $response = $this->zoomPost($path, [
            'topic' => $data['topic'],
            'type' => self::MEETING_TYPE_SCHEDULE,
            'start_time' => $this->toZoomTimeFormat($data['start_time']),
            'duration' => 30,
            'agenda' => '',
            'settings' => [
                // 'alternative_hosts'=>'wasundara.yushani@gmail.com',
                'join_before_host' => true,
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => false,
                'auto_recording' => 'local',
                'allow_multiple_devices' => false
            ]
        ]);

        $response_final = json_decode($response->body(), true);

        // return $response_final;

        $uuid = $response_final['uuid'];
        $meeting_id = $response_final['id'];
        $host_id = $response_final['host_id'];
        $host_email = $response_final['host_email'];
        $meeting_topic = $response_final['topic'];
        $meeting_type = $response_final['type'];
        $start_time = $response_final['start_time'];
        $duration = $response_final['duration'];
        $timezone = $response_final['timezone'];
        $link_created_at = $response_final['created_at'];
        $start_url = $response_final['start_url'];
        $join_url = $response_final['join_url'];
        $password = $response_final['password'];
        $auto_recording = $response_final['settings']['auto_recording'];
        $allow_multiple_devices = $response_final['settings']['allow_multiple_devices'];
        $created_at = $currentTime;
        $updated_at = $currentTime;
        $updated_by = 'user@gmail.com';

        EducationMeeting::create([
            'uuid' => $uuid,
            'meeting_id' => $meeting_id,
            'host_id' => $host_id,
            'host_email' => $host_email,
            'session_topic' => $meeting_topic,
            'type' => $meeting_type,
            'status' => $start_time,
            'start_time' => $start_time,
            'end_time' => $start_time,
            'duration' => $duration,
            'timezone' => $timezone,
            'link_created_at' => $link_created_at,
            'start_url' => $start_url,
            'join_url' => $join_url,
            'password' => $password,
            'auto_recording' => $auto_recording,
            'allow_multiple_devices' => $allow_multiple_devices,
            'session_id' => '12',
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'updated_by' => $updated_by,
        ]);


        EducationResources::create([
            'education_id' => $request->input('education_id'),
            'resource_name' => $request->input('topic'),
            'resource_description' => $request->input('fileDescription'),
            'resource_link' => $meeting_id,
            'resource_type' => "LiveClasses",
            'education_date' => $data['start_time'],
            'education_vendor' => '1'
        ]);

        return [
            'success' => $response->status() === 201,
            'data' => $response_final,
        ];
    }


    public function get(Request $request, string $id)
    {
        $path = 'meetings/' . $id;
        $response = $this->zoomGet($path);

        $data = json_decode($response->body(), true);
        if ($response->ok()) {
            $data['start_at'] = $this->toUnixTimeStamp($data['start_time'], $data['timezone']);
        }

        return [
            'success' => $response->ok(),
            'data' => $data,
        ];
    }


    public function getMeetingByID($educationID,$meetingID)
    {
        try {
            $educationListings = DB::table('edu_tbl_sessions')
                ->join('edu_tbl_education', 'edu_tbl_sessions.education_id', 'edu_tbl_education.education_id')
                ->join('edu_tbl_meetings', 'edu_tbl_sessions.video_link', 'edu_tbl_meetings.meeting_id')
                ->where('edu_tbl_meetings.meeting_id', $meetingID)
                ->where('edu_tbl_education.education_id', $educationID)
                ->select(
                    'edu_tbl_sessions.*',
                    'edu_tbl_education.*',
                    'edu_tbl_meetings.*',
                    'edu_tbl_sessions.start_time as sessionStart',
                    'edu_tbl_sessions.end_time as sessionEnd',
                )
                ->first();

            return response()->json([
                'status' => 200,
                'educationLesson' => $educationListings
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }


    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string',
            'start_time' => 'required|date',
            'agenda' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'data' => $validator->errors(),
            ];
        }
        $data = $validator->validated();

        $path = 'meetings/' . $id;
        $response = $this->zoomPatch($path, [
            'topic' => $data['topic'],
            'type' => self::MEETING_TYPE_SCHEDULE,
            'start_time' => (new \DateTime($data['start_time']))->format('Y-m-d\TH:i:s'),
            'duration' => 30,
            'agenda' => $data['agenda'],
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => true,
            ]
        ]);

        return [
            'success' => $response->status() === 204,
            'data' => json_decode($response->body(), true),
        ];
    }


    public function delete(Request $request, string $id)
    {
        $path = 'meetings/' . $id;
        $response = $this->zoomDelete($path);

        return [
            'success' => $response->status() === 204,
            'data' => json_decode($response->body(), true),
        ];
    }
}
