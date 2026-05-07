@section('title', 'Edit Schedule')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.schedules.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Schedules</a>
            <h2 class="text-2xl font-bold mt-2">Edit Schedule</h2>
        </div>

        @php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp

        <div class="glass-card p-6">
            <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="form-label">Class</label>
                    <select name="class_room_id" class="form-select" required>
                        @foreach($classRooms as $cr)
                            <option value="{{ $cr->id }}" {{ old('class_room_id', $schedule->class_room_id) == $cr->id ? 'selected' : '' }}>
                                {{ $cr->course->code }} - {{ $cr->course->name }} (Class {{ $cr->name }} - {{ $cr->type }})
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
                    <label class="form-label">Room (optional override)</label>
                    <input type="text" name="room" value="{{ old('room', $schedule->room) }}" class="form-input">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Update Schedule</button>
                    <a href="{{ route('admin.schedules.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
