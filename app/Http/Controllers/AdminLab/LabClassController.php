<?php

namespace App\Http\Controllers\AdminLab;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\User;
use App\Rules\UserHasRole;
use App\Rules\UserHasStudentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LabClassController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassRoom::where('type', 'LAB')->with(['course', 'lecturers', 'students', 'teachingAssistants']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $labClasses = $query->orderBy('course_id')->orderBy('name')->paginate(15);
        $courses = Course::orderBy('name')->get();

        return view('admin_lab.classes.index', compact('labClasses', 'courses'));
    }

    public function create()
    {
        $courses = Course::orderBy('name')->get();
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        $teachingAssistants = User::where('role', 'student')
            ->where('student_type', 'teaching_assistant')
            ->orderBy('name')->get();
        $students = User::where('role', 'student')->where('student_type', 'regular')->orderBy('name')->get();

        return view('admin_lab.classes.create', compact('courses', 'lecturers', 'teachingAssistants', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:10'],
            'mode' => ['required', Rule::in(['onsite', 'online'])],
            'room' => ['nullable', 'string', 'max:50'],
            'lecturers' => ['nullable', 'array'],
            'lecturers.*' => ['exists:users,id', new UserHasRole('lecturer')],
            'teaching_assistants' => ['nullable', 'array'],
            'teaching_assistants.*' => ['exists:users,id', new UserHasStudentType('teaching_assistant')],
            'students' => ['nullable', 'array'],
            'students.*' => ['exists:users,id', new UserHasStudentType('regular')],
        ]);

        $classRoom = ClassRoom::create([
            'course_id' => $validated['course_id'],
            'name' => $validated['name'],
            'type' => 'LAB', // Always LAB for admin lab
            'mode' => $validated['mode'],
            'room' => $validated['room'] ?? null,
        ]);

        // Combine lecturers, TAs, and students
        $members = array_merge(
            $validated['lecturers'] ?? [],
            $validated['teaching_assistants'] ?? [],
            $validated['students'] ?? []
        );
        $classRoom->users()->sync($members);

        return redirect()->route('admin_lab.classes.index')
            ->with('success', 'LAB class created successfully.');
    }

    public function edit(ClassRoom $classroom)
    {
        if ($classroom->type !== 'LAB') {
            abort(403, 'Only LAB classes can be managed here.');
        }

        $classroom->load(['course', 'users']);
        $courses = Course::orderBy('name')->get();
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        $teachingAssistants = User::where('role', 'student')
            ->where('student_type', 'teaching_assistant')
            ->orderBy('name')->get();
        $students = User::where('role', 'student')->where('student_type', 'regular')->orderBy('name')->get();

        $selectedLecturers = $classroom->lecturers->pluck('id')->toArray();
        $selectedTAs = $classroom->teachingAssistants->pluck('id')->toArray();
        $selectedStudents = $classroom->students()->where('student_type', 'regular')->pluck('users.id')->toArray();

        return view('admin_lab.classes.edit', compact(
            'classroom', 'courses', 'lecturers', 'teachingAssistants', 'students',
            'selectedLecturers', 'selectedTAs', 'selectedStudents'
        ));
    }

    public function update(Request $request, ClassRoom $classroom)
    {
        if ($classroom->type !== 'LAB') {
            abort(403);
        }

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:10'],
            'mode' => ['required', Rule::in(['onsite', 'online'])],
            'room' => ['nullable', 'string', 'max:50'],
            'lecturers' => ['nullable', 'array'],
            'lecturers.*' => ['exists:users,id', new UserHasRole('lecturer')],
            'teaching_assistants' => ['nullable', 'array'],
            'teaching_assistants.*' => ['exists:users,id', new UserHasStudentType('teaching_assistant')],
            'students' => ['nullable', 'array'],
            'students.*' => ['exists:users,id', new UserHasStudentType('regular')],
        ]);

        $classroom->update([
            'course_id' => $validated['course_id'],
            'name' => $validated['name'],
            'mode' => $validated['mode'],
            'room' => $validated['room'] ?? null,
        ]);

        $membersToSync = [];
        if ($request->has('lecturers')) {
            $membersToSync = array_merge($membersToSync, $validated['lecturers'] ?? []);
        } else {
            $membersToSync = array_merge($membersToSync, $classroom->lecturers()->pluck('users.id')->toArray());
        }

        if ($request->has('teaching_assistants')) {
            $membersToSync = array_merge($membersToSync, $validated['teaching_assistants'] ?? []);
        } else {
            $membersToSync = array_merge($membersToSync, $classroom->teachingAssistants()->pluck('users.id')->toArray());
        }

        if ($request->has('students')) {
            $membersToSync = array_merge($membersToSync, $validated['students'] ?? []);
        } else {
            $regularStudents = $classroom->students()->where('student_type', 'regular')->pluck('users.id')->toArray();
            $membersToSync = array_merge($membersToSync, $regularStudents);
        }

        $classroom->users()->sync($membersToSync);

        return redirect()->route('admin_lab.classes.index')
            ->with('success', 'LAB class updated successfully.');
    }

    public function destroy(ClassRoom $classroom)
    {
        if ($classroom->type !== 'LAB') {
            abort(403);
        }

        $classroom->delete();

        return redirect()->route('admin_lab.classes.index')
            ->with('success', 'LAB class deleted successfully.');
    }
}
