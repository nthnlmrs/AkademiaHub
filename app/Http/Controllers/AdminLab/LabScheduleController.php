<?php

namespace App\Http\Controllers\AdminLab;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Services\ScheduleConflictService;
use Illuminate\Http\Request;

class LabScheduleController extends Controller
{
    public function __construct(private ScheduleConflictService $conflicts) {}

    public function index(Request $request)
    {
        $query = Schedule::whereHas('classRoom', fn($q) => $q->where('type', 'LAB'))
            ->with(['classRoom.course', 'classRoom.lecturers', 'classRoom.teachingAssistants']);

        if ($request->filled('day')) {
            $query->where('day_of_week', $request->day);
        }

        $schedules  = $query->orderBy('day_of_week')->orderBy('start_time')->paginate(20);
        $labClasses = $this->getLabClassesForForm();

        return view('admin_lab.schedules.index', compact('schedules', 'labClasses'));
    }

    public function create()
    {
        $labClasses = $this->getLabClassesForForm();
        return view('admin_lab.schedules.create', compact('labClasses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->scheduleValidationRules());
        $classRoom = ClassRoom::findOrFail($validated['class_room_id']);

        if ($classRoom->type !== 'LAB') {
            return redirect()->back()
                ->withErrors(['class_room_id' => 'Only LAB classes can be managed here.'])
                ->withInput();
        }

        if ($error = $this->detectConflict($validated, $classRoom, null)) {
            return $error;
        }

        Schedule::create($validated);

        return redirect()->route('admin_lab.schedules.index')
            ->with('success', 'Lab schedule created successfully.');
    }

    public function edit(Schedule $schedule)
    {
        $labClasses = $this->getLabClassesForForm();
        return view('admin_lab.schedules.edit', compact('schedule', 'labClasses'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate($this->scheduleValidationRules());
        $classRoom = ClassRoom::findOrFail($validated['class_room_id']);

        if ($classRoom->type !== 'LAB') {
            return redirect()->back()
                ->withErrors(['class_room_id' => 'Only LAB classes can be managed here.'])
                ->withInput();
        }

        if ($error = $this->detectConflict($validated, $classRoom, $schedule->id)) {
            return $error;
        }

        $schedule->update($validated);

        return redirect()->route('admin_lab.schedules.index')
            ->with('success', 'Lab schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin_lab.schedules.index')
            ->with('success', 'Lab schedule deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function scheduleValidationRules(): array
    {
        return [
            'class_room_id' => ['required', 'exists:class_rooms,id'],
            'day_of_week'   => ['required', 'integer', 'min:0', 'max:6'],
            'start_time'    => ['required', 'date_format:H:i'],
            'end_time'      => ['required', 'date_format:H:i', 'after:start_time'],
            'room'          => ['nullable', 'string', 'max:50'],
        ];
    }

    private function getLabClassesForForm()
    {
        return ClassRoom::where('type', 'LAB')->with('course')->orderBy('course_id')->get();
    }

    /**
     * Run room, lecturer, and per-student conflict checks.
     * Returns a redirect response on the first conflict found, or null on success.
     */
    private function detectConflict(array $validated, ClassRoom $classRoom, ?int $excludeId)
    {
        // Room conflict
        if (!empty($validated['room'])) {
            if ($this->conflicts->hasRoomConflict(
                $validated['room'], $validated['day_of_week'],
                $validated['start_time'], $validated['end_time'], $excludeId
            )) {
                return redirect()->back()
                    ->withErrors(['room' => 'The room is already booked for the given time.'])
                    ->withInput();
            }
        }

        // Lecturer conflict
        $conflictingLecturer = $this->conflicts->conflictingLecturerName(
            $classRoom, $validated['day_of_week'],
            $validated['start_time'], $validated['end_time'], $excludeId
        );

        if ($conflictingLecturer) {
            return redirect()->back()
                ->withErrors(['time' => 'Lecturer ' . $conflictingLecturer . ' already has another class at this time.'])
                ->withInput();
        }

        // Per-student conflicts (Lab-specific)
        $studentConflicts = $this->conflicts->studentConflicts(
            $classRoom, $validated['day_of_week'],
            $validated['start_time'], $validated['end_time'], $excludeId
        );

        if ($studentConflicts->isNotEmpty()) {
            return redirect()->back()
                ->withErrors(['conflicts' => 'Schedule conflicts detected:'])
                ->with('conflict_details', $this->conflicts->formatStudentConflicts($studentConflicts))
                ->withInput();
        }

        return null;
    }
}
