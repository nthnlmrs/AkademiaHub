@section('title', 'Manage Courses')

<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Manage Courses</h2>
                <p class="text-slate-500 mt-1">Create and manage courses</p>
            </div>
            <a href="{{ route('admin.courses.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Course
            </a>
        </div>

        <div class="glass-card p-4">
            <form method="GET" class="flex gap-3 items-end">
                <div class="flex-1">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Course name or code...">
                </div>
                <button type="submit" class="btn-secondary">Search</button>
            </form>
        </div>

        <div class="glass-card overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th class="text-center">Credits</th>
                        <th class="text-center">Classes</th>
                        <th class="text-center">Sessions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td><span class="font-mono text-indigo-400">{{ $course->code }}</span></td>
                            <td>
                                <a href="{{ route('admin.courses.show', $course) }}" class="font-semibold text-slate-700 hover:text-indigo-600 transition-colors">
                                    {{ $course->name }}
                                </a>
                            </td>
                            <td class="text-center"><span class="font-bold text-slate-600">{{ $course->credits }}</span> <span class="text-xs text-slate-400">SKS</span></td>
                            <td class="text-center"><span class="badge badge-primary">{{ $course->class_rooms_count }}</span></td>
                            <td class="text-center"><span class="badge badge-secondary">{{ $course->course_sessions_count ?? 0 }}</span></td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.courses.show', $course) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">Sessions</a>
                                    <span class="text-slate-200">|</span>
                                    <a href="{{ route('admin.courses.edit', $course) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course and all related classes?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-500">No courses found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">{{ $courses->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
