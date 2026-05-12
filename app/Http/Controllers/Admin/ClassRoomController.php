<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\SyncsClassroomMembers;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassRoomController extends Controller
{
    use SyncsClassroomMembers;

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
        [$courses, $lecturers, $teachingAssistants, $students] = $this->getFormDependencies();

        return view('admin.classrooms.create', compact('courses', 'lecturers', 'teachingAssistants', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge(
            $this->classroomBaseRules(),
            ['type' => ['required', Rule::in(['LEC', 'LAB'])]],
            $this->classroomMemberRules()
        ));

        $classRoom = ClassRoom::create([
            'course_id' => $validated['course_id'],
            'name'      => $validated['name'],
            'type'      => $validated['type'],
            'mode'      => $validated['mode'],
            'room'      => $validated['room'] ?? null,
        ]);

        $classRoom->users()->sync($this->buildMembersFromValidated($validated));

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Class created successfully.');
    }

    public function edit(ClassRoom $classroom)
    {
        $classroom->load(['course', 'lecturers', 'students']);
        $courses  = Course::orderBy('name')->get();
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        $students  = User::where('role', 'student')->orderBy('name')->get();

        $selectedLecturers = $classroom->lecturers->pluck('id')->toArray();
        $selectedStudents  = $classroom->students->pluck('id')->toArray();

        return view('admin.classrooms.edit', compact('classroom', 'courses', 'lecturers', 'students', 'selectedLecturers', 'selectedStudents'));
    }

    public function update(Request $request, ClassRoom $classroom)
    {
        $validated = $request->validate(array_merge(
            $this->classroomBaseRules(),
            ['type' => ['required', Rule::in(['LEC', 'LAB'])]],
            $this->classroomMemberRules()
        ));

        $classroom->update([
            'course_id' => $validated['course_id'],
            'name'      => $validated['name'],
            'type'      => $validated['type'],
            'mode'      => $validated['mode'],
            'room'      => $validated['room'] ?? null,
        ]);

        $classroom->users()->sync($this->buildMembersForUpdate($request, $validated, $classroom));

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(ClassRoom $classroom)
    {
        $classroom->delete();

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Class deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /** Common validation rules for the classroom fields (minus 'type'). */
    private function classroomBaseRules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'name'      => ['required', 'string', 'max:10'],
            'mode'      => ['required', Rule::in(['onsite', 'online'])],
            'room'      => ['nullable', 'string', 'max:50'],
        ];
    }

    /** Fetch all dropdown data needed by the create/edit forms. */
    private function getFormDependencies(): array
    {
        return [
            Course::orderBy('name')->get(),
            User::where('role', 'lecturer')->orderBy('name')->get(),
            User::where('role', 'student')->where('student_type', 'teaching_assistant')->orderBy('name')->get(),
            User::where('role', 'student')->where('student_type', 'regular')->orderBy('name')->get(),
        ];
    }
}
