<?php

namespace App\Http\Controllers\AdminLab;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TAController extends Controller
{
    /**
     * List all students - show who is TA and who is regular.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'student');

        if ($request->filled('type')) {
            $query->where('student_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim_nip', 'like', "%{$search}%")
                  ->orWhere('ta_id', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('student_type', 'desc')->orderBy('name')->paginate(15);

        return view('admin_lab.ta.index', compact('students'));
    }

    /**
     * Show the form to promote a regular student to TA.
     */
    public function promote(User $user)
    {
        if (!$user->isStudent()) {
            abort(403, 'Only students can be promoted to Teaching Assistant.');
        }

        if ($user->isTeachingAssistant()) {
            return redirect()->route('admin_lab.ta.index')
                ->with('success', 'This student is already a Teaching Assistant.');
        }

        return view('admin_lab.ta.promote', compact('user'));
    }

    /**
     * Execute the promotion - switch student to TA with TA ID.
     */
    public function executePromotion(Request $request, User $user)
    {
        if (!$user->isStudent()) {
            abort(403, 'Only students can be promoted to Teaching Assistant.');
        }

        $validated = $request->validate([
            'ta_id' => ['required', 'string', 'max:20', Rule::unique('users', 'ta_id')->ignore($user->id)],
        ]);

        $user->update([
            'student_type' => 'teaching_assistant',
            'ta_id' => $validated['ta_id'],
        ]);

        return redirect()->route('admin_lab.ta.index')
            ->with('success', $user->name . ' has been promoted to Teaching Assistant with ID: ' . $validated['ta_id']);
    }

    /**
     * Demote a TA back to regular student.
     */
    public function demote(User $user)
    {
        if (!$user->isTeachingAssistant()) {
            abort(403, 'This user is not a Teaching Assistant.');
        }

        $user->update([
            'student_type' => 'regular',
            'ta_id' => null,
        ]);

        return redirect()->route('admin_lab.ta.index')
            ->with('success', $user->name . ' has been demoted to Regular Student.');
    }

    /**
     * Edit TA ID of an existing TA.
     */
    public function editTaId(User $user)
    {
        if (!$user->isTeachingAssistant()) {
            abort(403, 'This user is not a Teaching Assistant.');
        }

        return view('admin_lab.ta.edit_ta_id', compact('user'));
    }

    /**
     * Update TA ID.
     */
    public function updateTaId(Request $request, User $user)
    {
        if (!$user->isTeachingAssistant()) {
            abort(403);
        }

        $validated = $request->validate([
            'ta_id' => ['required', 'string', 'max:20', Rule::unique('users', 'ta_id')->ignore($user->id)],
        ]);

        $user->update(['ta_id' => $validated['ta_id']]);

        return redirect()->route('admin_lab.ta.index')
            ->with('success', 'TA ID updated to ' . $validated['ta_id']);
    }
}
