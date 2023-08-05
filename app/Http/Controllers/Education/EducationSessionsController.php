<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use App\Models\Education\EducationSessions;
use App\Models\Education\EducationVendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class EducationSessionsController extends Controller
{
    public function addNewEducationSession(Request $request)
    {
        try {

            $currentTime = \Carbon\Carbon::now('Asia/Kolkata')->toDateTimeString();
            $daysArr = $request->input('day');
            $sessionCount = (int)$request->input('session_no');

            $sessionData = $request->input('sessions');
            $sessionType = $request->input('sessionType');


            $days = array();



            for ($i = 1; $i <= count($daysArr); $i++) {

                for ($x = 1; $x <= $sessionCount; $x++) {
                    EducationSessions::create([
                        'education_id' => $request->input('education_id'),
                        'inventory_id' => $request->input('inventory_id'),
                        'day' => $daysArr[$i - 1],
                        'sesssion' => "Session" . $x,
                        'start_date' => $sessionData[$i . "day_" . $x . "_course_start_date"],
                        'end_date' => $sessionData[$i . "day_" . $x . "_course_end_date"],
                        'start_time' => $sessionData[$i . "day_" . $x . "_course_start_time"],
                        'end_time' => $sessionData[$i . "day_" . $x . "_course_end_time"],
                        'video_link' => $sessionData[$i . "day_" . $x . "_course_link"],

                        'session_type' => $sessionType,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                        'updated_by' => 'User 1',
                    ]);
                }
            }


            return response()->json([
                'status' => 200,
                'message' => 'Education Inventory Created'
            ]);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 400,
                'error_message' => throw $exception

            ]);
        }
    }


    public function eduction_lesson_link_update(Request $request)
    {
        $updatedData = DB::table('edu_tbl_sessions')
            ->where('session_id', $request->input('sessionID'))
            ->update(['video_link' => $request->input('videoID')]);

        return response()->json([
            'status' => 200,
            'educationSessions' => $updatedData,
        ]);
    }


    public function getTimeSlotsByDate(Request $request)
    {
        $eduicationID = $request->input('education_id');

        $selectedDate = $request->input('date');
        $selectedDayName = Carbon::parse($selectedDate)->dayName;

        $educationSessions = EducationSessions::where('education_id', $eduicationID)
            ->where('start_date', $selectedDate)
            ->get();
        $daySchedule = [];
        foreach ($educationSessions as $val) {
            if ($val['day'] == "Monday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::MONDAY);
            } else if ($val['day'] == "Tuesday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::TUESDAY);
            } else if ($val['day'] == "Wednesday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::WEDNESDAY);
            } else if ($val['day'] == "Thursday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::THURSDAY);
            } else if ($val['day'] == "Friday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::FRIDAY);
            } else if ($val['day'] == "Saturday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::SATURDAY);
            } else {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::SUNDAY);
            }
            $endDate = Carbon::parse($val['end_date']);
            for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {

                // $mondays=[
                //     'dates'=>$date->format('Y-m-d'),
                //     'day'=>Carbon::parse($date->format('Y-m-d'))->dayName
                // ];
                $daySchedule[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => Carbon::parse($date->format('Y-m-d'))->dayName,
                    'session' => $val['sesssion'],
                    'start_time' => $val['start_time'],
                    'end_time' => $val['end_time'],
                    'education_id' => $val['education_id'],
                    'start_date' => $val['start_date'],
                    'end_date' => $val['end_date'],
                ];
            }
        }

        return response()->json([
            'status' => 200,
            'education_sessions' => $educationSessions,
            'classSchedule' => $daySchedule
        ]);
    }




    public function getTimeSlotsBySession(Request $request)
    {


        $session_id = $request->input('session_id');

        $educationSessions = EducationSessions::where('session_id', $session_id)
            // ->where('education_id', $eduicationID)
            ->get();

        $daySchedule = [];
        foreach ($educationSessions as $val) {
            if ($val['day'] == "Monday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::MONDAY);
            } else if ($val['day'] == "Tuesday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::TUESDAY);
            } else if ($val['day'] == "Wednesday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::WEDNESDAY);
            } else if ($val['day'] == "Thursday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::THURSDAY);
            } else if ($val['day'] == "Friday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::FRIDAY);
            } else if ($val['day'] == "Saturday") {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::SATURDAY);
            } else {
                $startDate = Carbon::parse($val['start_date'])->subDays(1)->next(Carbon::SUNDAY);
            }

            $endDate = Carbon::parse($val['end_date']);

            for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {

                // $mondays=[
                //     'dates'=>$date->format('Y-m-d'),
                //     'day'=>Carbon::parse($date->format('Y-m-d'))->dayName
                // ];

                $daySchedule[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => Carbon::parse($date->format('Y-m-d'))->dayName,
                    'session' => $val['sesssion'],
                    'start_time' => $val['start_time'],
                    'end_time' => $val['end_time'],
                    'education_id' => $val['education_id'],
                    'start_date' => $val['start_date'],
                    'end_date' => $val['end_date'],
                ];
            }
        }



        return response()->json([
            'status' => 200,
            // 'education_sessions' => $educationSessions,
            'classSchedule' => $daySchedule
        ]);
    }






    public function getEducationSessionByLessonID(Request $request)
    {
        $educationID = (explode(',', $request->input('education_id')))[0];
        $inventoryID = $request->input('inventory_id');
        $startDateRange = $request->input('start_date');
        $endDataRange = $request->input('end_date');
        try {
            $educationSessions = EducationSessions::where('education_id', $educationID)
                // ->where('inventory_id', $inventoryID)
                ->where('start_date', '>=', $startDateRange)
                ->where('end_date', '<=', $endDataRange)
                // ->where('end_date','<=', $startDateRange)
                ->get();


            // $days = [];

            $mondays = [];
            foreach ($educationSessions as $val) {
                if ($val['day'] == "Monday") {
                    $startDate = Carbon::parse($val['start_date'])->next(Carbon::MONDAY);
                } else if ($val['day'] == "Tuesday") {
                    $startDate = Carbon::parse($val['start_date'])->next(Carbon::TUESDAY);
                } else if ($val['day'] == "Wednesday") {
                    $startDate = Carbon::parse($val['start_date'])->next(Carbon::WEDNESDAY);
                } else if ($val['day'] == "Thursday") {
                    $startDate = Carbon::parse($val['start_date'])->next(Carbon::THURSDAY);
                } else if ($val['day'] == "Friday") {
                    $startDate = Carbon::parse($val['start_date'])->next(Carbon::FRIDAY);
                } else if ($val['day'] == "Saturday") {
                    $startDate = Carbon::parse($val['start_date'])->next(Carbon::SATURDAY);
                } else {
                    $startDate = Carbon::parse($val['start_date'])->next(Carbon::SUNDAY);
                }

                $endDate = Carbon::parse($val['end_date']);

                for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {

                    // $mondays=[
                    //     'dates'=>$date->format('Y-m-d'),
                    //     'day'=>Carbon::parse($date->format('Y-m-d'))->dayName
                    // ];

                    $mondays[] = [
                        'date' => $date->format('Y-m-d'),
                        'day' => Carbon::parse($date->format('Y-m-d'))->dayName,
                        'session' => $val['sesssion'],
                        'start_time' => $val['start_time'],
                        'end_time' => $val['end_time'],
                        'education_id' => $val['education_id'],
                        'start_date' => $val['start_date'],
                        'end_date' => $val['end_date'],
                        'session_id' => $val['session_id']
                    ];
                }
            }



            return response()->json([
                'status' => 200,
                'education_sessions' => $mondays
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 400,
                'error_message' => throw $exception
            ]);
        }
    }
}
