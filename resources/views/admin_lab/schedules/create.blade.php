@section('title', 'Create Lab Schedule')

<x-app-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin_lab.schedules.index') }}" class="text-sm text-slate-500 hover:text-indigo-400 transition-colors">← Back to Lab Schedules</a>
            <h2 class="text-2xl font-bold mt-2">Create Lab Schedule</h2>
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
            <form method="POST" action="{{ route('admin_lab.schedules.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="form-label">LAB Class</label>
                    <select name="class_room_id" class="form-select" required>
                        <option value="">Select LAB Class</option>
                        @foreach($labClasses as $cr)
                            <option value="{{ $cr->id }}" {{ old('class_room_id') == $cr->id ? 'selected' : '' }}>
                                {{ $cr->course->code }} - {{ $cr->course->name }} (Class {{ $cr->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('class_room_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Day of Week</label>
                    <select name="day_of_week" class="form-select" required>
                        @foreach($days as $i => $day)
                            <option value="{{ $i }}" {{ old('day_of_week') == $i ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}" class="form-input" required>
                        @error('end_time')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Room (optional)</label>
                    <input type="text" name="room" value="{{ old('room') }}" class="form-input" placeholder="Lab Room 201">
                </div>

                <!-- Info box -->
                <div class="bg-cyan-500/10 border border-cyan-500/20 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-cyan-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-sm text-cyan-300">
                            <p class="font-semibold mb-1">Automatic Conflict Detection</p>
                            <p class="text-cyan-400/70">The system will automatically check if any enrolled student or TA has a conflicting schedule on the same day and time. If a conflict is found, the schedule will not be created.</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Create Schedule</button>
                    <a href="{{ route('admin_lab.schedules.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
