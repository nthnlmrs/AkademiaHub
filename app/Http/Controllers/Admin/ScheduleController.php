<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['classRoom.course', 'classRoom.lecturers']);

        if ($request->filled('day')) {
            $query->where('day_of_week', $request->day);
        }

        $schedules = $query->orderBy('day_of_week')->orderBy('start_time')->paginate(20);
        $classRooms = ClassRoom::with('course')->orderBy('course_id')->get();

        return view('admin.schedules.index', compact('schedules', 'classRooms'));
    }

    public function create()
    {
        $classRooms = ClassRoom::with('course')->orderBy('course_id')->get();
        return view('admin.schedules.create', compact('classRooms'));
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

        $classRoom = ClassRoom::findOrFail($validated['class_room_id']);

        // Check Room Conflict
        if (!empty($validated['room'])) {
            $roomConflict = Schedule::where('room', $validated['room'])
                ->where('day_of_week', $validated['day_of_week'])
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time'])
                ->exists();

            if ($roomConflict) {
                return redirect()->back()->withErrors(['room' => 'The room is already booked for the given time.'])->withInput();
            }
        }

        // Check Lecturer Conflict
        foreach ($classRoom->lecturers as $lecturer) {
            $lecturerConflict = Schedule::whereHas('classRoom.users', function($q) use ($lecturer) {
                    $q->where('users.id', $lecturer->id);
                })
                ->where('day_of_week', $validated['day_of_week'])
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time'])
                ->exists();

            if ($lecturerConflict) {
                return redirect()->back()->withErrors(['time' => 'Lecturer ' . $lecturer->name . ' already has another class at this time.'])->withInput();
            }
        }

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule created successfully.');
    }

    public function edit(Schedule $schedule)
    {
        $classRooms = ClassRoom::with('course')->orderBy('course_id')->get();
        return view('admin.schedules.edit', compact('schedule', 'classRooms'));
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

        // Check Room Conflict
        if (!empty($validated['room'])) {
            $roomConflict = Schedule::where('room', $validated['room'])
                ->where('id', '!=', $schedule->id)
                ->where('day_of_week', $validated['day_of_week'])
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time'])
                ->exists();

            if ($roomConflict) {
                return redirect()->back()->withErrors(['room' => 'The room is already booked for the given time.'])->withInput();
            }
        }

        // Check Lecturer Conflict
        foreach ($classRoom->lecturers as $lecturer) {
            $lecturerConflict = Schedule::whereHas('classRoom.users', function($q) use ($lecturer) {
                    $q->where('users.id', $lecturer->id);
                })
                ->where('id', '!=', $schedule->id)
                ->where('day_of_week', $validated['day_of_week'])
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time'])
                ->exists();

            if ($lecturerConflict) {
                return redirect()->back()->withErrors(['time' => 'Lecturer ' . $lecturer->name . ' already has another class at this time.'])->withInput();
            }
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
}
