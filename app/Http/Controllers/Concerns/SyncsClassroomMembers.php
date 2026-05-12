<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ClassRoom;
use Illuminate\Http\Request;

/**
 * Reusable classroom member sync logic.
 *
 * Eliminates repeated member-sync blocks in ClassRoomController (Admin)
 * and LabClassController (AdminLab).
 */
trait SyncsClassroomMembers
{
    /**
     * Build the full list of member IDs to sync from validated data.
     * Used on store – always takes all three groups from $validated.
     *
     * @param  array  $validated  The validated request data.
     * @return array<int>
     */
    protected function buildMembersFromValidated(array $validated): array
    {
        return array_merge(
            $validated['lecturers'] ?? [],
            $validated['teaching_assistants'] ?? [],
            $validated['students'] ?? []
        );
    }

    /**
     * Build the list of member IDs to sync on update.
     * Preserves existing IDs for any role-group that was NOT present in the request.
     *
     * @param  Request    $request
     * @param  array      $validated
     * @param  ClassRoom  $classroom
     * @return array<int>
     */
    protected function buildMembersForUpdate(Request $request, array $validated, ClassRoom $classroom): array
    {
        $membersToSync = [];

        if ($request->has('lecturers')) {
            $membersToSync = array_merge($membersToSync, $validated['lecturers'] ?? []);
        } else {
            $membersToSync = array_merge($membersToSync, $classroom->lecturers()->pluck('users.id')->toArray());
        }

        if ($request->has('teaching_assistants')) {
            $membersToSync = array_merge($membersToSync, $validated['teaching_assistants'] ?? []);
        } else {
            $membersToSync = array_merge($membersToSync, $classroom->teachingAssistants()->pluck('users.id')->toArray());
        }

        if ($request->has('students')) {
            $membersToSync = array_merge($membersToSync, $validated['students'] ?? []);
        } else {
            $regularStudents = $classroom->students()->where('student_type', 'regular')->pluck('users.id')->toArray();
            $membersToSync = array_merge($membersToSync, $regularStudents);
        }

        return $membersToSync;
    }

    /**
     * Return the shared validation rules for classroom member fields.
     *
     * @return array<string, array<mixed>>
     */
    protected function classroomMemberRules(): array
    {
        return [
            'lecturers'             => ['nullable', 'array'],
            'lecturers.*'           => ['exists:users,id', new \App\Rules\UserHasRole('lecturer')],
            'teaching_assistants'   => ['nullable', 'array'],
            'teaching_assistants.*' => ['exists:users,id', new \App\Rules\UserHasStudentType('teaching_assistant')],
            'students'              => ['nullable', 'array'],
            'students.*'            => ['exists:users,id', new \App\Rules\UserHasStudentType('regular')],
        ];
    }
}
