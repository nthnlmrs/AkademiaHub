<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\CourseSession;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Show form to create a quiz for a specific session.
     */
    public function create(ClassRoom $classroom, CourseSession $session)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isLecturer() && !$user->isTeachingAssistant()) {
            abort(403, 'Unauthorized.');
        }

        return view('class.quiz.create', compact('classroom', 'session'));
    }

    /**
     * Store a newly created quiz and its questions.
     */
    public function store(Request $request, ClassRoom $classroom, CourseSession $session)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isLecturer() && !$user->isTeachingAssistant()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.type' => 'required|in:mcq,essay',
            'questions.*.question' => 'required|string',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'required_with:questions.*.options|string',
            'questions.*.correct_answer' => 'nullable|string|required_if:questions.*.type,mcq',
        ]);

        // Validate total points do not exceed 100
        $totalPoints = collect($validated['questions'])->sum('points');
        if ($totalPoints > 100) {
            return back()->withErrors(['questions' => 'Total points cannot exceed 100. Current total: ' . $totalPoints])->withInput();
        }

        DB::beginTransaction();
        try {
            $quiz = Quiz::create([
                'course_session_id' => $session->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
            ]);

            foreach ($validated['questions'] as $qData) {
                QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'type' => $qData['type'],
                    'question' => $qData['question'],
                    'points' => $qData['points'],
                    'options' => $qData['type'] === 'mcq' ? ($qData['options'] ?? null) : null,
                    'correct_answer' => $qData['type'] === 'mcq' ? $qData['correct_answer'] : null,
                ]);
            }

            DB::commit();

            return redirect()->route('class.session', ['classroom' => $classroom->id, 'session' => $session->id])
                ->with('success', 'Quiz created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create quiz: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show quiz to student or lecturer.
     */
    public function show(ClassRoom $classroom, Quiz $quiz)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$classroom->users->contains($user->id)) {
            abort(403, 'Unauthorized.');
        }

        $quiz->load('questions');

        // Check if student has already attempted
        $attempt = $quiz->attempts()->where('user_id', $user->id)->first();

        return view('class.quiz.show', compact('classroom', 'quiz', 'attempt'));
    }

    /**
     * Handle student quiz submission.
     */
    public function submit(Request $request, ClassRoom $classroom, Quiz $quiz)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$classroom->users->contains($user->id)) {
            abort(403, 'Unauthorized.');
        }

        // Ensure user hasn't already submitted
        if ($quiz->attempts()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'You have already submitted this quiz.');
        }

        $quiz->load('questions');

        DB::beginTransaction();
        try {
            $hasEssay = false;
            $totalScore = 0;

            $attempt = $quiz->attempts()->create([
                'user_id' => $user->id,
                'status' => 'pending_review',
                'total_score' => 0,
            ]);

            foreach ($quiz->questions as $question) {
                $userAnswer = $request->input('answers.' . $question->id);
                $pointsAwarded = 0;

                if ($question->type === 'mcq') {
                    if ((string)$userAnswer === (string)$question->correct_answer) {
                        $pointsAwarded = $question->points;
                        $totalScore += $pointsAwarded;
                    }
                } else {
                    $hasEssay = true;
                    $pointsAwarded = null; // Needs manual review
                }

                $attempt->answers()->create([
                    'quiz_question_id' => $question->id,
                    'answer' => $userAnswer,
                    'points_awarded' => $pointsAwarded,
                ]);
            }

            $attempt->update([
                'total_score' => $totalScore,
                'status' => $hasEssay ? 'pending_review' : 'graded',
            ]);

            DB::commit();

            return redirect()->route('quiz.show', ['classroom' => $classroom->id, 'quiz' => $quiz->id])
                ->with('success', 'Quiz submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to submit quiz: ' . $e->getMessage()]);
        }
    }

    /**
     * Show submissions to lecturer.
     */
    public function submissions(ClassRoom $classroom, Quiz $quiz)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isLecturer() && !$user->isTeachingAssistant()) {
            abort(403, 'Unauthorized.');
        }

        $attempts = $quiz->attempts()->with(['user', 'answers.question'])->get();

        return view('class.quiz.submissions', compact('classroom', 'quiz', 'attempts'));
    }

    /**
     * Grade essay answers manually.
     */
    public function gradeAttempt(Request $request, ClassRoom $classroom, Quiz $quiz, \App\Models\QuizAttempt $attempt)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isLecturer() && !$user->isTeachingAssistant()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'grades' => 'required|array',
            'grades.*' => 'numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->grades as $answerId => $points) {
                $answer = $attempt->answers()->where('id', $answerId)->first();
                if ($answer && $answer->question->type === 'essay') {
                    // Ensure points don't exceed max points
                    $points = min($points, $answer->question->points);
                    $answer->update(['points_awarded' => $points]);
                }
            }

            // Recalculate total score
            $totalScore = $attempt->answers()->sum('points_awarded');

            $attempt->update([
                'total_score' => $totalScore,
                'status' => 'graded',
            ]);

            DB::commit();

            return back()->with('success', 'Grades saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to save grades: ' . $e->getMessage()]);
        }
    }
}