<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\User;
use App\Rules\UserHasRole;
use App\Rules\UserHasStudentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassRoomController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassRoom::with(['course', 'lecturers', 'students']);

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

        $classRooms = $query->orderBy('course_id')->orderBy('name')->paginate(15);
        $courses = Course::orderBy('name')->get();

        return view('admin.classrooms.index', compact('classRooms', 'courses'));
    }

    public function create()
    {
        $courses = Course::orderBy('name')->get();
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        $teachingAssistants = User::where('role', 'student')
            ->where('student_type', 'teaching_assistant')
            ->orderBy('name')->get();
        $students = User::where('role', 'student')->where('student_type', 'regular')->orderBy('name')->get();

        return view('admin.classrooms.create', compact('courses', 'lecturers', 'teachingAssistants', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:10'],
            'type' => ['required', Rule::in(['LEC', 'LAB'])],
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
            'type' => $validated['type'],
            'mode' => $validated['mode'],
            'room' => $validated['room'] ?? null,
        ]);

        // Attach lecturers, tas, and students
        $members = array_merge(
            $validated['lecturers'] ?? [],
            $validated['teaching_assistants'] ?? [],
            $validated['students'] ?? []
        );
        $classRoom->users()->sync($members);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Class created successfully.');
    }

    public function edit(ClassRoom $classroom)
    {
        $classroom->load(['course', 'lecturers', 'students']);
        $courses = Course::orderBy('name')->get();
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        $students = User::where('role', 'student')->orderBy('name')->get();

        $selectedLecturers = $classroom->lecturers->pluck('id')->toArray();
        $selectedStudents = $classroom->students->pluck('id')->toArray();

        return view('admin.classrooms.edit', compact('classroom', 'courses', 'lecturers', 'students', 'selectedLecturers', 'selectedStudents'));
    }

    public function update(Request $request, ClassRoom $classroom)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:10'],
            'type' => ['required', Rule::in(['LEC', 'LAB'])],
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
            'type' => $validated['type'],
            'mode' => $validated['mode'],
            'room' => $validated['room'] ?? null,
        ]);

        // When a request might not contain fields for certain roles, we shouldn't wipe them entirely if the form didn't support them.
        // But since we added TA to the validation, if we pass all 3 fields, we can safely sync them.
        // To be safe, if a field is not present in the request at all (not just empty, but missing), we keep existing ones for that role.
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
            // get only regular students
            $regularStudents = $classroom->students()->where('student_type', 'regular')->pluck('users.id')->toArray();
            $membersToSync = array_merge($membersToSync, $regularStudents);
        }

        $classroom->users()->sync($membersToSync);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(ClassRoom $classroom)
    {
        $classroom->delete();

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Class deleted successfully.');
    }
}
