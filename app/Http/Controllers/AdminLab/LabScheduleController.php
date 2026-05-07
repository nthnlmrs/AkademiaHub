<?php

namespace App\Http\Controllers\AdminLab;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;

class LabScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::whereHas('classRoom', function ($q) {
            $q->where('type', 'LAB');
        })->with(['classRoom.course', 'classRoom.lecturers', 'classRoom.teachingAssistants']);

        if ($request->filled('day')) {
            $query->where('day_of_week', $request->day);
        }

        $schedules = $query->orderBy('day_of_week')->orderBy('start_time')->paginate(20);
        $labClasses = ClassRoom::where('type', 'LAB')->with('course')->orderBy('course_id')->get();

        return view('admin_lab.schedules.index', compact('schedules', 'labClasses'));
    }

    public function create()
    {
        $labClasses = ClassRoom::where('type', 'LAB')->with('course')->orderBy('course_id')->get();
        return view('admin_lab.schedules.create', compact('labClasses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_room_id' => ['required', 'exists:class_rooms,id'],
            'day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:50'],
        ]);

        // Verify this is a LAB class
        $classRoom = ClassRoom::findOrFail($validated['class_room_id']);
        if ($classRoom->type !== 'LAB') {
            return redirect()->back()->withErrors(['class_room_id' => 'Only LAB classes can be managed here.'])->withInput();
        }

        // Check schedule conflicts for all enrolled students in this class
        $conflicts = $this->checkScheduleConflicts(
            $classRoom,
            $validated['day_of_week'],
            $validated['start_time'],
            $validated['end_time'],
            null // no schedule to exclude
        );

        if ($conflicts->isNotEmpty()) {
            $conflictMessages = $conflicts->map(function ($c) {
                return $c['user']->name . ' (' . ($c['user']->ta_id ?? $c['user']->nim_nip) . ') has conflict with "' .
                    $c['schedule']->classRoom->course->name . ' Class ' . $c['schedule']->classRoom->name .
                    '" at ' . $c['schedule']->start_time . '-' . $c['schedule']->end_time;
            });

            return redirect()->back()
                ->withErrors(['conflicts' => 'Schedule conflicts detected:'])
                ->with('conflict_details', $conflictMessages->toArray())
                ->withInput();
        }

        Schedule::create($validated);

        return redirect()->route('admin_lab.schedules.index')
            ->with('success', 'Lab schedule created successfully.');
    }

    public function edit(Schedule $schedule)
    {
        $labClasses = ClassRoom::where('type', 'LAB')->with('course')->orderBy('course_id')->get();
        return view('admin_lab.schedules.edit', compact('schedule', 'labClasses'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'class_room_id' => ['required', 'exists:class_rooms,id'],
            'day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:50'],
        ]);

        $classRoom = ClassRoom::findOrFail($validated['class_room_id']);
        if ($classRoom->type !== 'LAB') {
            return redirect()->back()->withErrors(['class_room_id' => 'Only LAB classes can be managed here.'])->withInput();
        }

        // Check conflicts excluding the current schedule
        $conflicts = $this->checkScheduleConflicts(
            $classRoom,
            $validated['day_of_week'],
            $validated['start_time'],
            $validated['end_time'],
            $schedule->id
        );

        if ($conflicts->isNotEmpty()) {
            $conflictMessages = $conflicts->map(function ($c) {
                return $c['user']->name . ' (' . ($c['user']->ta_id ?? $c['user']->nim_nip) . ') has conflict with "' .
                    $c['schedule']->classRoom->course->name . ' Class ' . $c['schedule']->classRoom->name .
                    '" at ' . $c['schedule']->start_time . '-' . $c['schedule']->end_time;
            });

            return redirect()->back()
                ->withErrors(['conflicts' => 'Schedule conflicts detected:'])
                ->with('conflict_details', $conflictMessages->toArray())
                ->withInput();
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

    /**
     * Check if any enrolled user in the class has a schedule conflict.
     * A conflict occurs when a user already has a class scheduled on the same day
     * with overlapping time range.
     */
    private function checkScheduleConflicts(ClassRoom $classRoom, int $dayOfWeek, string $startTime, string $endTime, ?int $excludeScheduleId = null): \Illuminate\Support\Collection
    {
        $enrolledUsers = $classRoom->users;
        $conflicts = collect();

        foreach ($enrolledUsers as $user) {
            // Get all class_room_ids this user is enrolled in (excluding the current class)
            $otherClassRoomIds = $user->classRooms()
                ->where('class_rooms.id', '!=', $classRoom->id)
                ->pluck('class_rooms.id');

            // Find overlapping schedules on the same day
            $overlapping = Schedule::whereIn('class_room_id', $otherClassRoomIds)
                ->where('day_of_week', $dayOfWeek)
                ->where(function ($q) use ($startTime, $endTime) {
                    // Overlap: new_start < existing_end AND new_end > existing_start
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                })
                ->when($excludeScheduleId, function ($q) use ($excludeScheduleId) {
                    $q->where('id', '!=', $excludeScheduleId);
                })
                ->with(['classRoom.course'])
                ->get();

            foreach ($overlapping as $s) {
                $conflicts->push([
                    'user' => $user,
                    'schedule' => $s,
                ]);
            }
        }

        return $conflicts;
    }
}
