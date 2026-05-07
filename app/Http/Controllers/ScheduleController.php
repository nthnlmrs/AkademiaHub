<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedDay = $request->get('day', now()->dayOfWeek);

        if ($user->isAdmin()) {
            $schedules = Schedule::where('day_of_week', $selectedDay)
                ->with(['classRoom.course', 'classRoom.lecturers', 'classRoom.teachingAssistants'])
                ->orderBy('start_time')
                ->get();
        } else {
            $classRoomIds = $user->classRooms->pluck('id');
            $schedules = Schedule::whereIn('class_room_id', $classRoomIds)
                ->where('day_of_week', $selectedDay)
                ->with(['classRoom.course', 'classRoom.lecturers', 'classRoom.teachingAssistants'])
                ->orderBy('start_time')
                ->get();
        }

        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('schedule.index', compact('schedules', 'selectedDay', 'days'));
    }
}
