@section('title', 'Lab Schedules')

<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Lab Schedules</h2>
                <p class="text-slate-500 mt-1">Manage LAB class schedules with conflict detection</p>
            </div>
            <a href="{{ route('admin_lab.schedules.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Lab Schedule
            </a>
        </div>

        @php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin_lab.schedules.index') }}" class="day-pill {{ !request()->filled('day') ? 'active' : '' }}">All Days</a>
            @foreach($days as $i => $day)
                <a href="{{ route('admin_lab.schedules.index', ['day' => $i]) }}" class="day-pill {{ request('day') === (string)$i ? 'active' : '' }}">{{ $day }}</a>
            @endforeach
        </div>

        <div class="glass-card overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Course</th>
                        <th>Class</th>
                        <th>Lecturer</th>
                        <th>TA</th>
                        <th>Room</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td><span class="font-medium text-indigo-400">{{ $schedule->day_name }}</span></td>
                            <td>
                                <span class="font-mono">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</span>
                                <span class="text-slate-500 mx-1">-</span>
                                <span class="font-mono">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                            </td>
                            <td class="font-medium">{{ $schedule->classRoom->course->name }}</td>
                            <td><span class="badge badge-info">{{ $schedule->classRoom->name }} (LAB)</span></td>
                            <td class="text-sm text-slate-600">
                                {{ $schedule->classRoom->lecturers->pluck('name')->join(', ') ?: '-' }}
                            </td>
                            <td class="text-sm text-cyan-400">
                                @foreach($schedule->classRoom->teachingAssistants as $ta)
                                    <div>{{ $ta->name }} <span class="text-xs text-slate-500">({{ $ta->ta_id }})</span></div>
                                @endforeach
                                @if($schedule->classRoom->teachingAssistants->isEmpty())
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="text-slate-600">{{ $schedule->room ?: $schedule->classRoom->room ?: '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin_lab.schedules.edit', $schedule) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin_lab.schedules.destroy', $schedule) }}" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-500">No lab schedules found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">{{ $schedules->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
