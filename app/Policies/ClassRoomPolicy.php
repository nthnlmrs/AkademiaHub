<?php

namespace App\Policies;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClassRoomPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClassRoom $classRoom): bool
    {
        if ($user->isAdmin()) return true;

        return $classRoom->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user is a lecturer for the model.
     */
    public function manage(User $user, ClassRoom $classRoom): bool
    {
        if ($user->isAdmin()) return true;

        return $classRoom->lecturers()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user is a lecturer or TA for the model.
     */
    public function assist(User $user, ClassRoom $classRoom): bool
    {
        if ($user->isAdmin()) return true;

        return $classRoom->users()
            ->where('users.id', $user->id)
            ->whereIn('role', ['lecturer', 'student']) // TA is a student type
            ->where(function($q) {
                $q->where('role', 'lecturer')
                  ->orWhere('student_type', 'teaching_assistant');
            })
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClassRoom $classRoom): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClassRoom $classRoom): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClassRoom $classRoom): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClassRoom $classRoom): bool
    {
        return false;
    }
}
