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

        $with = [
            'classRoom.course.courseSessions',
            'classRoom.lecturers',
            'classRoom.teachingAssistants',
        ];

        if ($user->isAdmin()) {
            $schedules = Schedule::where('day_of_week', $selectedDay)
                ->with($with)
                ->orderBy('start_time')
                ->get();
        } else {
            $classRoomIds = $user->classRooms->pluck('id');
            $schedules = Schedule::whereIn('class_room_id', $classRoomIds)
                ->where('day_of_week', $selectedDay)
                ->with($with)
                ->orderBy('start_time')
                ->get();
        }

        // Determine the "current" session for each schedule based on current semester week.
        // Session number = current week of year, capped to available sessions.
        $currentWeek = (int) now()->weekOfYear;

        $schedules->each(function ($schedule) use ($currentWeek) {
            $sessions = $schedule->classRoom->course->courseSessions ?? collect();
            if ($sessions->isEmpty()) {
                $schedule->currentSession = null;
                return;
            }
            // Offset by classroom ID for variety across classes in the same day
            $sessionIndex = ($currentWeek - 1 + $schedule->classRoom->id) % $sessions->count();
            $schedule->currentSession = $sessions->get($sessionIndex) ?? $sessions->first();
        });

        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('schedule.index', compact('schedules', 'selectedDay', 'days'));
    }
}
