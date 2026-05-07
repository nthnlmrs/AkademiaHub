@section('title', 'Lab Classes')

<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Lab Classes</h2>
                <p class="text-slate-500 mt-1">Manage laboratory classes with lecturers and TAs</p>
            </div>
            <a href="{{ route('admin_lab.classes.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Lab Class
            </a>
        </div>

        <div class="glass-card p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Class or course name...">
                </div>
                <div class="w-48">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-secondary">Filter</button>
            </form>
        </div>

        <div class="glass-card overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Class</th>
                        <th>Mode</th>
                        <th>Room</th>
                        <th>Lecturer</th>
                        <th>Teaching Assistants</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($labClasses as $cr)
                        <tr>
                            <td>
                                <span class="font-mono text-xs text-slate-500">{{ $cr->course->code }}</span>
                                <p class="font-medium">{{ $cr->course->name }}</p>
                            </td>
                            <td><span class="text-lg font-bold text-cyan-400">{{ $cr->name }}</span></td>
                            <td><span class="badge {{ $cr->mode === 'online' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($cr->mode) }}</span></td>
                            <td class="text-slate-600">{{ $cr->room ?: '-' }}</td>
                            <td class="text-sm text-slate-600">
                                {{ $cr->lecturers->pluck('name')->join(', ') ?: '-' }}
                            </td>
                            <td class="text-sm">
                                @foreach($cr->teachingAssistants as $ta)
                                    <div class="text-cyan-400">
                                        {{ $ta->name }}
                                        <span class="text-xs text-slate-500">({{ $ta->ta_id }})</span>
                                    </div>
                                @endforeach
                                @if($cr->teachingAssistants->isEmpty())
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="text-sm text-slate-600">{{ $cr->students->count() }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin_lab.classes.edit', $cr) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin_lab.classes.destroy', $cr) }}" onsubmit="return confirm('Delete this lab class?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-500">No lab classes found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">{{ $labClasses->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
