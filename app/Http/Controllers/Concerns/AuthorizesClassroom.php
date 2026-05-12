<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ClassRoom;
use Illuminate\Support\Facades\Auth;

/**
 * Reusable classroom access-check methods.
 *
 * Eliminates repeated:
 *   if (!$user->isAdmin() && !$classroom->users->contains($user->id)) { abort(403); }
 */
trait AuthorizesClassroom
{
    /**
     * Abort with 403 if the authenticated user is neither an admin
     * nor enrolled in the given classroom.
     */
    protected function authorizeClassroomAccess(ClassRoom $classroom, string $message = 'You are not enrolled in this class.'): void
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$classroom->users->contains($user->id)) {
            abort(403, $message);
        }
    }

    /**
     * Abort with 403 if the authenticated user cannot *manage* the classroom
     * (i.e. is not admin/lecturer of this class).
     */
    protected function authorizeClassroomManagement(ClassRoom $classroom, string $message = 'Only lecturers of this class can manage this resource.'): void
    {
        if (!Auth::user()->can('manage', $classroom)) {
            abort(403, $message);
        }
    }
}
