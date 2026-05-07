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

        // Find the most recent session that has modules or activities, or default to the first one
        $activeSession = null;
        if ($sessions->isNotEmpty()) {
            $activeSession = $sessions->last(function ($session) {
                return $session->modules()->exists() || $session->activities()->exists() || $session->quizzes()->exists();
            }) ?? $sessions->first();

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
