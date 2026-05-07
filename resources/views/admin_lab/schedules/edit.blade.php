@section('title', 'Edit Lab Schedule')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin_lab.schedules.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Lab Schedules</a>
            <h2 class="text-2xl font-bold mt-2">Edit Lab Schedule</h2>
        </div>

        <!-- Conflict Warning -->
        @if(session('conflict_details'))
            <div class="alert alert-error mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-semibold text-red-400">Schedule Conflicts Detected!</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-2">The following students have conflicting schedules:</p>
                    <ul class="space-y-1">
                        @foreach(session('conflict_details') as $detail)
                            <li class="text-sm text-red-300 flex items-start gap-2">
                                <span class="text-red-400 mt-0.5">✕</span>
                                {{ $detail }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp

        <div class="glass-card p-6">
            <form method="POST" action="{{ route('admin_lab.schedules.update', $schedule) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="form-label">LAB Class</label>
                    <select name="class_room_id" class="form-select" required>
                        @foreach($labClasses as $cr)
                            <option value="{{ $cr->id }}" {{ old('class_room_id', $schedule->class_room_id) == $cr->id ? 'selected' : '' }}>
                                {{ $cr->course->code }} - {{ $cr->course->name }} (Class {{ $cr->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Day of Week</label>
                    <select name="day_of_week" class="form-select" required>
                        @foreach($days as $i => $day)
                            <option value="{{ $i }}" {{ old('day_of_week', $schedule->day_of_week) == $i ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}" class="form-input" required>
                    </div>
                </div>

                <div>
                    <label class="form-label">Room (optional)</label>
                    <input type="text" name="room" value="{{ old('room', $schedule->room) }}" class="form-input">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Update Schedule</button>
                    <a href="{{ route('admin_lab.schedules.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
