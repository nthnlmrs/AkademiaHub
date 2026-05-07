<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index(ClassRoom $classroom)
    {
        if (!Auth::user()->can('view', $classroom)) {
            abort(403, 'Unauthorized');
        }
        $user = Auth::user();

        $classroom->load(['course.rubrics', 'students', 'lecturers']);
        $rubrics = $classroom->course->rubrics;

        // For students, only show their own grades
        if ($user->isStudent() && !$user->isTeachingAssistant()) {
            $grades = Grade::where('class_room_id', $classroom->id)
                ->where('user_id', $user->id)
                ->with(['gradeRubric', 'classRoom'])
                ->get();
            $students = collect([$user]);
        } else {
            $studentIds = $classroom->students()->pluck('users.id');
            
            $grades = Grade::where('class_room_id', $classroom->id)
                ->whereIn('user_id', $studentIds)
                ->with(['user', 'gradeRubric', 'classRoom'])
                ->get();
            $students = $classroom->students;
        }

        return view('class.gradebook', compact('classroom', 'grades', 'students', 'rubrics'));
    }

    public function storeRubric(Request $request, ClassRoom $classroom)
    {
        if (!Auth::user()->can('manage', $classroom)) {
            abort(403, 'Only lecturers of this class can manage rubrics.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $classroom->course->rubrics()->create($validated);

        return redirect()->back()->with('success', 'Rubric added successfully.');
    }

    public function store(Request $request, ClassRoom $classroom)
    {
        if (!Auth::user()->can('assist', $classroom)) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'grade_rubric_id' => ['required', 'exists:grade_rubrics,id'],
            'type' => ['required', 'string'], // Theory, Lab, etc.
            'component' => ['required', 'string', 'max:100'],
            'score' => ['required', 'numeric', 'min:0'],
            'max_score' => ['required', 'numeric', 'min:0'],
        ]);

        if (!$classroom->students()->where('users.id', $validated['user_id'])->exists()) {
            abort(403, 'The specified user is not a student in this classroom.');
        }

        $rubric = \App\Models\GradeRubric::find($validated['grade_rubric_id']);
        if ($rubric->course_id !== $classroom->course_id) {
            abort(403, 'The specified rubric does not belong to this course.');
        }

        Grade::updateOrCreate(
            [
                'class_room_id' => $classroom->id,
                'user_id' => $validated['user_id'],
                'grade_rubric_id' => $validated['grade_rubric_id'],
                'component' => $validated['component'],
            ],
            [
                'type' => $validated['type'],
                'score' => $validated['score'],
                'max_score' => $validated['max_score'],
            ]
        );

        return redirect()->back()->with('success', 'Grade saved successfully.');
    }

    public function destroy(Grade $grade)
    {
        if (!Auth::user()->can('manage', $grade->classRoom)) {
            abort(403, 'Unauthorized to delete this grade.');
        }

        $grade->delete();

        return redirect()->back()->with('success', 'Grade deleted.');
    }

    public function syncQuizzes(Request $request, ClassRoom $classroom)
    {
        if (!Auth::user()->can('manage', $classroom)) {
            abort(403, 'Only lecturers of this class can sync quizzes.');
        }

        $validated = $request->validate([
            'grade_rubric_id' => ['required', 'exists:grade_rubrics,id'],
            'quiz_ids' => ['required', 'array', 'min:1'],
            'quiz_ids.*' => ['exists:quizzes,id'],
            'component_name' => ['required', 'string', 'max:100'],
        ]);

        $classroom->load('students');

        // Fetch selected quizzes with their graded attempts
        $quizzes = \App\Models\Quiz::whereIn('id', $validated['quiz_ids'])
            ->with(['attempts' => function($q) {
                $q->where('status', 'graded');
            }])
            ->get();

        foreach ($classroom->students as $student) {
            $totalPointsEarned = 0;
            $totalMaxPoints = 0;

            foreach ($quizzes as $quiz) {
                $attempt = $quiz->attempts->where('user_id', $student->id)->first();
                if ($attempt) {
                    $totalPointsEarned += $attempt->total_score;
                }
                $totalMaxPoints += $quiz->total_points;
            }

            if ($totalMaxPoints > 0) {
                // Calculate percentage (scale to 100 max score)
                $percentageScore = ($totalPointsEarned / $totalMaxPoints) * 100;

                Grade::updateOrCreate(
                    [
                        'class_room_id' => $classroom->id,
                        'user_id' => $student->id,
                        'grade_rubric_id' => $validated['grade_rubric_id'],
                        'component' => $validated['component_name'],
                    ],
                    [
                        'type' => 'Quiz Sync',
                        'score' => round($percentageScore, 2),
                        'max_score' => 100,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Quizzes synced to gradebook successfully.');
    }
}
