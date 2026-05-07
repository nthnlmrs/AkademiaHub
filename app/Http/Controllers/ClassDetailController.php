<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\CourseSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassDetailController extends Controller
{
    public function show(ClassRoom $classroom)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$classroom->users->contains($user->id)) {
            abort(403, 'You are not enrolled in this class.');
        }

        $classroom->load([
            'course.courseSessions' => fn($q) => $q->orderBy('session_number'),
            'lecturers',
            'teachingAssistants',
        ]);

        $sessions = $classroom->course->courseSessions;
        $activeSession = $sessions->first(); // Default to first session

        if ($activeSession) {
            $activeSession->load(['activities', 'modules', 'quizzes.questions']);
        }

        return view('class.show', compact('classroom', 'sessions', 'activeSession'));
    }

    public function session(ClassRoom $classroom, CourseSession $session)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$classroom->users->contains($user->id)) {
            abort(403, 'You are not enrolled in this class.');
        }

        $classroom->load([
            'course.courseSessions' => fn($q) => $q->orderBy('session_number'),
            'lecturers',
            'teachingAssistants',
        ]);

        $sessions = $classroom->course->courseSessions;
        $activeSession = $session;
        $activeSession->load(['activities', 'modules', 'quizzes.questions']);

        return view('class.show', compact('classroom', 'sessions', 'activeSession'));
    }

    public function people(ClassRoom $classroom)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$classroom->users->contains($user->id)) {
            abort(403, 'You are not enrolled in this class.');
        }

        $classroom->load(['course.rubrics', 'lecturers', 'teachingAssistants', 'students']);

        return view('class.people', compact('classroom'));
    }
}
