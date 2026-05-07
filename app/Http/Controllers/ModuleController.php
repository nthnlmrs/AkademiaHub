<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Module;
use App\Models\CourseSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ModuleController extends Controller
{
    public function store(Request $request, ClassRoom $classroom, CourseSession $session)
    {
        $user = Auth::user();

        // Only lecturers/TAs/admin can upload
        if ($user->isStudent() && !$user->isTeachingAssistant()) {
            abort(403, 'You do not have permission to upload modules.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:51200'], // 50MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('modules/' . $classroom->id . '/' . $session->id, 'public');

        Module::create([
            'course_session_id' => $session->id,
            'title' => $request->title,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Module uploaded successfully.');
    }

    public function download(Module $module)
    {
        $user = Auth::user();

        // Since CourseSession is now attached to Course, we check if the user is enrolled in any classroom of that course
        $hasAccess = $user->isAdmin();
        if (!$hasAccess) {
            $hasAccess = $module->courseSession->course->classRooms()
                ->whereHas('users', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->exists();
        }

        if (!$hasAccess) {
            abort(403, 'You do not have access to this file.');
        }

        return Storage::disk('public')->download($module->file_path, $module->file_name);
    }

    public function destroy(Module $module)
    {
        $user = Auth::user();

        // Find the classroom this module belongs to, based on the course session
        // Note: CourseSession is now under Course, so we get the classroom by querying. Wait, the module might belong to a session, which belongs to a course, and many classrooms could be using it.
        // Actually, if modules belong to CourseSession, and the user is a lecturer for ANY classroom of that course, they might have access, OR just admin/owner.
        // Let's allow if user is admin, owner, or lecturer of a class that uses this session's course.
        $hasPermission = $user->isAdmin() || $module->uploaded_by === $user->id;

        if (!$hasPermission) {
            $isCourseLecturer = $module->courseSession->course->classRooms()
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id)->where('role', 'lecturer');
                })->exists();

            if ($isCourseLecturer) {
                $hasPermission = true;
            }
        }

        if (!$hasPermission) {
            abort(403, 'You do not have permission to delete this module.');
        }

        Storage::disk('public')->delete($module->file_path);
        $module->delete();

        return redirect()->back()->with('success', 'Module deleted successfully.');
    }
}
