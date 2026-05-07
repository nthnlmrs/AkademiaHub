@section('title', 'Manage Users')

<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Manage Users</h2>
                <p class="text-slate-500 mt-1">Create and manage lecturer and student accounts</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add User
            </a>
        </div>

        <!-- Filters -->
        <div class="glass-card p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Name, email, or ID...">
                </div>
                <div class="w-40">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin_lab" {{ request('role') === 'admin_lab' ? 'selected' : '' }}>Admin Lab</option>
                        <option value="lecturer" {{ request('role') === 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                    </select>
                </div>
                <button type="submit" class="btn-secondary">Filter</button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">Clear</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="glass-card overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>ID</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="font-medium">{{ $user->name }}</td>
                            <td class="text-slate-600">{{ $user->email }}</td>
                            <td><span class="font-mono text-sm text-slate-500">{{ $user->nim_nip }}</span></td>
                            <td>
                                @if($user->role === 'admin_lab')
                                    <span class="badge badge-warning">Admin Lab</span>
                                @elseif($user->role === 'lecturer')
                                    <span class="badge badge-primary">Lecturer</span>
                                @elseif($user->student_type === 'teaching_assistant')
                                    <span class="badge badge-info">Teaching Assistant</span>
                                @else
                                    <span class="badge badge-success">Student</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-slate-500">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
