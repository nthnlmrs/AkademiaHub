<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesClassroom;
use App\Models\ClassRoom;
use App\Models\CourseSession;
use Illuminate\Support\Facades\Auth;

class ClassDetailController extends Controller
{
    use AuthorizesClassroom;

    public function show(ClassRoom $classroom)
    {
        $this->authorizeClassroomAccess($classroom);

        $this->loadClassroomBase($classroom);
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
        $this->authorizeClassroomAccess($classroom);

        $this->loadClassroomBase($classroom);
        $sessions      = $classroom->course->courseSessions;
        $activeSession = $session;
        $activeSession->load(['activities', 'modules', 'quizzes.questions']);

        return view('class.show', compact('classroom', 'sessions', 'activeSession'));
    }

    public function people(ClassRoom $classroom)
    {
        $this->authorizeClassroomAccess($classroom);

        $classroom->load(['course.rubrics', 'lecturers', 'teachingAssistants', 'students']);

        return view('class.people', compact('classroom'));
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /** Load the base classroom relations shared by show() and session(). */
    private function loadClassroomBase(ClassRoom $classroom): void
    {
        $classroom->load([
            'course.courseSessions' => fn($q) => $q->orderBy('session_number'),
            'lecturers',
            'teachingAssistants',
        ]);
    }
}
