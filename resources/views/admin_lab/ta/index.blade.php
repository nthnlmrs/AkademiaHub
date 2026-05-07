@section('title', 'Manage Teaching Assistants')

<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Manage Teaching Assistants</h2>
                <p class="text-slate-500 mt-1">Promote students to TA or manage existing TAs</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="glass-card p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Name, email, NIM, or TA ID...">
                </div>
                <div class="w-48">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Students</option>
                        <option value="teaching_assistant" {{ request('type') === 'teaching_assistant' ? 'selected' : '' }}>Teaching Assistants</option>
                        <option value="regular" {{ request('type') === 'regular' ? 'selected' : '' }}>Regular Students</option>
                    </select>
                </div>
                <button type="submit" class="btn-secondary">Filter</button>
                @if(request()->hasAny(['search', 'type']))
                    <a href="{{ route('admin_lab.ta.index') }}" class="btn-secondary">Clear</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="glass-card overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>NIM</th>
                        <th>TA ID</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td class="font-medium">{{ $student->name }}</td>
                            <td><span class="font-mono text-sm text-slate-500">{{ $student->nim_nip }}</span></td>
                            <td>
                                @if($student->ta_id)
                                    <span class="font-mono text-sm text-cyan-400 font-semibold">{{ $student->ta_id }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="text-slate-600">{{ $student->email }}</td>
                            <td>
                                @if($student->isTeachingAssistant())
                                    <span class="badge badge-info">Teaching Assistant</span>
                                @else
                                    <span class="badge badge-success">Regular Student</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($student->isTeachingAssistant())
                                        <a href="{{ route('admin_lab.ta.edit_id', $student) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">Edit ID</a>
                                        <form method="POST" action="{{ route('admin_lab.ta.demote', $student) }}" onsubmit="return confirm('Demote {{ $student->name }} back to Regular Student?')">
                                            @csrf
                                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Demote</button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin_lab.ta.promote', $student) }}" class="btn-primary text-xs py-1 px-3">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                            Promote to TA
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-500">No students found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">{{ $students->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
