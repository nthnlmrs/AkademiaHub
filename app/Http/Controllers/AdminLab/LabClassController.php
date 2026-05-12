<?php

namespace App\Http\Controllers\AdminLab;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\SyncsClassroomMembers;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LabClassController extends Controller
{
    use SyncsClassroomMembers;

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
        $courses    = Course::orderBy('name')->get();

        return view('admin_lab.classes.index', compact('labClasses', 'courses'));
    }

    public function create()
    {
        [$courses, $lecturers, $teachingAssistants, $students] = $this->getFormDependencies();

        return view('admin_lab.classes.create', compact('courses', 'lecturers', 'teachingAssistants', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge(
            $this->labClassBaseRules(),
            $this->classroomMemberRules()
        ));

        $classRoom = ClassRoom::create([
            'course_id' => $validated['course_id'],
            'name'      => $validated['name'],
            'type'      => 'LAB', // Always LAB for admin lab
            'mode'      => $validated['mode'],
            'room'      => $validated['room'] ?? null,
        ]);

        $classRoom->users()->sync($this->buildMembersFromValidated($validated));

        return redirect()->route('admin_lab.classes.index')
            ->with('success', 'LAB class created successfully.');
    }

    public function edit(ClassRoom $classroom)
    {
        $this->abortUnlessLabClass($classroom);

        $classroom->load(['course', 'users']);
        [$courses, $lecturers, $teachingAssistants, $students] = $this->getFormDependencies();

        $selectedLecturers = $classroom->lecturers->pluck('id')->toArray();
        $selectedTAs       = $classroom->teachingAssistants->pluck('id')->toArray();
        $selectedStudents  = $classroom->students()->where('student_type', 'regular')->pluck('users.id')->toArray();

        return view('admin_lab.classes.edit', compact(
            'classroom', 'courses', 'lecturers', 'teachingAssistants', 'students',
            'selectedLecturers', 'selectedTAs', 'selectedStudents'
        ));
    }

    public function update(Request $request, ClassRoom $classroom)
    {
        $this->abortUnlessLabClass($classroom);

        $validated = $request->validate(array_merge(
            $this->labClassBaseRules(),
            $this->classroomMemberRules()
        ));

        $classroom->update([
            'course_id' => $validated['course_id'],
            'name'      => $validated['name'],
            'mode'      => $validated['mode'],
            'room'      => $validated['room'] ?? null,
        ]);

        $classroom->users()->sync($this->buildMembersForUpdate($request, $validated, $classroom));

        return redirect()->route('admin_lab.classes.index')
            ->with('success', 'LAB class updated successfully.');
    }

    public function destroy(ClassRoom $classroom)
    {
        $this->abortUnlessLabClass($classroom);

        $classroom->delete();

        return redirect()->route('admin_lab.classes.index')
            ->with('success', 'LAB class deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /** Abort if the classroom is not of type LAB. */
    private function abortUnlessLabClass(ClassRoom $classroom, string $message = 'Only LAB classes can be managed here.'): void
    {
        if ($classroom->type !== 'LAB') {
            abort(403, $message);
        }
    }

    /** Base validation rules for LAB classroom fields (no 'type' – it's always LAB). */
    private function labClassBaseRules(): array
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
