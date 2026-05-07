<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        if ($user->isAdminLab()) {
            return $this->adminLabDashboard();
        }

        return $this->userDashboard($user);
    }

    private function adminLabDashboard()
    {
        $totalTAs = \App\Models\User::where('role', 'student')->where('student_type', 'teaching_assistant')->count();
        $totalLabClasses = ClassRoom::where('type', 'LAB')->count();
        $totalLabSchedules = Schedule::whereHas('classRoom', fn($q) => $q->where('type', 'LAB'))->count();
        $totalStudents = \App\Models\User::where('role', 'student')->count();

        return view('dashboard.admin_lab', compact('totalTAs', 'totalLabClasses', 'totalLabSchedules', 'totalStudents'));
    }

    private function adminDashboard()
    {
        $totalCourses = Course::count();
        $totalClasses = ClassRoom::count();
        $totalLecturers = \App\Models\User::where('role', 'lecturer')->count();
        $totalStudents = \App\Models\User::where('role', 'student')->count();

        return view('dashboard.admin', compact('totalCourses', 'totalClasses', 'totalLecturers', 'totalStudents'));
    }

    private function userDashboard($user)
    {
        $classRooms = $user->classRooms()->with(['course', 'schedules', 'lecturers'])->get();
        $today = now()->dayOfWeek;

        $todaySchedules = Schedule::whereIn('class_room_id', $classRooms->pluck('id'))
            ->where('day_of_week', $today)
            ->with(['classRoom.course', 'classRoom.lecturers'])
            ->orderBy('start_time')
            ->get();

        $upcomingSchedules = Schedule::whereIn('class_room_id', $classRooms->pluck('id'))
            ->where('day_of_week', '>', $today)
            ->with(['classRoom.course'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $recentNotifications = $user->notifications()->latest()->take(3)->get();
        
        $gpa = $user->grades()->avg('score'); // Simple average for now
        $totalCredits = $classRooms->count() * 3; // Assuming 3 credits per class as fallback

        return view('dashboard.user', compact(
            'classRooms', 
            'todaySchedules', 
            'upcomingSchedules', 
            'recentNotifications',
            'gpa',
            'totalCredits'
        ));
    }
}
