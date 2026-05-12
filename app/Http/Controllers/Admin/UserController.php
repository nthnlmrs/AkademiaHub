<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->userRules());

        $this->normalizeStudentType($validated);
        $validated['password']          = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->abortIfAdmin($user);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->abortIfAdmin($user);

        $validated = $request->validate($this->userRules($user->id));

        $this->normalizeStudentType($validated);

        if ($request->filled('password')) {
            $request->validate(['password' => ['string', 'min:8']]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->abortIfAdmin($user, 'Cannot delete admin user.');

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Shared validation rules for User store/update.
     * Pass $ignoreId to skip unique checks for the current user (update).
     */
    private function userRules(?int $ignoreId = null): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($ignoreId)],
            'role'         => ['required', Rule::in(['admin_lab', 'lecturer', 'student'])],
            'student_type' => ['required_if:role,student', Rule::in(['regular', 'teaching_assistant'])],
            'nim_nip'      => ['required', 'string', 'max:50', Rule::unique('users', 'nim_nip')->ignore($ignoreId)],
            // 'password' is only required on store; update handles it separately
            ...($ignoreId === null ? ['password' => ['required', 'string', 'min:8']] : []),
        ];
    }

    /** Ensure non-student roles always get student_type = 'regular'. */
    private function normalizeStudentType(array &$validated): void
    {
        if ($validated['role'] !== 'student') {
            $validated['student_type'] = 'regular';
        }
    }

    /** Abort 403 if the given user is an admin. */
    private function abortIfAdmin(User $user, string $message = 'Cannot edit admin user.'): void
    {
        if ($user->isAdmin()) {
            abort(403, $message);
        }
    }
}
