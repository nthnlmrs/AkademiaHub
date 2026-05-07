<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::withCount(['classRooms', 'courseSessions']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('name')->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:courses'],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);

        Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load('courseSessions');
        return view('admin.courses.show', compact('course'));
    }

    public function storeSession(Request $request, Course $course)
    {
        $request->validate([
            'session_number' => [
                'required', 
                'integer', 
                \Illuminate\Validation\Rule::unique('course_sessions')->where(fn ($q) => $q->where('course_id', $course->id))
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'session_number.unique' => 'Session number ' . $request->session_number . ' already exists in this course.'
        ]);

        $course->courseSessions()->create($request->only(['session_number', 'title', 'description']));

        return redirect()->back()->with('success', 'Session added successfully.');
    }

    public function updateSession(Request $request, Course $course, \App\Models\CourseSession $session)
    {
        $request->validate([
            'session_number' => [
                'required', 
                'integer', 
                \Illuminate\Validation\Rule::unique('course_sessions')
                    ->where(fn ($q) => $q->where('course_id', $course->id))
                    ->ignore($session->id)
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'session_number.unique' => 'Session number ' . $request->session_number . ' already exists in this course.'
        ]);

        $session->update($request->only(['session_number', 'title', 'description']));

        return redirect()->route('admin.courses.show', $course)->with('success', 'Session updated successfully.');
    }

    public function destroySession(Course $course, \App\Models\CourseSession $session)
    {
        $session->delete();
        return redirect()->back()->with('success', 'Session deleted successfully.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:courses,code,' . $course->id],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
