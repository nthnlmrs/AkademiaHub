<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Services\ScheduleConflictService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function __construct(private ScheduleConflictService $conflicts) {}

    public function index(Request $request)
    {
        $query = Schedule::with(['classRoom.course', 'classRoom.lecturers']);

        if ($request->filled('day')) {
            $query->where('day_of_week', $request->day);
        }

        $schedules  = $query->orderBy('day_of_week')->orderBy('start_time')->paginate(20);
        $classRooms = $this->getClassRoomsForForm();

        return view('admin.schedules.index', compact('schedules', 'classRooms'));
    }

    public function create()
    {
        $classRooms = $this->getClassRoomsForForm();
        return view('admin.schedules.create', compact('classRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->scheduleValidationRules());
        $classRoom = ClassRoom::findOrFail($validated['class_room_id']);

        if ($error = $this->detectConflict($validated, $classRoom, null)) {
            return $error;
        }

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule created successfully.');
    }

    public function edit(Schedule $schedule)
    {
        $classRooms = $this->getClassRoomsForForm();
        return view('admin.schedules.edit', compact('schedule', 'classRooms'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate($this->scheduleValidationRules());
        $classRoom = ClassRoom::findOrFail($validated['class_room_id']);

        if ($error = $this->detectConflict($validated, $classRoom, $schedule->id)) {
            return $error;
        }

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule deleted successfully.');
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

    private function getClassRoomsForForm()
    {
        return ClassRoom::with('course')->orderBy('course_id')->get();
    }

    /**
     * Run room + lecturer conflict checks; return a redirect response on conflict, or null on success.
     */
    private function detectConflict(array $validated, ClassRoom $classRoom, ?int $excludeId)
    {
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

        $conflictingLecturer = $this->conflicts->conflictingLecturerName(
            $classRoom, $validated['day_of_week'],
            $validated['start_time'], $validated['end_time'], $excludeId
        );

        if ($conflictingLecturer) {
            return redirect()->back()
                ->withErrors(['time' => 'Lecturer ' . $conflictingLecturer . ' already has another class at this time.'])
                ->withInput();
        }

        return null;
    }
}
