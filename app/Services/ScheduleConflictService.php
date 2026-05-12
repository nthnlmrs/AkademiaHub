<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\Schedule;
use Illuminate\Support\Collection;

/**
 * Encapsulates schedule conflict-checking logic that was duplicated
 * between Admin\ScheduleController and AdminLab\LabScheduleController.
 */
class ScheduleConflictService
{
    /**
     * Check whether the given room is already booked at the specified time.
     * Pass $excludeScheduleId to ignore the current schedule (for updates).
     */
    public function hasRoomConflict(
        string $room,
        int    $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int   $excludeScheduleId = null
    ): bool {
        return Schedule::where('room', $room)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->when($excludeScheduleId, fn($q) => $q->where('id', '!=', $excludeScheduleId))
            ->exists();
    }

    /**
     * Check whether any lecturer assigned to the classroom has a conflicting schedule.
     * Returns the name of the first conflicting lecturer, or null if no conflict.
     */
    public function conflictingLecturerName(
        ClassRoom $classRoom,
        int       $dayOfWeek,
        string    $startTime,
        string    $endTime,
        ?int      $excludeScheduleId = null
    ): ?string {
        foreach ($classRoom->lecturers as $lecturer) {
            $conflict = Schedule::whereHas('classRoom.users', function ($q) use ($lecturer) {
                    $q->where('users.id', $lecturer->id);
                })
                ->where('day_of_week', $dayOfWeek)
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->when($excludeScheduleId, fn($q) => $q->where('id', '!=', $excludeScheduleId))
                ->exists();

            if ($conflict) {
                return $lecturer->name;
            }
        }

        return null;
    }

    /**
     * Check for per-student schedule conflicts for all enrolled users.
     * Used by the Lab schedule controller to surface per-student conflicts.
     *
     * Returns a Collection of ['user' => User, 'schedule' => Schedule] pairs.
     */
    public function studentConflicts(
        ClassRoom $classRoom,
        int       $dayOfWeek,
        string    $startTime,
        string    $endTime,
        ?int      $excludeScheduleId = null
    ): Collection {
        $conflicts = collect();

        foreach ($classRoom->users as $user) {
            $otherClassRoomIds = $user->classRooms()
                ->where('class_rooms.id', '!=', $classRoom->id)
                ->pluck('class_rooms.id');

            $overlapping = Schedule::whereIn('class_room_id', $otherClassRoomIds)
                ->where('day_of_week', $dayOfWeek)
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                })
                ->when($excludeScheduleId, fn($q) => $q->where('id', '!=', $excludeScheduleId))
                ->with(['classRoom.course'])
                ->get();

            foreach ($overlapping as $schedule) {
                $conflicts->push(['user' => $user, 'schedule' => $schedule]);
            }
        }

        return $conflicts;
    }

    /**
     * Format a collection of student conflicts into human-readable strings.
     *
     * @param  Collection  $conflicts
     * @return array<string>
     */
    public function formatStudentConflicts(Collection $conflicts): array
    {
        return $conflicts->map(function ($c) {
            return $c['user']->name . ' (' . ($c['user']->ta_id ?? $c['user']->nim_nip) . ') has conflict with "' .
                $c['schedule']->classRoom->course->name . ' Class ' . $c['schedule']->classRoom->name .
                '" at ' . $c['schedule']->start_time . '-' . $c['schedule']->end_time;
        })->toArray();
    }
}
