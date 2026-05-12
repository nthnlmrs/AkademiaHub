<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate($this->courseRules());

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
        $request->validate(
            $this->sessionRules($course),
            $this->sessionMessages($request->session_number)
        );

        $course->courseSessions()->create($request->only(['session_number', 'title', 'description']));

        return redirect()->back()->with('success', 'Session added successfully.');
    }

    public function updateSession(Request $request, Course $course, CourseSession $session)
    {
        $request->validate(
            $this->sessionRules($course, $session->id),
            $this->sessionMessages($request->session_number)
        );

        $session->update($request->only(['session_number', 'title', 'description']));

        return redirect()->route('admin.courses.show', $course)->with('success', 'Session updated successfully.');
    }

    public function destroySession(Course $course, CourseSession $session)
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
        $validated = $request->validate($this->courseRules($course->id));

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

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Validation rules for Course store/update.
     * Pass $ignoreId to ignore the current course on unique checks (update).
     */
    private function courseRules(?int $ignoreId = null): array
    {
        return [
            'code'        => ['required', 'string', 'max:20', Rule::unique('courses', 'code')->ignore($ignoreId)],
            'name'        => ['required', 'string', 'max:255'],
            'credits'     => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Validation rules for CourseSession store/update.
     * Pass $ignoreId to ignore an existing session on unique checks (update).
     */
    private function sessionRules(Course $course, ?int $ignoreId = null): array
    {
        return [
            'session_number' => [
                'required',
                'integer',
                Rule::unique('course_sessions')
                    ->where(fn($q) => $q->where('course_id', $course->id))
                    ->ignore($ignoreId),
            ],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    /** Custom validation messages for session_number uniqueness. */
    private function sessionMessages(mixed $sessionNumber): array
    {
        return [
            'session_number.unique' => 'Session number ' . $sessionNumber . ' already exists in this course.',
        ];
    }
}
