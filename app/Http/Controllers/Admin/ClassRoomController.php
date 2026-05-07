<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\User;
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
        $students = User::where('role', 'student')->orderBy('name')->get();

        return view('admin.classrooms.create', compact('courses', 'lecturers', 'students'));
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
            'lecturers.*' => ['exists:users,id'],
            'students' => ['nullable', 'array'],
            'students.*' => ['exists:users,id'],
        ]);

        $classRoom = ClassRoom::create([
            'course_id' => $validated['course_id'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'mode' => $validated['mode'],
            'room' => $validated['room'] ?? null,
        ]);

        // Attach lecturers and students
        $members = array_merge($validated['lecturers'] ?? [], $validated['students'] ?? []);
        $classRoom->users()->sync($members);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Class created successfully.');
    }

    public function edit(ClassRoom $classroom)
    {
        $classroom->load(['course', 'users']);
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
            'lecturers.*' => ['exists:users,id'],
            'students' => ['nullable', 'array'],
            'students.*' => ['exists:users,id'],
        ]);

        $classroom->update([
            'course_id' => $validated['course_id'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'mode' => $validated['mode'],
            'room' => $validated['room'] ?? null,
        ]);

        $members = array_merge($validated['lecturers'] ?? [], $validated['students'] ?? []);
        $classroom->users()->sync($members);

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
